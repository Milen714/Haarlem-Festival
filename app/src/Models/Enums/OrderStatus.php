<?php
namespace App\Models\Enums;

enum OrderStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Confirmed = 'Confirmed';
    case Cancelled = 'Cancelled';
}