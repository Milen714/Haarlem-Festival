<?php

namespace App\Repositories;

use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
use App\Models\Enums\OrderStatus;
use App\Repositories\Interfaces\IOrderRepository;
use App\Framework\Repository;
use PDO;
use PDOException;

class OrderRepository extends Repository implements IOrderRepository
{
    /**
     * Base query for fetching orders with full hydration of user and order items with ticket types.
     */
    private function getBaseQuery(): string
    {
        return "
            SELECT
                o.order_id,
                o.user_id,
                o.order_date,
                o.subtotal,
                o.total,
                o.serviceFee,
                o.reservationFees,
                o.currency,
                o.status,
                o.stripe_payment_intent_id,
                o.stripe_customer_id,
                o.created_at as order_created_at,
                o.paid_at,

                -- User fields
                u.id as user_id,
                u.email as user_email,
                u.fname as user_fname,
                u.lname as user_lname,
                u.address as user_address,
                u.phone as user_phone,
                u.created_at as user_created_at,

                -- Order Item fields
                oi.orderitem_id,
                oi.order_id as oi_order_id,
                oi.ticket_type_id,
                oi.quantity,
                oi.unit_price,
                oi.reservation_fee,

                -- Ticket Type fields
                tt.ticket_type_id,
                s.schedule_id,
                tt.description,
                tt.min_age,
                tt.max_age,
                tt.min_quantity,
                tt.max_quantity,
                tt.capacity,
                tt.special_requirements,

                -- Ticket scheme fields
                ts.ticket_scheme_id,
                ts.name as ts_name,
                ts.scheme_enum,
                ts.price,
                ts.fee,
                ts.ticket_language,

                -- Schedule fields
                s.event_id,
                s.date,
                s.start_time,
                s.end_time,
                s.total_capacity,
                s.tickets_sold,
                s.is_sold_out,
                s.venue_id,
                s.artist_id,
                s.restaurant_id,
                s.landmark_id,

                -- Venue fields
                v.venue_id,
                v.name as venue_name,
                v.street_address as venue_address,
                v.city as venue_city,
                v.postal_code as venue_postal_code,
                v.country as venue_country,
                v.description_html as venue_description_html,
                v.capacity as venue_capacity,
                v.phone as venue_phone,
                v.venue_image_id as venue_image_id,
                v.email as venue_email,

                -- Venue media fields
                venue_media.media_id as venue_media_id,
                venue_media.file_path as venue_media_file_path,
                venue_media.alt_text as venue_media_alt_text,
                venue_media.file_path as image_path,
                venue_media.alt_text as image_alt,

                -- Artist fields
                a.artist_id,
                a.name as artist_name,
                a.slug as artist_slug,
                a.profile_image_id as artist_profile_image_id,

                -- Artist media fields
                artist_media.media_id as artist_media_id,
                artist_media.file_path as artist_media_file_path,
                artist_media.alt_text as artist_media_alt_text,

                -- Restaurant fields
                r.restaurant_id,
                r.name as restaurant_name,
                r.main_image_id as restaurant_main_image_id,

                -- Restaurant media fields
                restaurant_media.media_id as restaurant_media_id,
                restaurant_media.file_path as restaurant_media_file_path,
                restaurant_media.alt_text as restaurant_media_alt_text,

                -- Landmark fields
                l.landmark_id,
                l.name as landmark_name,
                l.name as landmark_title,
                l.short_description as landmark_short_description,
                l.landmark_slug,
                l.main_image_id,

                -- Landmark media fields
                landmark_media.media_id as landmark_media_id,
                landmark_media.file_path as landmark_media_file_path,
                landmark_media.alt_text as landmark_media_alt_text,

                -- Event category fields
                ec.event_id as event_category_id,
                ec.type as event_category_type,
                ec.title as event_category_title,
                ec.category_description as event_category_description,
                ec.slug as event_category_slug

            FROM `ORDER` o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN ORDER_ITEM oi ON o.order_id = oi.order_id
            LEFT JOIN TICKET_TYPE tt ON oi.ticket_type_id = tt.ticket_type_id
            LEFT JOIN TICKET_SCHEME ts ON tt.scheme_id = ts.ticket_scheme_id
            LEFT JOIN SCHEDULE s ON tt.schedule_id = s.schedule_id
            LEFT JOIN VENUE v ON s.venue_id = v.venue_id
            LEFT JOIN MEDIA venue_media ON v.venue_image_id = venue_media.media_id
            LEFT JOIN ARTIST a ON s.artist_id = a.artist_id
            LEFT JOIN MEDIA artist_media ON a.profile_image_id = artist_media.media_id
            LEFT JOIN RESTAURANT r ON s.restaurant_id = r.restaurant_id
            LEFT JOIN MEDIA restaurant_media ON r.main_image_id = restaurant_media.media_id
            LEFT JOIN LANDMARK l ON s.landmark_id = l.landmark_id
            LEFT JOIN MEDIA landmark_media ON l.main_image_id = landmark_media.media_id
            LEFT JOIN EVENT_CATEGORIES ec ON s.event_id = ec.event_id
        ";
    }

