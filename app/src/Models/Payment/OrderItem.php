<?php
namespace App\Models\Payment;
use App\Models\TicketType;

class OrderItem
{
    public ?int $orderitem_id;
    public ?int $order_id;
    public TicketType $ticket_type;
    public int $quantity;
    public ?float $unit_price;
    public ?float $reservation_fee;
    public ?float $total_price; 

    public function __construct() {}

    public function fromPdo(array $data): OrderItem {
        $item = new OrderItem();
        $item->orderitem_id = $data['orderitem_id'] ?? null;
        $item->order_id = $data['order_id'] ?? null;
        $item->quantity = $data['quantity'] ?? 0;
        $item->unit_price = $data['unit_price'] ?? 0.0;
        $item->reservation_fee = $data['reservation_fee'] ?? 0.0;
        $item->total_price = $data['total_price'] ?? 0.0;
        $ticketType = new TicketType();
        $ticketType->fromPDOData($data);
        $item->ticket_type = $ticketType;   

        return $item;
    }

    
}