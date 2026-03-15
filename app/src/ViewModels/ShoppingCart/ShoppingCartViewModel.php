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
        $this->order->calculateTotals();
        $this->nCartItems = $this->getNumberOfCartItems();
        $this->subtotal = $order->subtotal ?? 0.0;
        $this->reservationFees = $order->reservationFees ?? 0.0;
        $this->serviceFee = $order->serviceFee ?? 0.0;
        $this->total = $order->total ?? 0.0;


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

    
   
}