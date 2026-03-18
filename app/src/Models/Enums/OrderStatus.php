<?php
namespace App\Models\Enums;

enum OrderStatus: string
{
    case Pending_Payment = 'Pending_Payment';
    case Fulfilled = 'Fulfilled';
    case Fulfillment_Failed = 'Fulfillment_Failed';
    case Paid = 'Paid';
    case In_Cart = 'In_Cart';
    case Refunded = 'Refunded';
    case Cancelled = 'Cancelled';
}