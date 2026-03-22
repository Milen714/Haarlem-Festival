<?php 
namespace App\Services;
use App\Services\Interfaces\IPaymentService;
use App\config\Secrets;



class PaymentService implements IPaymentService {

    public function __construct() {
    }

    public function stripeCheckout(object $item): \Stripe\Checkout\Session {
        $stripeSecretKey = Secrets::$stripeSecretKey  ?? '';
        $domainUrl = rtrim(Secrets::$domain ?? 'http://localhost', '/');

        if ($stripeSecretKey === '') {
            throw new \RuntimeException('Missing STRIPE_SECRET_KEY environment variable.');
        }

        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        return $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->amount, // amount in cents
                ],
                'quantity' => 1,
            ]],
            // Stripe will replace {CHECKOUT_SESSION_ID}
            'return_url' => $domainUrl . '/return?session_id={CHECKOUT_SESSION_ID}&paymentId=',
        ]);
}
    public function stripeCheckoutStatus(array $jsonData): array {
       try{
            $stripeSecret = Secrets::$stripeSecretKey  ?? '';
            $stripe = new \Stripe\StripeClient($stripeSecret);
            $sessionId = $jsonData['session_id'] ?? null;
            if (!$sessionId) {
                throw new \InvalidArgumentException('Missing session_id in request data.');
            }

            $session = $stripe->checkout->sessions->retrieve($sessionId);

            $paymentIntent = null;
            $paidAt = null;

            if (!empty($session->payment_intent)) {
                $paymentIntent = $stripe->paymentIntents->retrieve($session->payment_intent);
                $paidAt = $paymentIntent->created ?? null;
            }

            return [
                'status' => $session->status,
                'payment_status' => $session->payment_status,
                'customer_email' => $session->customer_details?->email ?? '',
                'session_created_at' => $session->created,
                'session_created_at_iso' => gmdate('c', $session->created),
                'paid_at' => $paidAt,
                'paid_at_iso' => $paidAt ? gmdate('c', $paidAt) : null,
            ];
       } catch (\Exception $e) {
            throw $e;
        }
    }

    public function verifyWebhookSignature(string $payload, string $sigHeader): \Stripe\Event
    {
        $webhookSecret = Secrets::$stripeWebhookSecret ?? '';
        if ($webhookSecret === '') {
            throw new \RuntimeException('Missing STRIPE_WEBHOOK_SECRET environment variable.');
        }
        return \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
    }
}