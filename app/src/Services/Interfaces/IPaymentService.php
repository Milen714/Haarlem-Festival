<?php
namespace App\Services\Interfaces;
interface IPaymentService {
    public function stripeCheckout(object $item): void;
    //public function stripeCheckoutStatus(string $sessionId): bool;
}