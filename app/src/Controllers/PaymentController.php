<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Models\Enums\OrderStatus;
use App\Middleware\RequireRole;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\IOrderService;
use App\Services\PaymentService;
use App\Services\OrderService;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
use DateTime;

class PaymentController extends BaseController
{
    
    private ITicketService $ticketService;
    private IPaymentService $paymentService;
    private IOrderService $orderService;
    public function __construct()
    {
        $this->ticketService = new TicketService();
        $this->paymentService = new PaymentService();
        $this->orderService = new OrderService();
    }

    public function index(array $params = [])
    {
        //$order=$this->orderService->getOrderById(2);
        $order= $this->orderService->getSessionCart();
        $viewModel = new ShoppingCartViewModel($order);
        $this->view('ShoppingCart/ShoppingCart', ['viewModel' => $viewModel]);
    }
    public function checkout(array $params = [])
    {
        $order=$this->orderService->getSessionCart();
        $viewModel = new ShoppingCartViewModel($order);
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
        //$order=$this->orderService->getOrderById(1);
        $order=$this->orderService->getSessionCart();
        $viewModel = new ShoppingCartViewModel($order);
        $this->view('ShoppingCart/DetailsCheckout', ['viewModel' => $viewModel]);
    }
    public function test(array $params = [])
    {
         header('Content-Type: application/json');
         $sessionCart = $this->orderService->getSessionCart();
         $user = isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser'] : new User();
          $this->orderService->persistSessionCart($sessionCart, $user);
          $this->orderService->hydrateSessionCart($sessionCart);

         //$viewModel = new ShoppingCartViewModel($order);
        
        // echo json_encode($viewModel, JSON_PRETTY_PRINT);   
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