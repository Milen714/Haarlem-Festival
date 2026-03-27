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
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;
use Stripe\ApiOperations\Update;
use App\Services\MailService;
use App\Services\Interfaces\IMailService;
use FontLib\Table\Type\head;

class OrderController extends BaseController
{
    private ITicketService $ticketService;
    private IPaymentService $paymentService;
    private IOrderService $orderService;
    private IMailService $mailService;
    public function __construct()
    {
        $this->ticketService = new TicketService();
        $this->paymentService = new PaymentService();
        $this->orderService = new OrderService();
        $this->mailService = new MailService();
    }

    public function addToCart(array $params = []): void
    {   /// begone
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
            $this->jsonResponse([
                'success' => true,
                'cart' => $cart
            ], 200);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getNumberOfCartItems(array $params = []): void
    {
        try {
            $cart = $this->orderService->getSessionCart();
            if (!$cart) {
                $this->jsonResponse([
                    'success' => true,
                    'numberOfItems' => 0
                ], 200);
                return;
            }
            $numberOfItems = count($cart->orderItems);
            $this->jsonResponse([
                'success' => true,
                'numberOfItems' => $numberOfItems
            ], 200);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function removeOrderItemFromCart(array $params = []): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            if (!$jsonData) {
                throw new \Exception('Invalid JSON input');
            }

            $this->orderService->removeOrderItemFromSessionCart($jsonData['sessionOrderitem_id']);
            //$cart = $this->orderService->getSessionCart();
            echo json_encode([
                'success' => true
            ], JSON_PRETTY_PRINT);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getOrderItemDataForUpdate(array $params = []): void
    {
        try {
            $sessionOrderItemId = $_GET['sessionOrderitem_id'] ?? null;
            $cart = $this->orderService->getSessionCart();

            $item = $this->orderService->getOrderItemFromCartBySessionItemId($cart, $sessionOrderItemId);
            if (!$item) {
                throw new \Exception('Order item not found in cart');
            }
            $this->jsonResponse([
                'success' => true,
                'data' => ['orderItem' => $item]
            ], 200);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateOrderItemInCart(array $params = []): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            if (!$jsonData) {
                throw new \Exception('Invalid JSON input');
            }
            $sessionOrderItemId = $jsonData['sessionOrderitem_id'] ?? null;
            $newQuantity = $jsonData['quantity'] ?? null;
            if ($sessionOrderItemId === null || $newQuantity === null) {
                throw new \Exception('Missing required fields: sessionOrderitem_id and quantity');
            }

            $this->orderService->updateOrderItemInSessionCart($sessionOrderItemId, $newQuantity);

            $this->jsonResponse([
                'success' => true
            ], 200);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function showUserTickets(): void
    {
        /** @var \App\Models\User $user */
        $user = $_SESSION['loggedInUser'];

        if (!$user->id) {
            header('Location: /login');
            exit;
        }

        $this->orderService->generateTicketHashes(69);

        $orderItems = $this->orderService->getPaidOrderItemsByUserId($user->id);

        $this->view('Orders/my-tickets', [
            'orderItems' => $orderItems
        ]);
    }
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function downloadTickets(array $params = []): void
    {
        $pdfName = $_GET['ticket_name'] ?? null;
        $sessionId = $_GET['session_id'] ?? null;
        try {
            $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            if (!$order) {
                http_response_code(404);
                echo 'Order not found for this checkout session.';
                return;
            }

            if (!$order->ticket_pdf_path) {
                http_response_code(409);
                echo 'Your tickets are still being generated. Please wait a few seconds and try again.';
                return;
            }

            $ticketPdfPath = __DIR__ . '/../../public/Assets/documents/' . $order->ticket_pdf_path;

            // 1. Check if file exists
            if (!file_exists($ticketPdfPath)) {
                http_response_code(409);
                echo 'Your tickets are still being prepared. Please retry shortly.';
                return;
            }

            // 2. Get file info
            $fileName = basename($ticketPdfPath);
            $fileSize = filesize($ticketPdfPath);

            // 3. Set HTTP headers BEFORE any output
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . $fileSize);
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            // 4. Send file to browser
            readfile($ticketPdfPath);
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
    }
}
