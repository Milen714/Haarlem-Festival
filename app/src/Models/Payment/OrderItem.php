<?php
namespace App\Models\Payment;
use App\Models\TicketType;

class OrderItem
{
    public ?int $orderitem_id = null;
    public ?int $sessionOrderitem_id = null;
    public ?int $order_id = null;
    public ?TicketType $ticket_type = null;
    public ?int $quantity = null;
    public ?float $unit_price = null;
    public ?float $reservation_fee = null;
    public ?float $subtotal = null;
    public ?float $total_price = null;

    public function __construct() {
        if ($this->ticket_type !== null && $this->ticket_type->ticket_scheme !== null) {
            $this->unit_price = $this->ticket_type->ticket_scheme->price;
            $this->reservation_fee = $this->calculateReservationFee();
            $this->subtotal = $this->calculateSubtotal();
            $this->total_price = $this->calculateTotalPrice();
        }
        
    }

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
    public function calculateTotalPriceWithNewQuantity(int $newQuantity): void
    {
        $this->quantity = $newQuantity;
        $this->subtotal = $this->calculateSubtotal();
        $this->total_price = $this->calculateTotalPrice();
    }

    private function calculateSubtotal(): float
    {
        return ($this->unit_price ?? 0.0) * $this->quantity;
    }
    private function calculateReservationFee(): float
    {
        if ($this->ticket_type !== null && $this->ticket_type->ticket_scheme !== null && $this->ticket_type->ticket_scheme->fee !== null) {
            return $this->ticket_type->ticket_scheme->fee;
        }
        return 0.0;
    }
    public function createOrderItemFromTicketType(int $quantity, TicketType $ticketType): OrderItem
    {
        $orderItem = new OrderItem();
        $orderItem->quantity = $quantity;
        $orderItem->ticket_type = $ticketType;
        $orderItem->unit_price = $ticketType->ticket_scheme->price ?? 0.0;
        $orderItem->reservation_fee = $orderItem->calculateReservationFee();
        $orderItem->subtotal = $orderItem->calculateSubtotal();
        $orderItem->total_price = $orderItem->calculateTotalPrice();
        return $orderItem;
    }
}