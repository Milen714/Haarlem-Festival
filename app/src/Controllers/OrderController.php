<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Services\TicketFulfillmentService;
use App\Models\Payment\OrderItem;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UserFacingException;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;

/**
 * OrderController
 * 
 * Handles shopping cart operations and ticket management.
 * Manages adding/removing items from cart, updating quantities, ticket downloads, and order history.
 */
class OrderController extends BaseController
{
    private ITicketService $ticketService;
    private IOrderService $orderService;
    private ITicketFulfillmentService $ticketFulfillmentService;
    private ILogService $logService;

    public function __construct()
    {
        $this->ticketService            = new TicketService();
        $this->orderService             = new OrderService();
        $this->ticketFulfillmentService = new TicketFulfillmentService();
        $this->logService               = new LogService();
    }

    /**
     * Add a ticket to the session cart
     */
    public function addToCart(array $params = []): void
    {
        try {
            $jsonData = $this->getPostData();
            if (!$jsonData) {
                throw new ValidationException('Invalid JSON input');
            }

            $ticketType = $this->ticketService->getTicketTypeById($jsonData['ticketTypeId']);
            if (!$ticketType) {
                throw new ResourceNotFoundException('Ticket type not found');
            }
            $quantity = $jsonData['quantity'];
            $schemeEnum = $ticketType->ticket_scheme?->scheme_enum?->value ?? '';
            if (str_starts_with($schemeEnum, 'HISTORY_')) {
                $quantity = $this->resolvePersonCount($quantity, $schemeEnum);
            }
            $orderItem = (new OrderItem())->createOrderItemFromTicketType($quantity, $ticketType);
            $this->orderService->addOrderItemToSessionCart($orderItem);
            $cart = $this->orderService->getSessionCart();
            $this->sendSuccessResponse([
                'success' => true,
                'cart'    => $cart,
            ], 200);
        } catch (UserFacingException $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * Get the current number of items in the cart
     */
    public function getNumberOfCartItems(array $params = []): void
    {
        try {
            $cart = $this->orderService->getSessionCart();
            if (!$cart) {
                $this->sendSuccessResponse(['success' => true, 'numberOfItems' => 0], 200);
                return;
            }
            $this->sendSuccessResponse([
                'success'       => true,
                'numberOfItems' => count($cart->orderItems),
            ], 200);
        } catch (\Throwable $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove an item from the cart
     */
    public function removeOrderItemFromCart(array $params = []): void
    {
        try {
            $jsonData = $this->getPostData();
            if (!$jsonData) {
                throw new ValidationException('Invalid JSON input');
            }

            $this->orderService->removeOrderItemFromSessionCart($jsonData['sessionOrderitem_id']);
            $this->sendSuccessResponse(['success' => true], 200);
        } catch (UserFacingException $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * Get order item data for quantity update
     */
    public function getOrderItemDataForUpdate(array $params = []): void
    {
        try {
            $sessionOrderItemId = $_GET['sessionOrderitem_id'] ?? null;
            $cart = $this->orderService->getSessionCart();

            $item = $this->orderService->getOrderItemFromCartBySessionItemId($cart, $sessionOrderItemId);
            if (!$item) {
                throw new ResourceNotFoundException('Order item not found in cart');
            }
            $this->sendSuccessResponse([
                'success' => true,
                'data'    => ['orderItem' => $item],
            ], 200);
        } catch (UserFacingException $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * Update the quantity of an item in the cart
     */
    public function updateOrderItemInCart(array $params = []): void
    {
        try {
            $jsonData = $this->getPostData();
            if (!$jsonData) {
                throw new ValidationException('Invalid JSON input');
            }
            $sessionOrderItemId = $jsonData['sessionOrderitem_id'] ?? null;
            $newQuantity        = $jsonData['quantity'] ?? null;
            if ($sessionOrderItemId === null || $newQuantity === null) {
                throw new ValidationException('Missing required fields: sessionOrderitem_id and quantity');
            }

            $this->orderService->updateOrderItemInSessionCart($sessionOrderItemId, $newQuantity);
            $this->sendSuccessResponse(['success' => true], 200);
        } catch (UserFacingException $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }
    /**
     * Download ticket PDF for an order. Authorizes ownership before serving file.
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function downloadTickets(array $params = []): void
    {
        $sessionId = $_GET['session_id'] ?? null;

        try {
            $order = $this->orderService->getOrderByStripeCheckoutSessionId($sessionId);
            if (!$order) {
                throw new ResourceNotFoundException('Order not found for this checkout session.');
            }

            $loggedInUser = $this->getLoggedInUser();
            if (!$this->orderService->canUserDownloadOrderTickets($loggedInUser, $order)) {
                throw new \App\Exceptions\UnauthorizedException('You do not have permission to access these tickets.');
            }

            $ticketPdfPath = $this->ticketFulfillmentService->validateAndGetTicketPdf($order->ticket_pdf_path);
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
        } catch (ResourceNotFoundException $e) {
            http_response_code(404);
            echo $e->getMessage();
        } catch (\App\Exceptions\UnauthorizedException $e) {
            http_response_code(403);
            echo $e->getMessage();
        } catch (\Throwable $e) {
            http_response_code(500);
            echo 'An error occurred while processing your request.';
        }
    }

    /**
     * Summary of getOrderColumns
     * @param array $params
     * @return void
     */
    public function getOrderColumns(array $params = []): void
    {
        try {
            $columns = $this->orderService->getAllowedExportColumns();
            $this->sendSuccessResponse([
                'success' => true,
                'columns' => $columns,
            ], 200);
        } catch (\Throwable $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }
    public function exportOrders(array $params = []): void
    {
        try {
            $jsonData = $this->getPostData();
            if (!$jsonData) {
                throw new ValidationException('Invalid JSON input');
            }
            $requestedColumns = $jsonData['columns'] ?? [];
            $paidAfter       = $jsonData['paidAfter'] ?? null;

            if (empty($requestedColumns)) {
                throw new ValidationException('No columns specified for export');
            }

            $ordersData = $this->orderService->getAllOrdersForExport($requestedColumns, $paidAfter);
            
            if (!is_array($ordersData)) {
                throw new \Exception('Export data must be an array');
            }
            
            $filename = 'orders_export_CSV_' . date('Ymd_His');

            $this->orderService->generateCSV($ordersData, $filename, $requestedColumns, true, false);
        } catch (ValidationException $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (UserFacingException $e) {
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            $this->logService->error("CSV Export error: " . $e->getMessage(), $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Summary of exportOrdersExcel
     * @param array $params
     * @throws ValidationException
     * @throws \Exception
     * @return void
     */
    public function exportOrdersExcel(array $params = []): void
    {
        try {
            $jsonData = $this->getPostData();
            if (!$jsonData) {
                throw new ValidationException('Invalid JSON input');
            }
            $requestedColumns = $jsonData['columns'] ?? [];
            $paidAfter       = $jsonData['paidAfter'] ?? null;

            if (empty($requestedColumns)) {
                throw new ValidationException('No columns specified for export');
            }

            $ordersData = $this->orderService->getAllOrdersForExport($requestedColumns, $paidAfter);
            
            if (!is_array($ordersData)) {
                throw new \Exception('Export data must be an array');
            }
            
            $filename = 'orders_export_' . date('Ymd_His');

            $this->orderService->generateExcelViaHtml($ordersData, $filename, $requestedColumns, true, false);
        } catch (\Throwable $e) {
            $this->logService->error("CSV Export error: " . $e->getMessage(), $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->sendSuccessResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolvePersonCount(int $quantity, string $schemeEnum): int
    {
        if ($schemeEnum === 'HISTORY_FAMILY_TICKET') {
            return $quantity * 4;
        }
        return $quantity;
    }
}