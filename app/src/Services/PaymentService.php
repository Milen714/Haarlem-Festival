<?php 
namespace App\Services;
use App\Services\Interfaces\IPaymentService;


class PaymentService implements IPaymentService {

    public function __construct() {
    }

    public function stripeCheckout(object $item): void {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->safeLoad();

        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
        $domainUrl = rtrim($_ENV['DOMAIN_URL'] ?? 'http://localhost', '/');

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

    echo json_encode(array('clientSecret' => $checkout_session->client_secret));
}

}