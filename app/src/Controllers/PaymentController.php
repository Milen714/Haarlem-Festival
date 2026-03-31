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
use App\Services\MailService;
use App\Services\Interfaces\IMailService;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Services\TicketFulfillmentService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;
use DateTime;

class PaymentController extends BaseController
{

    private ITicketService $ticketService;
    private IPaymentService $paymentService;
    private IOrderService $orderService;
    private IMailService $mailService;
    private ITicketFulfillmentService $ticketFulfillmentService;
    private ILogService $logService;

    public function __construct()
    {
        $this->ticketService = new TicketService();
        $this->paymentService = new PaymentService();
        $this->orderService = new OrderService();
        $this->mailService = new MailService();
        $this->ticketFulfillmentService = new TicketFulfillmentService();
        $this->logService = new LogService();
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
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function personalProgram(){
        $userId = isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : null;
        if (!$userId) {
            //should show error
            $this->notFound();
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
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function checkout(array $params = [])
    {
        $order=$this->orderService->getSessionCart();
        $viewModel = new ShoppingCartViewModel($order);
        $this->view('ShoppingCart/PaymentPartial', ['viewModel' => $viewModel]);
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
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
            $this->logService->exception('Payment', $e);
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while creating the checkout session.']);
        }
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function return(array $params = [])
    {
        try {
            $order = $this->orderService->getOrderByStripeCheckoutSessionId($_GET['session_id'] ?? '');
            if ($order === null) {
                throw new \Exception('No active cart found.');
            }
            $viewModel = new ShoppingCartViewModel($order);
            $this->view('ShoppingCart/CheckoutSuccess', ['viewModel' => $viewModel]);
        } catch (\Throwable $e) {
            error_log('Error loading checkout success page: ' . $e->getMessage());
            // Optionally, you could redirect to an error page or show a user-friendly message here.
        }
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function status(array $params = [])
    {
        header('Content-Type: application/json');
        try {
            $jsonString = file_get_contents('php://input');
            $jsonData   = json_decode($jsonString, true);
            $sessionId  = $jsonData['session_id'] ?? null;

            $data = $this->paymentService->stripeCheckoutStatus($jsonData);
            $order = null;
            $ticketReady = false;
            $orderStatus = null;

            // If Stripe confirms payment, update the order in DB and clear the session cart.
            // This handles the case where the webhook fires after the redirect (race condition).
            if (
                ($data['status'] ?? '')          === 'complete' &&
                ($data['payment_status'] ?? '')  === 'paid'     &&
                $sessionId !== null
            ) {
                $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);

                $this->orderService->clearSessionCart();
            }

            if ($order === null && $sessionId !== null) {
                $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            }

            if ($order !== null) {
                $orderStatus = $order->status->value;
                if (!empty($order->ticket_pdf_path)) {
                    $pdfPath = __DIR__ . '/../../public/Assets/documents/' . $order->ticket_pdf_path;
                    $ticketReady = file_exists($pdfPath);
                }
            }

            $data['ticket_ready'] = $ticketReady;
            $data['order_status'] = $orderStatus;

            http_response_code(200);
            echo json_encode($data);

        } catch (\Exception $e) {
            $this->logService->exception('Payment', $e);
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
    
}