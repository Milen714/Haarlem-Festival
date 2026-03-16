<?php
namespace App\Views\ShoppingCart;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

use function App\Views\ShoppingCart\Components\displaySteps;

/** @var ShoppingCartViewModel|null $viewModel */
$viewModel = $viewModel ?? null;
$order = $viewModel?->order;

?>

<?php include __DIR__ . '/Components/CheckoutProgress.php';
        displaySteps(2); ?>

<section class="flex flex-col md:flex-row gap-8 w-[95%] mx-auto mt-5">
    <!-- !-- If user is not logged in, show login/signup section --! -->
    <?php if (!isset($_SESSION['loggedInUser'])):?>
    <?php
            include __DIR__ . '/Components/DetailsLoginSignup.php';
        ?>
    <?php else: ?>
    <section class="w-full md:w-[60%] bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h2 class="text-2xl font-bold mb-6">Welcome back,
            <?= htmlspecialchars($_SESSION['loggedInUser']->fname ?? ''); ?>!</h2>
        <p class="text-gray-700 mb-4">Review your order details below and proceed to payment.</p>
        <a href="/checkout" class="home_dance_button mt-auto block text-center">Proceed to Checkout</a>
    </section>
    <?php endif; ?>
    <section class="w-full md:w-[40%]">
        <?php
            $showProceedButton = false;
            include __DIR__ . '/Components/OrderSummaryRows.php';
        ?>
    </section>
</section>