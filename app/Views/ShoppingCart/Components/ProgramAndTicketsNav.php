<?php 
namespace App\Views\ShoppingCart\Components;
use App\ViewModels\ShoppingCart\PaidTicketsViewModel;
/**
 * Component for rendering the navigation tabs for Program and Tickets
 */

/** @var PaidTicketsViewModel $viewModel */
?>

<nav class="flex justify-between">
    <ul class="flex gap-4 items-end" aria-label="Ticket Tabs">

        <button id="my-program-button"
            class="h-min <?php echo $viewModel->showMyTicketsSection ? 'home-ticket-tab-inactive' : 'home-ticket-tab-active'; ?>"
            data-showMyTicketSection="false">My
            Program <span>( <?php echo $viewModel->totalTickets; ?> )</span></button>


        <button id="my-tickets-button"
            class="h-min <?php echo $viewModel->showMyTicketsSection ? 'home-ticket-tab-active' : 'home-ticket-tab-inactive'; ?>"
            data-showMyTicketSection="true">My
            Tickets <span>( <?php echo $viewModel->totalTickets; ?> )</span></button>


        <a href="/payment" class="h-min home-ticket-tab-inactive" data-date="">Shopping Cart</a>

    </ul>

    <button id="share-program-btn" class="home_calendar_button_active">
        <img src="/Assets/Home/ShareIcon.svg" alt="Share Icon"> Share Program
    </button>


</nav>

<!--Share Program-->
<div id="share-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="font-bold text-lg mb-1">Share Your Program</h3>
        <p class="text-sm text-gray-500 mb-4">Anyone with this link can view your schedule.</p>
        <div class="flex gap-2">
            <input id="share-url-input" type="text" readonly
                class="flex-grow border border-gray-300 rounded px-3 py-2 text-sm text-gray-700 bg-gray-50 min-w-0" />
            <button id="copy-url-btn" class="home_calendar_button_active flex-shrink-0 px-4">Copy</button>
        </div>
        <p id="copy-feedback" class="text-green-600 text-xs mt-2 hidden">Copied to clipboard!</p>
        <button id="close-share-modal" class="mt-4 text-sm text-gray-400 hover:text-gray-600 underline">Close</button>
    </div>
</div>


<script>
document.getElementById('my-program-button')?.addEventListener('click', function() {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('showMyTicketSection', 'false');
    window.location.href = currentUrl.toString();
});

document.getElementById('my-tickets-button')?.addEventListener('click', function() {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('showMyTicketSection', 'true');
    window.location.href = currentUrl.toString();
});
</script>