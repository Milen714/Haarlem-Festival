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

class OrderController extends BaseController
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

    public function addToCart(array $params = []): void
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
}