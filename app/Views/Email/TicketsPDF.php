<?php
namespace App\Views\Email;

use App\Models\TicketType;
use App\Models\Schedule;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

/** @var TicketType $ticketType */
$ticketType = isset($ticketType) ? $ticketType : null;

/** @var Schedule $scheduleItem */
$scheduleItem = new Schedule();

/** @var ShoppingCartViewModel|null $viewModel */
$viewModel = $viewModel ?? null;
$order = $viewModel?->order;
?>



<section class="flex flex-col justify-center items-center gap-5 mt-2">

    <?php
            foreach ($viewModel?->order->orderItems ?? [] as $item) {
                $scheduleItem = $item->ticket_type->schedule;
                $ticketType = $item->ticket_type;
                include __DIR__ . '/Components/TicketPDFRow.php';
            }
            ?>

</section>