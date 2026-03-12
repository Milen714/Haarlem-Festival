<?php
namespace App\ViewModels\ShoppingCart;

use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;

class ShoppingCartViewModel
{
    public Order $order;
    public int $nCartItems = 0;
    public float $subtotal = 0.0;
    public float $serviceFee = 0.0;
    public float $reservationFees = 0.0;
    public float $total = 0.0;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->calculateTotals();
        $this->nCartItems = $this->getNumberOfCartItems();

    }
    public function getNumberOfCartItems(): int
    {
        $this->nCartItems = 0;

        foreach ($this->order->orderItems as $item) {
            /** @var OrderItem $item */
            $this->nCartItems += (int)($item->quantity ?? 0);
        }

        return $this->nCartItems;
    }

    public function calculateTotals(): void
    {
        $this->subtotal = 0.0;
        $this->reservationFees = 0.0;

        foreach ($this->order->orderItems as $item) {
            /** @var OrderItem $item */
            $this->subtotal += (float)($item->subtotal ?? 0.0);
            $this->reservationFees += (float)($item->reservation_fee ?? 0.0) * (int)($item->quantity ?? 0);
        }

        $this->serviceFee = round($this->subtotal * 0.025, 2);
        $this->total = $this->subtotal + $this->serviceFee + $this->reservationFees;
    }
   
}