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

    <a href="/payment" class="home_calendar_button_active  ">
        <img src="/Assets/Home/ShareIcon.svg" alt="Share Icon"> Share Program
    </a>

</nav>

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