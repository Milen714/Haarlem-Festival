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
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserFacingException;

/**
 * PaymentController
 * 
 * Handles payment processing and checkout flow.
 * Manages Stripe integration, payment status tracking, ticket fulfillment, and order return pages.
 */
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

    /**
     * Display shopping cart page
     */
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

    /**
     * Display user's personal program (paid tickets)
     */
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

    /**
     * Display checkout page with payment form
     */
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

    /**
     * Create Stripe checkout session for the current cart
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function createCheckoutSession(array $params = [])
    {
        try {
            $order = $this->orderService->getSessionCart();

            if ($order === null || empty($order->orderItems)) {
                throw new ValidationException('No active cart found.');
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
        } catch (UserFacingException $e) {
            $this->logService->exception('Payment', new \Exception($e->getMessage()));
            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Throwable $e) {
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while creating the checkout session.', 500);
        }
    }

    /**
     * Display payment success page with ticket download option. Authorizes order ownership.
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function return(array $params = [])
    {
        try {
            $order = $this->orderService->getOrderByStripeCheckoutSessionId($_GET['session_id'] ?? '');
            if ($order === null) {
                throw new ResourceNotFoundException('No active cart found.');
            }

            $loggedInUser = $this->getLoggedInUser();
            if (!$this->orderService->authorizeOrderOwnership($loggedInUser, $order, function() {
                throw new UnauthorizedException('You do not have permission to access this order.');
            })) {
                return;
            }

            $viewModel = new ShoppingCartViewModel($order);
            $this->view('ShoppingCart/CheckoutSuccess', ['viewModel' => $viewModel]);
        } catch (UnauthorizedException $e) {
            $this->logService->info('Payment', 'Unauthorized access attempt: ' . $e->getMessage());
            $this->forbidden();
        } catch (UserFacingException $e) {
            $this->logService->exception('Payment', new \Exception($e->getMessage()));
            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Throwable $e) {
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while processing the return.', 500);
        }
    }

    /**
     * Check Stripe checkout session status and payment completion. Authorizes order ownership.
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function status(array $params = [])
    {
        try {
            $jsonData = $this->getPostData();
            $sessionId = $jsonData['session_id'] ?? null;

            if (!$sessionId) {
                throw new ValidationException('session_id is required.');
            }

            $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            if ($order) {
                $loggedInUser = $this->getLoggedInUser();
                if (!$this->orderService->authorizeOrderOwnership($loggedInUser, $order, function() {
                    throw new UnauthorizedException('You do not have permission to access this order.');
                })) {
                    return;
                }
            }

            $data = $this->paymentService->stripeCheckoutStatus($jsonData);

            if (
                ($data['status'] ?? '')         === 'complete' &&
                ($data['payment_status'] ?? '') === 'paid'     &&
                $sessionId !== null
            ) {
                $this->orderService->clearSessionCart();
            }

            $this->sendSuccessResponse($data, 200);
        } catch (UserFacingException $e) {
            $this->logService->info('Payment', 'User-facing error: ' . $e->getMessage());
            $code = $e instanceof UnauthorizedException ? 403 : 400;
            $this->sendErrorResponse($e->getMessage(), $code);
        } catch (\Exception $e) {
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while checking the payment status.', 500);
        }
    }

    /**
     * Check if ticket PDF is ready for download. Authorizes order ownership.
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function ticketReady(array $params = [])
    {
        try {
            $jsonData  = $this->getPostData();
            $sessionId = $jsonData['session_id'] ?? null;

            if (!$sessionId) {
                throw new ValidationException('session_id is required.');
            }

            $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            if (!$order) {
                throw new ResourceNotFoundException('Order not found.');
            }

            $loggedInUser = $this->getLoggedInUser();
            if (!$this->orderService->authorizeOrderOwnership($loggedInUser, $order, function() {
                throw new UnauthorizedException('You do not have permission to access this order.');
            })) {
                return;
            }

            $ticketReady = $this->ticketFulfillmentService->isTicketPdfReady($order->ticket_pdf_path ?? '');

            $this->sendSuccessResponse(['ticket_ready' => $ticketReady], 200);
        } catch (UserFacingException $e) {
            $this->logService->info('Payment', 'User-facing error: ' . $e->getMessage());
            $code = $e instanceof ResourceNotFoundException ? 404 : ($e instanceof UnauthorizedException ? 403 : 400);
            $this->sendErrorResponse($e->getMessage(), $code);
        } catch (\Throwable $e) {
            $this->logService->exception('Payment', $e);
            $this->sendErrorResponse('An error occurred while checking ticket readiness.', 500);
        }
    }

    /**
     * Display order details page
     */
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