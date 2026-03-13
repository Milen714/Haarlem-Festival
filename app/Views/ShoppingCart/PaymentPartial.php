<?php
namespace App\Views\ShoppingCart;

use App\ViewModels\ShoppingCart\ShoppingCartViewModel;
use function App\Views\ShoppingCart\Components\displaySteps;

/** @var ShoppingCartViewModel|null $viewModel */
$viewModel = $viewModel ?? null;
$order = $viewModel?->order;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();
$stripePublishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
?>
<?php 
        include __DIR__ . '/Components/CheckoutProgress.php';
        displaySteps(3); 
    ?>
<section class="flex flex-col md:flex-row gap-8 w-[95%] mx-auto mt-5">

    <section class="w-full md:w-[60%]" id="checkout"></section>

    <section class="w-full md:w-[40%]">
        <?php
            $showProceedButton = false;
            include __DIR__ . '/Components/OrderSummaryRows.php';
        ?>
    </section>

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