    /**
     * Base query for fetching order items with full ticket type hydration.
     */
    private function getOrderItemsBaseQuery(): string
    {
        return "
            SELECT
                oi.orderitem_id,
                oi.order_id,
                oi.ticket_type_id,
                oi.quantity,
                oi.unit_price,
                oi.reservation_fee,

                -- Ticket Type fields
                tt.ticket_type_id,
                s.schedule_id,
                tt.description,
                tt.min_age,
                tt.max_age,
                tt.min_quantity,
                tt.max_quantity,
                tt.capacity,
                tt.special_requirements,

                -- Ticket scheme fields
                ts.ticket_scheme_id,
                ts.name as name,
                ts.scheme_enum,
                ts.price,
                ts.fee,
                ts.ticket_language,

                -- Schedule fields
                s.event_id,
                s.date,
                s.start_time,
                s.end_time,
                s.total_capacity,
                s.tickets_sold,
                s.is_sold_out,
                s.venue_id,
                s.artist_id,
                s.restaurant_id,
                s.landmark_id,

                -- Venue fields
                v.venue_id,
                v.name as venue_name,
                v.street_address as venue_address,
                v.city as venue_city,
                v.postal_code as venue_postal_code,
                v.country as venue_country,
                v.description_html as venue_description_html,
                v.capacity as venue_capacity,
                v.phone as venue_phone,
                v.venue_image_id as venue_image_id,
                v.email as venue_email,

                --venue media fields
                venue_media.media_id as venue_media_id,
                venue_media.file_path as venue_media_file_path,
                venue_media.alt_text as venue_media_alt_text,
                venue_media.file_path as image_path,
                venue_media.alt_text as image_alt,

                -- Artist fields
                a.artist_id,
                a.name as artist_name,
                a.press_quote as artist_press_quote,
                a.profile_image_id as artist_profile_image_id,
               

                -- Artist media fields
                artist_media.media_id as artist_media_id,
                artist_media.file_path as artist_media_file_path,
                artist_media.alt_text as artist_media_alt_text,

                -- Restaurant fields
                r.restaurant_id,
                r.name as restaurant_name,
                r.price_category as restaurant_price_category,
                r.stars as restaurant_stars,
                r.main_image_id as restaurant_main_image_id,

                -- Restaurant media fields
                restaurant_media.media_id as restaurant_media_id,
                restaurant_media.file_path as restaurant_media_file_path,
                restaurant_media.alt_text as restaurant_media_alt_text,

                -- Landmark fields
                l.landmark_id,
                l.name as landmark_name,
                l.name as landmark_title,
                l.short_description as landmark_short_description,
                l.landmark_slug,
                l.main_image_id,

                -- Landmark media fields
                landmark_media.media_id as landmark_media_id,
                landmark_media.file_path as landmark_media_file_path,
                landmark_media.alt_text as landmark_media_alt_text,

                -- Event category fields
                ec.event_id as event_category_id,
                ec.type as event_category_type,
                ec.title as event_category_title,
                ec.category_description as event_category_description,
                ec.slug as event_category_slug

            FROM ORDER_ITEM oi
            LEFT JOIN TICKET_TYPE tt ON oi.ticket_type_id = tt.ticket_type_id
            LEFT JOIN TICKET_SCHEME ts ON tt.scheme_id = ts.ticket_scheme_id
            LEFT JOIN SCHEDULE s ON tt.schedule_id = s.schedule_id
            LEFT JOIN VENUE v ON s.venue_id = v.venue_id
            LEFT JOIN ARTIST a ON s.artist_id = a.artist_id
            LEFT JOIN MEDIA artist_media ON a.profile_image_id = artist_media.media_id
            LEFT JOIN RESTAURANT r ON s.restaurant_id = r.restaurant_id
            LEFT JOIN MEDIA restaurant_media ON r.main_image_id = restaurant_media.media_id
            LEFT JOIN LANDMARK l ON s.landmark_id = l.landmark_id
            LEFT JOIN MEDIA landmark_media ON l.main_image_id = landmark_media.media_id
            LEFT JOIN MEDIA venue_media ON v.venue_image_id = venue_media.media_id
            LEFT JOIN EVENT_CATEGORIES ec ON s.event_id = ec.event_id
        ";
    }

