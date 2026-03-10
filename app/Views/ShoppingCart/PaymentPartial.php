<?php
namespace App\Views\ShoppingCart;
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();
$stripePublishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
?>
<section class="flex flex-col gap-6 font-roboto w-[90%] mx-auto  mt-5">
    <?php 
        
        include __DIR__ . '/Components/CheckoutProgress.php'; 
    ?>

    <div id="checkout"></div>

</section>

<script>
const publishableKey = '<?= htmlspecialchars($stripePublishableKey, ENT_QUOTES, 'UTF-8') ?>';

if (!publishableKey.startsWith('pk_')) {
    throw new Error('Invalid STRIPE_PUBLISHABLE_KEY: frontend must use a pk_ key, not secret text.');
}

const stripe = Stripe(publishableKey);

fetch('/create-checkout-session')
    .then(async (res) => {
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch (_e) {
            throw new Error('Checkout session endpoint returned non-JSON: ' + text.slice(0, 160));
        }
    })
    .then(data => {
        if (!data.clientSecret) {
            throw new Error(data.error || 'Missing clientSecret in checkout session response.');
        }
        stripe.initEmbeddedCheckout({
            clientSecret: data.clientSecret
        }).then(checkout => {
            checkout.mount('#checkout');
        });
    })
    .catch((err) => {
        console.error(err);
    });
</script>