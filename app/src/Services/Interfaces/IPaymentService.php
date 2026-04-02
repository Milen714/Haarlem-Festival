<?php
namespace App\Services\Interfaces;
interface IPaymentService {
    public function stripeCheckout(object $item): \Stripe\Checkout\Session;
    public function stripeCheckoutStatus(array $jsonData): array;
    public function verifyWebhookSignature(string $payload, string $sigHeader): \Stripe\Event;
}