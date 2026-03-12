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
    public ?float $subtotal;
    public ?float $total_price; 

    public function __construct() {}

    public function fromPdo(array $data): OrderItem {
        $item = new OrderItem();
        $item->orderitem_id = $data['orderitem_id'] ?? null;
        $item->order_id = $data['order_id'] ?? null;
        $item->quantity = isset($data['quantity']) ? (int)$data['quantity'] : 0;
        $ticketType = new TicketType();
        $ticketType->fromPDOData($data);
        $item->ticket_type = $ticketType;   
        $item->unit_price = $ticketType->ticket_scheme->price ?? null;
        $item->reservation_fee = $item->calculateReservationFee();
        $item->subtotal = $item->calculateSubtotal();
        $item->total_price = $item->calculateTotalPrice();

        return $item;
    }
    private function calculateTotalPrice(): float
    {
        $total = ($this->unit_price ?? 0.0) * $this->quantity;
        if ($this->reservation_fee !== null) {
            $total += $this->reservation_fee * $this->quantity;
        }
        return $total;
    }

    private function calculateSubtotal(): float
    {
        return ($this->unit_price ?? 0.0) * $this->quantity;
    }
    private function calculateReservationFee(): float
    {
        if ($this->ticket_type->ticket_scheme->fee !== null) {
            return $this->ticket_type->ticket_scheme->fee;
        }
        return 0.0;
    }
}