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
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        background: #f5f5f5;
        margin: 0;
        padding: 20px;
    }

    .page {
        width: 100%;
    }

    .container {
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
    }

    .ticket-wrapper {
        margin-bottom: 20px;
        page-break-inside: avoid;
    }

    /* your existing styles (unchanged) */
    .ticket-card {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 18px;
        background: #ffffff;
        overflow: hidden;
    }

    .ticket-accent {
        width: 10px;
    }

    .ticket-image-cell {
        width: 110px;
        vertical-align: top;
    }

    .ticket-image {
        display: block;
        width: 110px;
        height: 110px;
    }

    .ticket-main {
        padding: 12px 14px;
    }

    .ticket-inner {
        width: 100%;
        border-collapse: collapse;
    }

    .ticket-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .ticket-date-box-cell {
        width: 72px;
        vertical-align: top;
        padding-right: 12px;
    }

    .ticket-date-box {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        color: #111111;
        padding: 12px 8px;
        border-radius: 6px;
    }

    .ticket-label {
        display: inline-block;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        color: #222222;
        padding: 5px 10px;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .ticket-meta {
        font-size: 13px;
        line-height: 1.6;
    }

    .ticket-price {
        font-size: 22px;
        font-weight: bold;
    }

    .ticket-qr-cell {
        border-left: 2px dashed #9ca3af;
        padding-left: 10px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="page">
        <div class="container">

            <?php foreach ($viewModel?->order->orderItems ?? [] as $item): ?>
            <div class="ticket-wrapper">
                <?php
                        $scheduleItem = $item->ticket_type->schedule;
                        $ticketType = $item->ticket_type;
                        include __DIR__ . '/Components/TicketPDFRow.php';
                    ?>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
</body>

</html>