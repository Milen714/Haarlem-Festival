<?php 
namespace App\Services;
use App\Services\Interfaces\IPaymentService;
use App\config\Secrets;



class PaymentService implements IPaymentService {

    public function __construct() {
    }

    public function stripeCheckout(object $item): void {
        $stripeSecretKey = Secrets::$stripeSecretKey  ?? '';
        $domainUrl = rtrim(Secrets::$domain ?? 'http://localhost', '/');

        if ($stripeSecretKey === '') {
            throw new \RuntimeException('Missing STRIPE_SECRET_KEY environment variable.');
        }

        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        header('Content-Type: application/json');


        $checkout_session = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'mode' => 'payment',

            // Minimal line item (no dashboard setup needed)
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

        http_response_code(200);
        echo json_encode(array('clientSecret' => $checkout_session->client_secret));
}
    public function stripeCheckoutStatus(array $jsonData): void {
        header('Content-Type: application/json');
       // Implement logic to check payment status using Stripe API
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

            http_response_code(200);
            echo json_encode([
                'status' => $session->status,
                'payment_status' => $session->payment_status,
                'customer_email' => $session->customer_details?->email ?? '',
                'session_created_at' => $session->created,
                'session_created_at_iso' => gmdate('c', $session->created),
                'paid_at' => $paidAt,
                'paid_at_iso' => $paidAt ? gmdate('c', $paidAt) : null,
            ]);
       } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            
        }


    }

}