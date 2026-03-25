<?php
namespace App\Views\Home\Components;

use App\Models\Schedule;
use App\Models\Media;
use App\Models\Enums\EventType;
use App\Models\Payment\OrderItem;
use App\ViewModels\ShoppingCart\OrderItemViewModel;

/** @var OrderItem $item */

$p = new OrderItemViewModel($item);

$cardImage = $p->getCardImage() ?? new Media();
$scheduleRef = $scheduleItem ?? new Schedule();

$eventLabel = '';
$accentColor = '#6B2FD1';
$mutedColor = '#EFE7FB';
$eventType = $scheduleRef->event_category?->type ?? null;

if (isset($item) && !empty($item)) {
    switch ($eventType) {
        case EventType::Magic:
            $accentColor = '#B18132';
            $mutedColor = '#F3E8D1';
            $eventLabel = $scheduleRef->event_category?->title ?? 'Magic';
            break;

        case EventType::History:
            $accentColor = '#A7C957';
            $mutedColor = '#EAF4D7';
            $eventLabel = 'History';
            break;

        case EventType::Yummy:
            $accentColor = '#CC112F';
            $mutedColor = '#F8D9DF';
            $eventLabel = 'Yummy';
            break;

        case EventType::Jazz:
            $accentColor = '#6B2FD1';
            $mutedColor = '#EDE4FB';
            $eventLabel = 'Jazz';
            break;

        case EventType::Dance:
            $accentColor = '#CC9900';
            $mutedColor = '#FFF3BF';
            $eventLabel = 'Dance';
            break;

        default:
            $accentColor = '#6B2FD1';
            $mutedColor = '#EDE4FB';
            $eventLabel = 'Event';
            break;
    }
}

$imagePath = $cardImage->file_path ?? '';
$imageAlt  = $cardImage->alt_text ?? '';
?>

<table class="ticket-card" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="ticket-accent" style="background-color: <?= htmlspecialchars($accentColor) ?>;"></td>

        <td class="ticket-main">
            <table class="ticket-inner" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="ticket-content">
                        <div class="ticket-label" style="background-color: <?= htmlspecialchars($mutedColor) ?>;">
                            <?= htmlspecialchars(strtoupper($eventLabel)) ?>
                        </div>

                        <table class="ticket-info-row" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td class="ticket-date-box-cell">
                                    <div class="ticket-date-box"
                                        style="background-color: <?= htmlspecialchars($mutedColor) ?>;">
                                        <?= htmlspecialchars($p->getDateBoxLabel()) ?>
                                    </div>
                                </td>

                                <td class="ticket-text-cell">
                                    <div class="ticket-title">
                                        <?= htmlspecialchars($p->getDisplayName()) ?>
                                    </div>

                                    <div class="ticket-meta">
                                        <span class="ticket-meta-label">Location:</span>
                                        <?= htmlspecialchars($p->getVenueDisplay()) ?>
                                    </div>

                                    <div class="ticket-meta">
                                        <span class="ticket-meta-label">Duration:</span>
                                        <?= htmlspecialchars($p->getDurationDisplay()) ?>
                                    </div>

                                    <div class="ticket-meta">
                                        <span class="ticket-meta-label">Quantity:</span>
                                        x <?= (int)$item->quantity ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td class="ticket-price-cell">
                        <div class="ticket-price">
                            €<?= number_format((float)$item->subtotal, 2) ?>
                        </div>
                    </td>

                    <td class="ticket-qr-cell">
                        <div class="ticket-qr-wrap">
                            <?php $item->generateQrCode(); ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>