    public function createOrder(Order $order): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO `ORDER` (
                    user_id,
                    order_date,
                    subtotal,
                    total,
                    serviceFee,
                    reservationFees,
                    currency,
                    status,
                    stripe_payment_intent_id,
                    stripe_customer_id,
                    created_at,
                    paid_at
                ) VALUES (
                    :user_id,
                    :order_date,
                    :subtotal,
                    :total,
                    :serviceFee,
                    :reservationFees,
                    :currency,
                    :status,
                    :stripe_payment_intent_id,
                    :stripe_customer_id,
                    :created_at,
                    :paid_at
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':user_id', $order->user?->id, PDO::PARAM_INT);
            $stmt->bindValue(':order_date', $order->order_date?->format('Y-m-d H:i:s'));
            $stmt->bindValue(':subtotal', $order->subtotal ?? 0.0);
            $stmt->bindValue(':total', $order->total ?? 0.0);
            $stmt->bindValue(':serviceFee', $order->serviceFee ?? 0.0);
            $stmt->bindValue(':reservationFees', $order->reservationFees ?? 0.0);
            $stmt->bindValue(':currency', $order->currency ?: 'EUR');
            $stmt->bindValue(':status', $order->status->value);
            $stmt->bindValue(':stripe_payment_intent_id', $order->stripe_payment_intent_id);
            $stmt->bindValue(':stripe_customer_id', $order->stripe_customer_id);
            $stmt->bindValue(':created_at', $order->created_at ?? date('Y-m-d H:i:s'));
            $stmt->bindValue(':paid_at', $order->paid_at);

            $result = $stmt->execute();
            if ($result) {
                $order->order_id = (int)$pdo->lastInsertId();
            }

