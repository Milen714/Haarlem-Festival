<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\ScheduleService;
use App\Repositories\UserRepository;
use App\Repositories\PageRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\VenueRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\RestaurantRepository;
use App\Services\ArtistService;
use App\Services\VenueService;
use App\Services\PageService;
use App\Services\LandmarkService;
use App\Services\RestaurantService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Models\Enums\OrderStatus;
use App\Middleware\RequireRole;
use App\Repositories\MediaRepository;
use App\Services\MediaService;
use App\ViewModels\Home\ScheduleList;
use App\ViewModels\Home\StartingPoints;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\IOrderService;
use App\Services\PaymentService;
use App\Services\OrderService;
use App\config\Secrets;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
class PaymentController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private PageService $pageService;
    private PageRepository $pageRepository;
    private LandmarkService $landmarkService;
    private MediaService $mediaService;
    private MediaRepository $mediaRepository;
    private ScheduleRepository $scheduleRepository;
    private ScheduleService $scheduleService;
    private VenueRepository $venueRepository;
    private VenueService $venueService;
    private ITicketService $ticketService;
    private IPaymentService $paymentService;
    private IOrderService $orderService;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);

        $this->pageRepository = new PageRepository();
        $this->pageService = new PageService($this->pageRepository);

        $this->mediaRepository = new MediaRepository();
        $this->mediaService = new MediaService($this->mediaRepository);

        $this->venueRepository = new VenueRepository();
        $this->venueService = new VenueService($this->venueRepository, $this->mediaService);

        $artistService = new ArtistService(new ArtistRepository(), $this->mediaService);
        $restaurantService = new RestaurantService(new RestaurantRepository(), $this->mediaService);
        $this->landmarkService = new LandmarkService();

        $this->scheduleRepository = new ScheduleRepository();
        $this->scheduleService = new ScheduleService(
            $this->scheduleRepository,
            $this->venueService,
            $artistService,
            $restaurantService,
            $this->landmarkService
        );
        $this->ticketService = new TicketService();
        $this->paymentService = new PaymentService();
        $this->orderService = new OrderService();
    }

    public function index(array $params = [])
    {
        //$order=$this->orderService->getOrderById(2);
        $order= $this->orderService->createSessionCart();
        $viewModel = new ShoppingCartViewModel($this->orderService->getOrderById(1));
        $this->view('ShoppingCart/ShoppingCart', ['viewModel' => $viewModel]);
    }
    public function checkout(array $params = [])
    {
        $order=$this->orderService->getSessionCart();
        $viewModel = new ShoppingCartViewModel($this->orderService->getOrderById(1));
        $this->view('ShoppingCart/PaymentPartial', ['viewModel' => $viewModel]);
    }

    public function createCheckoutSession(array $params = [])
    {
       try {
        $item = [
            'name' => 'Test Product',
            'amount' => 100 * 100, // amount in cents
            'quantity' => 1,
        ];
        $this->paymentService->stripeCheckout((object)$item);
        } catch (\Exception $e) {
             error_log('Error creating checkout session: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while creating the checkout session.']);
        }
        //require '../payment/checkout.php';  
    }
    public function return(array $params = [])
    {
        $this->view('ShoppingCart/CheckoutSuccess');
        
    }
    public function status(array $params = [])
    {
        header('Content-Type: application/json');
        try{
            $jsonString = file_get_contents('php://input');
            $jsonData = json_decode($jsonString, true);
            $this->paymentService->stripeCheckoutStatus($jsonData);

        }catch (\Exception $e) {
             error_log('Error checking payment status: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while checking the payment status.']);
        }
    }
    public function details(array $params = [])
    {
        $order=$this->orderService->getOrderById(1);
        $viewModel = new ShoppingCartViewModel($order);
        $this->view('ShoppingCart/DetailsCheckout', ['viewModel' => $viewModel]);
    }
    public function test(array $params = [])
    {
         header('Content-Type: application/json');
         //$order=$this->orderService->getOpenOrderByUserId(1);
         $order=$_SESSION['session_cart'] ?? null;
         if (!$order) {
            echo json_encode(['message' => 'No open order found for user.']);
            return;
         }

         $viewModel = new ShoppingCartViewModel($order);
        
         echo json_encode($viewModel, JSON_PRETTY_PRINT);   
        //$this->view('ShoppingCart/WishlistMain', ['viewModel' => null]);
    }

    public function createTestOrder(array $params = []): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $jsonData = json_decode(file_get_contents('php://input'), true);
             if (!$jsonData) {
                throw new \Exception('Invalid JSON input');
            }
            
            $ticketType = $this->ticketService->getTicketTypeById($jsonData['ticketTypeId']);
             if (!$ticketType) {
                throw new \Exception('Ticket type not found');
            }
            $orderItem = (new OrderItem())->createOrderItemFromTicketType($jsonData['quantity'], $ticketType);
            $this->orderService->addOrderItemToSessionCart($orderItem);
            $cart = $this->orderService->getSessionCart();
            echo json_encode([
                'success' => true,
                'cart' => $cart
            ], JSON_PRETTY_PRINT);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }




}