<?php
namespace App\Views\ShoppingCart;
use App\ViewModels\ShoppingCart\PaidTicketsViewModel;
use App\Models\User;

/** @var PaidTicketsViewModel $viewModel */
/** @var User $sharedByUser */
?>

<header class="flex flex-col mx-auto w-full md:w-[85%]">
    <div class="my-6 flex flex-col gap-1">
        <p class="text-sm text-gray-500 font-montserrat">Shared program</p>
        <h1 class="text-5xl font-montserrat font-bold text-[var(--text-home-primary)]">
            <?php echo htmlspecialchars(trim(($sharedByUser->fname ?? '') . ' ' . ($sharedByUser->lname ?? '')) ?: 'A visitor'); ?>'s Program
        </h1>
    </div>
</header>

<section class="flex flex-col gap-2 mx-auto w-full md:w-[85%] bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <?php if (!empty($viewModel->orderItems)): ?>
    <div class="bg-white p-6 rounded-lg shadow-sm max-h-[75vh] overflow-y-auto no-scrollbar">
        <section class="flex flex-col gap-2">
            <?php foreach ($viewModel->orderItems as $item): ?>
            <?php include __DIR__ . '/Components/TicketItemRow.php'; ?>
            <?php endforeach; ?>
        </section>
    </div>
    <?php else: ?>
    <p class="text-gray-500 py-4">This program has no upcoming events.</p>
    <?php endif; ?>
</section>
