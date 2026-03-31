<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Models\Enums\OrderStatus;
use App\Middleware\RequireRole;
use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\IOrderService;
use App\Services\PaymentService;
use App\Services\OrderService;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Services\TicketFulfillmentService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;

class PaymentController extends BaseController
{
    private IPaymentService $paymentService;
    private IOrderService $orderService;
    private ITicketFulfillmentService $ticketFulfillmentService;
    private ILogService $logService;

    public function __construct()
    {
        $this->paymentService           = new PaymentService();
        $this->orderService             = new OrderService();
        $this->ticketFulfillmentService = new TicketFulfillmentService();
        $this->logService               = new LogService();
    }

    public function index(array $params = [])
    {
        try{
            $order = $this->orderService->getSessionCart();
            if (!isset($order)) {
                $order = $this->orderService->createSessionCart();
            }
            $viewModel = new ShoppingCartViewModel($order);
            $this->view('ShoppingCart/ShoppingCart', ['viewModel' => $viewModel]);
        } catch (\Throwable $e) {
            $this->logService->exception('ShoppingCartIndex', $e);
            $this->view('ShoppingCart/ShoppingCart', ['viewModel' => null, 'error' => 'An error occurred while loading your shopping cart. Please try again later.']);
        }
    }

    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function personalProgram()
    {
        try {
            $userId = $this->getLoggedInUser()?->id;
            if (!$userId) {
                $this->notFound();
                return;
            }

            $tickets = $this->orderService->getPaidTicketsByUser($userId);
            $this->view('ShoppingCart/wishlist', ['tickets' => $tickets]);
        } catch (\Throwable $e) {
            $this->logService->exception('PersonalProgram', $e);
            $this->view('ShoppingCart/wishlist', ['tickets' => [], 'error' => 'An error occurred while loading your personal program. Please try again later.']);
        }
    }

    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function checkout(array $params = [])
    {   
        try {
            $order     = $this->orderService->getSessionCart();
            $viewModel = new ShoppingCartViewModel($order);
            $this->view('ShoppingCart/PaymentPartial', ['viewModel' => $viewModel]);
        } catch (\Throwable $e) {
            $this->logService->exception('Checkout', $e);
            $this->view('ShoppingCart/Checkout', ['viewModel' => null, 'error' => 'An error occurred while loading the checkout page. Please try again later.']);
        }
    }

    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function createCheckoutSession(array $params = [])
    {
        try {
            $order = $this->orderService->getSessionCart();

            if ($order === null || empty($order->orderItems)) {
                $this->sendErrorResponse('No active cart found.', 400);
                return;
            }

            if ($order->order_id === null) {
                $user = $this->getLoggedInUser();
                if ($user !== null) {
                    $order   = $this->orderService->persistSessionCart($order, $user);
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

            $this->sendSuccessResponse(['clientSecret' => $stripeSession->client_secret], 200);
        } catch (\Throwable $e) {
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while creating the checkout session.', 500);
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
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while processing the return.', 500);
        }
    }

    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function status(array $params = [])
    {
        try {
            $jsonData = $this->getPostData();
            $data     = $this->paymentService->stripeCheckoutStatus($jsonData);
            $sessionId = $jsonData['session_id'] ?? null;

            if (
                ($data['status'] ?? '')         === 'complete' &&
                ($data['payment_status'] ?? '') === 'paid'     &&
                $sessionId !== null
            ) {
                $this->orderService->clearSessionCart();
            }

            $this->sendSuccessResponse($data, 200);
        } catch (\Exception $e) {
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while checking the payment status.', 500);
        }
    }

    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function ticketReady(array $params = [])
    {
        try {
            $jsonData  = $this->getPostData();
            $sessionId = $jsonData['session_id'] ?? null;

            if (!$sessionId) {
                $this->sendErrorResponse('session_id is required.', 400);
                return;
            }

            $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            if (!$order) {
                $this->sendErrorResponse('Order not found.', 404);
                return;
            }

            $ticketReady = $this->ticketFulfillmentService->isTicketPdfReady($order->ticket_pdf_path ?? '');

            $this->sendSuccessResponse(['ticket_ready' => $ticketReady], 200);
        } catch (\Throwable $e) {
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while checking ticket readiness.', 500);
        }
    }

    public function details(array $params = [])
    {
        try {
            $order     = $this->orderService->getSessionCart();
            $viewModel = new ShoppingCartViewModel($order);
            $this->view('ShoppingCart/DetailsCheckout', ['viewModel' => $viewModel]);
        } catch (\Throwable $e) {
            $this->logService->exception('PaymentDetails', $e);
            $this->view('ShoppingCart/Details', ['viewModel' => null, 'error' => 'An error occurred while loading your order details. Please try again later.']);
        }
    }
}