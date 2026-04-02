<?php
namespace App\Views\ShoppingCart\Partials;

use App\ViewModels\ShoppingCart\PaidTicketsViewModel;

/** @var PaidTicketsViewModel $viewModel */
?>



<div class="bg-white p-6 rounded-lg shadow-sm max-h-[75vh] overflow-y-auto no-scrollbar">
    <section class="flex flex-col gap-2">
        <?php foreach($viewModel->orderItems as $item): ?>
        <?php include __DIR__ . '/../Components/TicketItemRow.php'; ?>
        <?php endforeach; ?>
    </section>

    <aside class="flex items-start bg-amber-50 p-4 rounded-md border border-amber-100">
        <span class="mr-3 text-amber-500">ℹ️</span>
        <div>
            <h4 class="text-xs font-bold text-amber-900">Your Program is saved automatically</h4>
            <p class="text-xs text-amber-800 opacity-80 mt-1 leading-relaxed">Share with friends or
                convert saved Tickets to order when ready. Items in your Program are not confirmed until
                payment is completed.</p>
        </div>
    </aside>
</div>