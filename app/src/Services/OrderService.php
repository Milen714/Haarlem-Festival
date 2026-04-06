<?php

namespace App\Services;

use App\Models\Enums\OrderStatus;
use App\Models\Enums\TicketSchemeEnum;
use App\Models\Payment\Order;
use App\Models\User;
use App\Models\Payment\OrderItem;
use App\Repositories\Interfaces\IOrderRepository;
use App\Repositories\OrderRepository;
use App\Services\Interfaces\IOrderService;
use App\Exceptions\ValidationException;

class OrderService implements IOrderService
{
    private IOrderRepository $orderRepository;
    private TicketService $ticketService;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->ticketService   = new TicketService();
    }
    public function createOrder(Order $order): bool
    {
        return $this->orderRepository->createOrder($order);
    }

    public function getOrderById(int $orderId): ?Order
    {
        return $this->orderRepository->getOrderById($orderId);
    }

    public function getOrdersByUserId(int $userId): array
    {
        return $this->orderRepository->getOrdersByUserId($userId);
    }
    public function getOpenOrderByUserId(int $userId, ?array $statuses = null): ?Order
    {
        $order = $this->orderRepository->getOpenOrderByUserId($userId, $statuses);
        if ($order === null) {
            return null;
        }

        foreach ($order->orderItems as $item) {
            $item->sessionOrderitem_id = array_search($item, $order->orderItems) + 1;
        }
        return $order;
    }

    public function updateOrderStatus(int $orderId, OrderStatus $status, ?string $pdf = null): bool
    {
        return $this->orderRepository->updateOrderStatus($orderId, $status, $pdf);
    }

    public function addOrderItem(OrderItem $orderItem): bool
    {
        return $this->orderRepository->addOrderItem($orderItem);
    }
    public function updateOrderItemQuantity(OrderItem $orderItem): bool
    {
        return $this->orderRepository->updateOrderItemQuantity($orderItem);
    }

    public function getOrderItemsByOrderId(int $orderId): array
    {
        return $this->orderRepository->getOrderItemsByOrderId($orderId);
    }

    public function getPaidTicketsByUser(int $userId, ?string $date = null): array
    {
        return $this->orderRepository->getPaidTicketsByUser($userId, $date);
    }

    public function createSessionCart(): Order
    {
        $order = new Order();
        $order->generateReferenceNumber();
        $order->order_date = new \DateTime();
        $order->status = OrderStatus::In_Cart;
        if(!isset($_SESSION['session_cart'])){
            $_SESSION['session_cart'] = $order;
        }
        return $order;
    }
    public function getSessionCart(): ?Order
    {
        $cart = $_SESSION['session_cart'] ?? null;
        if ($cart){
            $this->assignSessionOrderItemIds($cart);
        }
        return $cart;
    }
    public function clearSessionCart(): void
    {
        unset($_SESSION['session_cart']);
    }
    private function assignSessionOrderItemIds(Order $cart): void
    {
        foreach ($cart->orderItems as $index => $item) {
            $item->sessionOrderitem_id = $index;
        }
    }

    // Soft availability check then adds to session cart. Persisted carts also hard-lock the seat immediately.
    public function addOrderItemToSessionCart(OrderItem $item): void
    {
        $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;

        if ($ticketTypeId !== null) {
            $available = $this->ticketService->getAvailableCapacity($ticketTypeId);

            // Also count seats already in the cart for this ticket type so the
            // soft check accounts for items the user already has in their session.
            $alreadyInCart = 0;
            $cart = $this->getSessionCart();
            if ($cart !== null) {
                foreach ($cart->orderItems as $existing) {
                    if (($existing->ticket_type?->ticket_type_id ?? null) === $ticketTypeId) {
                        $alreadyInCart += (int) $existing->quantity;
                    }
                }
            }

            if (($item->quantity + $alreadyInCart) > $available) {
                $remaining = max(0, $available - $alreadyInCart);
                throw new ValidationException(
                    "Only {$remaining} seat(s) remaining for this ticket type."
                );
            }
        }

        if (!isset($cart)) {
            $cart = $this->getSessionCart();
        }
        if ($cart === null) {
            $cart = $this->createSessionCart();
        }
        
        // // If this is the first item AND user is logged in AND not yet persisted, create order in DB
        $isFirstItem = empty($cart->orderItems);
        $userIsLoggedIn = isset($_SESSION['loggedInUser']);
        if ($isFirstItem && $userIsLoggedIn && $cart->order_id === null) {
            $cart->user = $_SESSION['loggedInUser'];
            $cart->order_date = new \DateTime();
            $cart->status = OrderStatus::In_Cart;
            $cart->calculateTotals();
            $this->orderRepository->createOrder($cart);  // sets $cart->order_id
        }
        
        $cart->orderItems[] = $item;
        $this->assignSessionOrderItemIds($cart);
        $cart->calculateTotals();
        
        if($cart->order_id !== null){
            // Cart is already in the DB — lock the seat now
            $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;
            if ($ticketTypeId !== null) {
                $reserveQty    = (int)$item->quantity;
                $schemeEnumObj = $item->ticket_type?->ticket_scheme?->scheme_enum ?? null;
                if ($schemeEnumObj === TicketSchemeEnum::HISTORY_FAMILY_TICKET) {
                    $reserveQty *= 4;
                }
                $reserved = $this->ticketService->reserveSeats($ticketTypeId, $reserveQty);
                if (!$reserved) {
                    throw new ValidationException(
                        "Ticket type {$ticketTypeId} is sold out or has insufficient capacity."
                    );
                }
                $this->ticketService->syncHistoryScheduleSoldOut($ticketTypeId);
            }
            array_last($cart->orderItems)->order_id = $cart->order_id;
            $this->orderRepository->addOrderItem($item);
            $this->orderRepository->updateOrderTotals($cart);
        }
        $this->hydrateSessionCart($cart);
    }
    // Saves the session cart to the DB and returns the new order ID.
    public function persistSessionCart(Order $order, User $user, bool $ticketsAlreadyLocked = false): Order
    {
        $order->user = $user;
        $order->order_id = null;
        $order->order_date = new \DateTime();
        $order->status = OrderStatus::In_Cart;
        $order->calculateTotals();

        if (!$ticketsAlreadyLocked) {
            // Build items array and reserve all seats in a single transaction.
            // For pass-type tickets, expand to all sibling ticket types sharing the same scheme
            // so that 1 pass purchase deducts 1 slot from every schedule for that pass.
            $items = [];
            foreach ($order->orderItems as $item) {
                $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;
                $schemeEnum   = $item->ticket_type?->ticket_scheme?->scheme_enum ?? null;
                $schemeId     = $item->ticket_type?->ticket_scheme?->ticket_scheme_id ?? null;
                $quantity     = (int)$item->quantity;

                if ($ticketTypeId === null) {
                    continue;
                }

                if ($schemeEnum !== null && TicketSchemeEnum::isPassType($schemeEnum) && $schemeId !== null) {
                    $siblingIds = $this->ticketService->getTicketTypeIdsBySchemeId($schemeId);
                    foreach ($siblingIds as $siblingId) {
                        $items[] = ['ticket_type_id' => (int)$siblingId, 'quantity' => $quantity];
                    }
                } else {
                    $capacityQty = $quantity;
                    if ($schemeEnum === TicketSchemeEnum::HISTORY_FAMILY_TICKET) {
                        $capacityQty *= 4;
                    }
                    $items[] = ['ticket_type_id' => $ticketTypeId, 'quantity' => $capacityQty];
                }
            }

            if (!empty($items) && !$this->ticketService->reserveMultiple($items)) {
                throw new ValidationException(
                    'One or more ticket types are sold out or have insufficient capacity. Please review your cart.'
                );
            }
        }

        $this->orderRepository->createOrder($order);  // sets $order->order_id

        foreach ($order->orderItems as $item) {
            $item->orderitem_id = null;
            $item->order_id = $order->order_id;
            $this->orderRepository->addOrderItem($item);
        }

        if ($order->order_id === null) {
            throw new \RuntimeException('Failed to persist session cart: missing order ID after insert.');
        }

        return $order;
    }
    public function hydrateSessionCart(Order $order): void
    {
        $_SESSION['session_cart'] = $order;
    }
    public function getOrderByStripeCheckoutSessionId(string $sessionId): ?Order
    {
        return $this->orderRepository->getOrderByStripeCheckoutSessionId($sessionId);
    }

    public function setStripeCheckoutSessionId(int $orderId, string $sessionId): bool
    {
        return $this->orderRepository->setStripeCheckoutSessionId($orderId, $sessionId);
    }

    public function hydrateSessionCartFormDbOnLogin(User $user): void{
        $openStatuses = [OrderStatus::In_Cart, OrderStatus::Pending_Payment];
        $dbCart = $this->getOpenOrderByUserId($user->id, $openStatuses);
        $sessionCart = $this->getSessionCart();
        $sessionCartHasItems = $sessionCart !== null && count($sessionCart->orderItems) > 0;

        if ($sessionCartHasItems) {
            // Cancel all currently open orders before persisting the session cart as the new active one.
            while ($dbCart !== null) {
                $cancelled = $this->updateOrderStatus($dbCart->order_id, OrderStatus::Cancelled);
                if (!$cancelled) {
                    throw new \RuntimeException("Failed to cancel open order ID {$dbCart->order_id}.");
                }
                $dbCart = $this->getOpenOrderByUserId($user->id, $openStatuses);
            }

            $persistedOrder = $this->persistSessionCart($sessionCart, $user);;
            if ($persistedOrder !== null) {
                $this->hydrateSessionCart($persistedOrder);
            } else {
                $this->hydrateSessionCart($sessionCart);
            }
            return;
        }

        if ($dbCart !== null) {
            $this->hydrateSessionCart($dbCart);
        }
    }
    public function getOrderItemFromCartBySessionItemId(Order $cart, int $sessionOrderItemId): ?OrderItem
    {
        foreach ($cart->orderItems as $item) {
            if ($item->sessionOrderitem_id === $sessionOrderItemId) {
                return $item;
            }
        }
        return null;
    }
    public function removeOrderItemFromSessionCart(int $orderItemId): void
    {
        $cart = $this->getSessionCart();
        if ($cart === null) {
            throw new \RuntimeException('No session cart found when trying to remove order item.');
        }
        $itemToRemove = $this->getOrderItemFromCartBySessionItemId($cart, $orderItemId);
        
        array_splice($cart->orderItems, $itemToRemove->sessionOrderitem_id, 1);
        
        $this->assignSessionOrderItemIds($cart);
        $cart->calculateTotals();
        $this->hydrateSessionCart($cart);
        if ($cart->order_id !== null && $itemToRemove->orderitem_id !== null) {
            $this->ticketService->releaseOrderItems([$itemToRemove]);
            $this->orderRepository->removeOrderItem($itemToRemove->orderitem_id);
            $this->orderRepository->updateOrderTotals($cart);
        }
        
    }
    public function updateOrderItemInSessionCart(int $sessionOrderItemId, int $newQuantity): void
    {
        $cart = $this->getSessionCart();
        if ($cart === null) {
            throw new \RuntimeException('No session cart found when trying to update order item.');
        }
        /**
         * @var OrderItem $itemToUpdate
         */
        $itemToUpdate = $this->getOrderItemFromCartBySessionItemId($cart, $sessionOrderItemId);
        if ($itemToUpdate === null) {
            throw new \RuntimeException("No order item found in cart with sessionOrderitem_id {$sessionOrderItemId}.");
        }
        $itemToUpdate->calculateTotalPriceWithNewQuantity($newQuantity);

        $cart->calculateTotals();
        $this->hydrateSessionCart($cart);

        if ($cart->order_id !== null && $itemToUpdate->orderitem_id !== null) {
            $this->orderRepository->updateOrderItemQuantity($itemToUpdate);
            $this->orderRepository->updateOrderTotals($cart);
        }
    }

    public function generateTicketHashes(int $orderId): void
    {
        $items = $this->orderRepository->getOrderItemsByOrderId($orderId);

        foreach ($items as $item) {
            $uniqueHash = bin2hex(random_bytes(8));

            $this->orderRepository->updateItemHash($item->orderitem_id, $uniqueHash);
        }
    }

    public function getOrderItemByHash(string $hash): ?OrderItem
    {
        return $this->orderRepository->getOrderItemByHash($hash);
    }

    public function markAsScanned(int $orderItemId): bool
    {
        return $this->orderRepository->markAsScanned($orderItemId);
    }

    public function getPaidOrderItemsByUserId(int $userId): array
    {
        $orders = $this->getOrdersByUserId($userId);
        $paidItems = [];

        foreach ($orders as $order) {
            if ($order->status === OrderStatus::Fulfilled) {
                $items = $this->getOrderItemsByOrderId($order->order_id);
                $paidItems = array_merge($paidItems, $items);
            }
        }

        return $paidItems;
    }
        public function getOrdersWhereStatusIn(array $statuses): array
        {
            return $this->orderRepository->getOrdersWhereStatusIn($statuses);
        }

        public function canUserDownloadOrderTickets(User $user, Order $order): bool
        {
            return $user->id === $order->user->id || $user->role === \App\Models\Enums\UserRole::ADMIN;
        }

        public function authorizeOrderOwnership(User $user, Order $order, callable $onUnauthorized): bool
        {
            if ($user->id !== $order->user->id && $user->role !== \App\Models\Enums\UserRole::ADMIN) {
                $onUnauthorized();
                return false;
            }
            return true;
        }

        public function getAllowedExportColumns(): array
        {
            return $this->orderRepository->getAllowedExportColumns();
        }
        public function getAllOrdersForExport(array $requestedColumns, ?string $paidAfter = null): array
        {
            return $this->orderRepository->getAllOrdersForExport($requestedColumns, $paidAfter);
        }

        /**
         * Generate and download/save a CSV file from order data
         * 
         * Converts array of associative arrays into a properly formatted CSV with UTF-8 BOM
         * for Excel compatibility. Supports dynamic column selection and optional file saving.
         * 
         * @param array $data Array of associative arrays containing order data
         * @param string $filename Filename without extension (will add .csv)
         * @param array $requestedColumns Array of column names to include in export (if empty, includes all)
         * @param bool $download Whether to send file as download (default: true)
         * @param bool $save Whether to save file to server (default: false)
         * @param string $savePath Path to save file when $save is true (default: 'Assets/documents/')
         * 
         * @return bool True if successful
         * 
         * @throws ValidationException If $data is not an array, is empty, or $requestedColumns is not an array
         * @throws \Exception If memory stream creation or file operations fail
         */
        function generateCSV($data, $filename, $requestedColumns = [], $download = true, $save = false, $savePath = 'Assets/documents/')
        {
            try {
                // Ensure $data is an array
                if (!is_array($data)) {
                    throw new ValidationException('Data must be an array. Received: ' . gettype($data));
                }

                // Validate data is not empty
                if (empty($data)) {
                    throw new ValidationException('No data provided for CSV export');
                }

                // Validate requestedColumns is an array
                if (!is_array($requestedColumns)) {
                    throw new ValidationException('Requested columns must be an array. Received: ' . gettype($requestedColumns));
                }

                // Ensure data format - already arrays from repository
                // No conversion needed; data from getAllOrdersForExport is already arrays
                $csvData = $data;

                // Filter to only requested columns if provided
                if (!empty($requestedColumns)) {
                    $csvData = array_map(function($row) use ($requestedColumns) {
                        $filtered = [];
                        foreach ($requestedColumns as $col) {
                            $filtered[$col] = $row[$col] ?? '';
                        }
                        return $filtered;
                    }, $csvData);
                }

                // Create CSV in memory
                $output = fopen('php://memory', 'r+');
                if (!$output) {
                    throw new \Exception('Failed to create memory stream');
                }

                // Write header row
                $headers = array_keys($csvData[0]);
                fputcsv($output, $headers, ',', '"', '\\');

                // Write data rows
                foreach($csvData as $row){
                    fputcsv($output, array_values($row), ',', '"', '\\');
                }

                // Get CSV content
                rewind($output);
                $csvContent = stream_get_contents($output);
                fclose($output);

                // Add UTF-8 BOM for Excel compatibility (tells Excel to use comma delimiter)
                $csvContent = "\xEF\xBB\xBF" . $csvContent;

                // Save to server if requested
                if($save){
                    if (!is_dir($savePath)) {
                        mkdir($savePath, 0755, true);
                    }
                    file_put_contents($savePath . $filename . '.csv', $csvContent);
                }

                // Download as CSV file
                if($download){
                    // Clear any previous output/buffering before sending headers
                    while (ob_get_level() > 0) {
                        ob_end_clean();
                    }
                    
                    // Set headers for file download
                    header('Content-Type: text/csv; charset=utf-8');
                    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
                    header('Content-Length: ' . strlen($csvContent));
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    header('Connection: close');
                    
                    echo $csvContent;
                    exit;
                }

                return true;
            } catch (\Exception $e) {
                throw new \Exception("CSV generation failed: " . $e->getMessage());
            }
        }

        /**
         * Generate and download/save an Excel file from order data using HTML table format
         * 
         * Converts array of associative arrays into a formatted HTML table with inline styles
         * that Excel recognizes. Includes colored headers, alternating row colors, and borders.
         * Supports dynamic column selection and optional file saving.
         * 
         * @param array $data Array of associative arrays containing order data
         * @param string $filename Filename without extension (will add .xls)
         * @param array $requestedColumns Array of column names to include in export (if empty, includes all)
         * @param bool $download Whether to send file as download (default: true)
         * @param bool $save Whether to save file to server (default: false)
         * @param string $savePath Path to save file when $save is true (default: 'Assets/documents/')
         * 
         * @return bool True if successful
         * 
         * @throws ValidationException If $data is not an array or is empty
         * @throws \Exception If file operations fail
         */
        public function generateExcelViaHtml($data, $filename, $requestedColumns = [], $download = true, $save = false, $savePath = 'Assets/documents/')
        {
            try {
                // Ensure data is an array
                if (!is_array($data)) {
                    throw new ValidationException('Data must be an array. Received: ' . gettype($data));
                }

                // Validate data is not empty
                if (empty($data)) {
                    throw new ValidationException('No data provided for Excel export');
                }

                // Ensure data is array of associative arrays
                $htmlData = $data;

                // Filter to only requested columns if provided
                if (!empty($requestedColumns)) {
                    $htmlData = array_map(function($row) use ($requestedColumns) {
                        $filtered = [];
                        foreach ($requestedColumns as $col) {
                            $filtered[$col] = $row[$col] ?? '';
                        }
                        return $filtered;
                    }, $htmlData);
                }

                // Get headers from first row
                $headers = array_keys($htmlData[0]);

                // Build HTML table with inline styles for Excel compatibility
                $html = '<!DOCTYPE html>
                    <html>
                        <head>
                            <meta charset="UTF-8">
                        </head>
                        <body>
                            <table style="border-collapse: collapse; width: 100%;">';

                // Write header row with inline styles
                $html .= '<tr>';
                foreach ($headers as $header) {
                    $html .= '<th style="background-color: #4472C4; color: white; padding: 12px; border: 1px solid #ddd; font-weight: bold; text-align: left;">' 
                        . htmlspecialchars($header) . '</th>';
                }
                $html .= '</tr>';

                // Add data rows with alternating colors
                $rowCount = 0;
                foreach ($htmlData as $row) {
                    $bgColor = ($rowCount % 2 === 0) ? '#ffffff' : '#f9f9f9';
                    $html .= '<tr style="background-color: ' . $bgColor . ';">';
                    foreach ($row as $value) {
                        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' 
                            . htmlspecialchars($value ?? '') . '</td>';
                    }
                    $html .= '</tr>';
                    $rowCount++;
                }
                $html .= '</table></body></html>';

                // Save to server if requested
                if ($save) {
                    if (!is_dir($savePath)) {
                        mkdir($savePath, 0755, true);
                    }
                    file_put_contents($savePath . $filename . '.html', $html);
                }

                // Download as Excel file
                if ($download) {
                    // Clear any previous output/buffering before sending headers
                    while (ob_get_level() > 0) {
                        ob_end_clean();
                    }

                    // Set headers for Excel download
                    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    header('Cache-Control: no-cache, no-store, must-revalidate');

                    echo $html;
                    exit;
                }

                return true;

            } catch (\Exception $e) {
                throw new \Exception("Excel generation failed: " . $e->getMessage());
            }
        }
}