<?php 
namespace App\Views\ShoppingCart;

use App\Models\TicketType;
use App\Models\Schedule;

/** @var TicketType $ticketType */
$ticketType = isset($ticketType) ? $ticketType : null;

/** @var Schedule $scheduleItem */
$scheduleItem = isset($ticketType) ? $ticketType->schedule : null;


?>


<section class="flex flex-col gap-6 font-roboto w-[90%] mx-auto  mt-5">
    <?php 
        
        include __DIR__ . '/Components/CheckoutProgress.php'; 
    ?>
    <?php 
        
        include __DIR__ . '/Components/TicketItemRow.php'; 
    ?>
    <a href="/checkout">Test Checkout</a>

</section>