            return $result;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to create order: " . $e->getMessage());
        }
    }

    public function getOrderById(int $orderId): ?Order
    {
        try {
            $pdo = $this->connect();

            $query = $this->getBaseQuery() . " WHERE o.order_id = :order_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) {
                return null;
            }

            // Create order from first row (order data is same across all item rows)
            $order = new Order();
            $order->fromPDOData($rows[0]);

            // Build array of OrderItems from all rows
            $seenItems = [];
            foreach ($rows as $row) {
                if (!is_null($row['orderitem_id']) && !isset($seenItems[$row['orderitem_id']])) {
                    $seenItems[$row['orderitem_id']] = true;
                    $orderItem = new OrderItem();
                    $orderItem = $orderItem->fromPdo($row);
                    $order->orderItems[] = $orderItem;
                }
            }

            return $order;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching order by ID: " . $e->getMessage());
        }
    }

    public function getOrdersByUserId(int $userId): array
    {
        try {
            $pdo = $this->connect();

            $query = $this->getBaseQuery() . "
                WHERE o.user_id = :user_id
                ORDER BY o.order_date DESC
            ";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $orders = [];
            $orderMap = [];

            foreach ($rows as $row) {
                $orderId = $row['order_id'];

                // Create order if not already created
                if (!isset($orderMap[$orderId])) {
                    $order = new Order();
                    $order->fromPDOData($row);
                    $orders[] = $order;
                    $orderMap[$orderId] = $order;
                } else {
                    $order = $orderMap[$orderId];
                }

                // Add order item if it exists
                if (!is_null($row['orderitem_id']) && !in_array($row['orderitem_id'], array_column($order->orderItems, 'orderitem_id'))) {
                    $orderItem = new OrderItem();
                    $orderItem = $orderItem->fromPdo($row);
                    $order->orderItems[] = $orderItem;
                }
            }

            return $orders;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching orders by user ID: " . $e->getMessage());
        }
    }
    public function getOpenOrderByUserId(int $userId): ?Order{
        try {
            $pdo = $this->connect();

            $query = $this->getBaseQuery() . "
                WHERE o.user_id = :user_id AND o.status = 'Pending' OR o.status = 'Confirmed'
                ORDER BY o.order_date DESC
                LIMIT 1
            ";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            //$stmt->bindValue(':status', OrderStatus::Pending->value);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) {
                return null;
            }

            // Create order from first row (order data is same across all item rows)
            $order = new Order();
            $order->fromPDOData($rows[0]);

            // Build array of OrderItems from all rows
            $seenItems = [];
            foreach ($rows as $row) {
                if (!is_null($row['orderitem_id']) && !isset($seenItems[$row['orderitem_id']])) {
                    $seenItems[$row['orderitem_id']] = true;
                    $orderItem = new OrderItem();
                    $orderItem = $orderItem->fromPdo($row);
                    $order->orderItems[] = $orderItem;
                }
            }

            return $order;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching open order by user ID: " . $e->getMessage());
        }
    }

    public function updateOrderStatus(int $orderId, OrderStatus $status): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                UPDATE `ORDER` SET
                    status = :status
                WHERE order_id = :order_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->bindValue(':status', $status->value);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to update order status: " . $e->getMessage());
        }
    }

    public function addOrderItem(OrderItem $orderItem): bool
    {
        try {
            $pdo = $this->connect();

            $query = "
                INSERT INTO ORDER_ITEM (
                    order_id,
                    ticket_type_id,
                    quantity,
                    unit_price,
                    reservation_fee
                ) VALUES (
                    :order_id,
                    :ticket_type_id,
                    :quantity,
                    :unit_price,
                    :reservation_fee
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':order_id', $orderItem->order_id, PDO::PARAM_INT);
            $stmt->bindValue(':ticket_type_id', $orderItem->ticket_type->ticket_type_id, PDO::PARAM_INT);
            $stmt->bindValue(':quantity', $orderItem->quantity, PDO::PARAM_INT);
            $stmt->bindValue(':unit_price', $orderItem->unit_price);
            $stmt->bindValue(':reservation_fee', $orderItem->reservation_fee);

            $result = $stmt->execute();
            if ($result) {
                $orderItem->orderitem_id = (int)$pdo->lastInsertId();
            }

            return $result;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to add order item: " . $e->getMessage());
        }
    }

    public function getOrderItemsByOrderId(int $orderId): array
    {
        try {
            $pdo = $this->connect();

            $query = $this->getOrderItemsBaseQuery() . "
                WHERE oi.order_id = :order_id
                ORDER BY oi.orderitem_id ASC
            ";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function (array $row): OrderItem {
                $orderItem = new OrderItem();
                return $orderItem->fromPdo($row);
            }, $rows);
        } catch (PDOException $e) {
            throw new \RuntimeException("Error fetching order items by order ID: " . $e->getMessage());
        }
    }
}