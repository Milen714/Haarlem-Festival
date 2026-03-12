<?php
namespace App\Models\Payment;
use App\Models\Enums\OrderStatus;
use App\Models\User;
use App\Models\Payment\OrderItem;
use DateTime;

class Order
{
    public int $order_id;
    public User $user;
    public ?DateTime $order_date;
    public ?float $total_amount;
    public OrderStatus $status;
    public ?string $stripe_customer_id;
    public ?string $stripe_payment_intent_id;
    public ?string $created_at;
    public ?string $paid_at;

    /** @var OrderItem[] */
    public array $orderItems = [];

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->order_id = isset($data['order_id']) ? (int)$data['order_id'] : 0;
        $this->total_amount = isset($data['total_amount']) ? (float)$data['total_amount'] : null;
        $this->status = isset($data['status']) ? OrderStatus::from($data['status']) : OrderStatus::Pending;
        $this->stripe_customer_id = $data['stripe_customer_id'] ?? null;
        $this->stripe_payment_intent_id = $data['stripe_payment_intent_id'] ?? null;
        $this->created_at = $data['order_created_at'] ?? $data['created_at'] ?? null;
        $this->paid_at = $data['paid_at'] ?? null;
        $this->order_date = isset($data['order_date']) ? new DateTime($data['order_date']) : null;

        // Hydrate User object
        $user = new User();
        $user->fromPDOData($data);
        $this->user = $user;

        // OrderItems are typically loaded separately via getOrderItemsByOrderId
        $this->orderItems = [];
    }
}