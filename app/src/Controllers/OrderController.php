<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Services\TicketFulfillmentService;
use App\Models\Payment\OrderItem;

class OrderController extends BaseController
{
    private ITicketService $ticketService;
    private IOrderService $orderService;
    private ITicketFulfillmentService $ticketFulfillmentService;

    public function __construct()
    {
        $this->ticketService            = new TicketService();
        $this->orderService             = new OrderService();
        $this->ticketFulfillmentService = new TicketFulfillmentService();
    }

    public function addToCart(array $params = []): void
    {
        try {
            $jsonData = $this->getPostData();
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
                'cart'    => $cart,
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
                $this->jsonResponse(['success' => true, 'numberOfItems' => 0], 200);
                return;
            }
            $this->jsonResponse([
                'success'       => true,
                'numberOfItems' => count($cart->orderItems),
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
        try {
            $jsonData = $this->getPostData();
            if (!$jsonData) {
                throw new \Exception('Invalid JSON input');
            }

            $this->orderService->removeOrderItemFromSessionCart($jsonData['sessionOrderitem_id']);
            $this->jsonResponse(['success' => true], 200);
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
                'data'    => ['orderItem' => $item],
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
        try {
            $jsonData = $this->getPostData();
            if (!$jsonData) {
                throw new \Exception('Invalid JSON input');
            }
            $sessionOrderItemId = $jsonData['sessionOrderitem_id'] ?? null;
            $newQuantity        = $jsonData['quantity'] ?? null;
            if ($sessionOrderItemId === null || $newQuantity === null) {
                throw new \Exception('Missing required fields: sessionOrderitem_id and quantity');
            }

            $this->orderService->updateOrderItemInSessionCart($sessionOrderItemId, $newQuantity);
            $this->jsonResponse(['success' => true], 200);
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
        $user = $this->getLoggedInUser();

        if (!$user?->id) {
            $this->redirect('/login');
        }

        $orderItems = $this->orderService->getPaidOrderItemsByUserId($user->id);

        $this->view('Orders/my-tickets', [
            'orderItems' => $orderItems,
        ]);
    }

    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function downloadTickets(array $params = []): void
    {
        $pdfName   = $_GET['ticket_name'] ?? null;
        $sessionId = $_GET['session_id']  ?? null;

        try {
            $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            if (!$order) {
                http_response_code(404);
                echo 'Order not found for this checkout session.';
                return;
            }

            // Ownership check — only the owning user or an ADMIN may download
            $loggedInUser = $this->getLoggedInUser();
            if (
                $loggedInUser?->role !== UserRole::ADMIN &&
                ($order->user_id ?? null) !== ($loggedInUser?->id ?? null)
            ) {
                $this->forbidden();
                return;
            }

            if (!$order->ticket_pdf_path) {
                http_response_code(409);
                echo 'Your tickets are still being generated. Please wait a few seconds and try again.';
                return;
            }

            if (!$this->ticketFulfillmentService->isTicketPdfReady($order->ticket_pdf_path)) {
                http_response_code(409);
                echo 'Your tickets are still being prepared. Please retry shortly.';
                return;
            }

            $ticketPdfPath = $this->ticketFulfillmentService->getTicketPdfPath($order->ticket_pdf_path);
            $fileName      = basename($ticketPdfPath);
            $fileSize      = filesize($ticketPdfPath);

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . $fileSize);
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($ticketPdfPath);
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo 'Error: ' . $e->getMessage();
        }
    }
}
