<?php
namespace App\Models\Payment;
use App\Models\Enums\OrderStatus;
use App\Models\User;
use App\Models\Payment\OrderItem;
use DateTime;

class Order
{
    public ?int $order_id = null;
    public ?string $reference_number = null;
    public ?User $user = null;
    public ?DateTime $order_date = null;
    public ?float $subtotal = null;
    public ?float $total = null;
    public ?float $serviceFee = null;
    public ?float $reservationFees = null;
    public string $currency = 'EUR';
    public OrderStatus $status = OrderStatus::In_Cart;
    public ?string $stripe_customer_id = null;
    public ?string $stripe_payment_intent_id = null;
    public ?string $stripe_checkout_session_id = null;
    public ?string $created_at = null;
    public ?string $paid_at = null;
    public ?string $ticket_pdf_path = null;

    /** @var OrderItem[] */
    public array $orderItems = [];

    public function __construct() {
        $this->calculateTotals();
    }

    public function fromPDOData(array $data): void
    {
        $this->order_id = isset($data['order_id']) ? (int)$data['order_id'] : 0;
        $this->reference_number = $data['order_reference_number'] ?? $data['reference_number'] ?? null;
        $this->subtotal = isset($data['subtotal']) ? (float)$data['subtotal'] : null;
        $this->total = isset($data['total']) ? (float)$data['total'] : null;
        $this->serviceFee = isset($data['serviceFee']) ? (float)$data['serviceFee'] : null;
        $this->reservationFees = isset($data['reservationFees']) ? (float)$data['reservationFees'] : null;
        $this->currency = (string)($data['currency'] ?? 'EUR');
        $this->status = isset($data['status']) ? OrderStatus::from($data['status']) : OrderStatus::In_Cart;
        $this->stripe_customer_id = $data['stripe_customer_id'] ?? null;
        $this->stripe_payment_intent_id = $data['stripe_payment_intent_id'] ?? null;
        $this->stripe_checkout_session_id = $data['stripe_checkout_session_id'] ?? null;
        $this->created_at = $data['order_created_at'] ?? $data['created_at'] ?? null;
        $this->paid_at = $data['paid_at'] ?? null;
        $this->order_date = isset($data['order_date']) ? new DateTime($data['order_date']) : null;
        $this->ticket_pdf_path = $data['ticket_pdf_path'] ?? null;
        // Hydrate User object
        $user = new User();
        $user->fromPDOData($data);
        $this->user = $user;

        // OrderItems are typically loaded separately via getOrderItemsByOrderId
        $this->orderItems = [];
    }
    public function calculateTotalAmount(): float
    {
        $total = 0.0;
        foreach ($this->orderItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }
        return $total;
    }
    public function calculateTotals(): void
    {
        $this->subtotal = 0.0;
        $this->reservationFees = 0.0;

        foreach ($this->orderItems as $item) {
            /** @var OrderItem $item */
            $this->subtotal += (float)($item->subtotal ?? 0.0);
            $this->reservationFees += (float)($item->reservation_fee ?? 0.0) * (int)($item->quantity ?? 0);
        }

        $this->serviceFee = round($this->subtotal * 0.025, 2);
        $this->total = $this->subtotal + $this->serviceFee + $this->reservationFees;
    }
    public function generateReferenceNumber(): void
    {
        $this->reference_number = 'HF-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
    }
}