<?php
namespace App\Views\ShoppingCart;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

/** @var ShoppingCartViewModel|null $viewModel */
$viewModel = $viewModel ?? null;
$order = $viewModel?->order;

?>


<section class="flex flex-col md:flex-row gap-8 w-[95%] mx-auto mt-5">
    <?php if (!isset($_SESSION['loggedInUser'])):?>
    <?php
            include __DIR__ . '/Components/DetailsLoginSignup.php';
        ?>
    <?php endif; ?>
    <section class="w-full md:w-[40%]">
        <?php
            include __DIR__ . '/Components/OrderSummaryRows.php';
        ?>
    </section>
</section>