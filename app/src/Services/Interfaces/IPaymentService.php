<?php
namespace App\Services\Interfaces;
interface IPaymentService {
    public function stripeCheckout(object $item): \Stripe\Checkout\Session;
    public function stripeCheckoutStatus(array $jsonData): void;
    public function verifyWebhookSignature(string $payload, string $sigHeader): \Stripe\Event;
}