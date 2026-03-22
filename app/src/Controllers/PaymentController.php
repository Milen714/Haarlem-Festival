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
        if(!isset($order)){
            $order = $this->orderService->createSessionCart();
        }
        $viewModel = new ShoppingCartViewModel($order);
        $this->view('ShoppingCart/ShoppingCart', ['viewModel' => $viewModel]);
    }

    public function personalProgram(){
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            //should show error
            header('Location: /login');
            exit;
        }

        $tickets = $this->orderService->getPaidTicketsByUser($userId);
        //for each events
        foreach ($tickets as $ticket) {
            $ticket['title'] = $ticket['artist_name']
                    ?? $ticket['restaurant_name']
                    ?? $ticket['landmark_name']
                    ?? 'Event';
            $ticket['ticket_image'] = $ticket['artist_media_file_path']
                    ?? $ticket['restaurant_media_file_path']
                    ?? $ticket['landmark_media_file_path']
                    ?? $ticket['venue_media_file_path'] 
                    ?? $ticket['magic_media_file_path'];    
            $ticket['alt_text'] = $ticket['artist_media_alt_text']
                    ?? $ticket['restaurant_media_alt_text']
                    ?? $ticket['landmark_media_alt_text']
                    ?? $ticket['venue_media_alt_text'] 
                    ?? $ticket['magic_media_alt_text'];   
        }

        $this->view('ShoppingCart/wishlist', [
            'tickets' => $tickets
        ]);
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER])]
    public function checkout(array $params = [])
    {
        $order=$this->orderService->getSessionCart();
        $viewModel = new ShoppingCartViewModel($order);
        $this->view('ShoppingCart/PaymentPartial', ['viewModel' => $viewModel]);
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER])]
    public function createCheckoutSession(array $params = [])
    {
        try {
            $order = $this->orderService->getSessionCart();

            if ($order === null || empty($order->orderItems)) {
                http_response_code(400);
                echo json_encode(['error' => 'No active cart found.']);
                return;
            }

            // If the cart hasn't been persisted to DB yet (user added items after login),
            // persist it now so we can link the Stripe session to an order.
            if ($order->order_id === null) {
                $user = $_SESSION['loggedInUser'] ?? null;
                if ($user !== null) {
                    $orderId = $this->orderService->persistSessionCart($order, $user);
                    $order   = $this->orderService->getOrderById($orderId);
                    $this->orderService->hydrateSessionCart($order);
                }
            }

            $order->calculateTotals();
            $item = [
                'name'     => 'Haarlem Festival Ticket/s',
                'amount'   => (int)round($order->total * 100),
                'quantity' => 1,
            ];
            $stripeSession = $this->paymentService->stripeCheckout((object)$item);

            if ($order->order_id !== null) {
                $this->orderService->setStripeCheckoutSessionId($order->order_id, $stripeSession->id);
                $this->orderService->updateOrderStatus($order->order_id, OrderStatus::Pending_Payment);
            }

            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['clientSecret' => $stripeSession->client_secret]);
        } catch (\Throwable $e) {
            error_log('Error creating checkout session: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while creating the checkout session.']);
        }
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER])]
    public function return(array $params = [])
    {
        $sessionId = $_GET['session_id'] ?? null;
        if ($sessionId !== null) {
            $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            if ($order !== null && $order->status === OrderStatus::Paid) {
                $this->orderService->clearSessionCart();
            }
        }
        $this->view('ShoppingCart/CheckoutSuccess');
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER])]
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
            echo json_encode(['error' => 'An error occurred while checking the payment status.' . $e->getMessage()]);
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