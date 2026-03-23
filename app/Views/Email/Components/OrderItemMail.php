<?php
namespace App\Views\Email\Components;

use App\Models\Schedule;
use App\Models\Media;
use App\Models\Enums\EventType;
use App\Models\Payment\OrderItem;

/** @var OrderItem $item */

$eventLabel = '';
$accentColor = '#1e4b6e';
$badgeColor = '#dbeafe';
$cardImage = new Media();
$scheduleRef = new Schedule();
$eventName = '';
$locationName = '';
$timeText = '';
$durationText = '';
$quantityText = '';
$subtotalText = '€0.00';

if (isset($item) && !empty($item)) {
    $scheduleRef = $scheduleItem;
    $eventType = $scheduleRef->event_category?->type ?? null;
    $eventName = $item->ticket_type->schedule->artist->name ?? '';
    
    switch ($eventType) {
        case EventType::Magic:
            $accentColor = '#7c3aed';
            $badgeColor = '#ede9fe';
            $cardImage = $item->ticket_type->schedule->venue->venue_image ?? new Media();
            $eventLabel = $scheduleRef->event_category?->title ?? 'Magic';
            $eventName = $item->ticket_type->ticket_scheme->name ?? '';
            break;

        case EventType::History:
            $accentColor = '#92400e';
            $badgeColor = '#fef3c7';
            $cardImage = $item->ticket_type->schedule->landmark->landmark_image ?? new Media();
            $eventLabel = 'History';
            break;

        case EventType::Yummy:
            $accentColor = '#b45309';
            $badgeColor = '#fde68a';
            $cardImage = $item->ticket_type->schedule->restaurant->main_image ?? new Media();
            $eventLabel = 'Yummy';
            break;

        case EventType::Jazz:
            $accentColor = '#1e3a8a';
            $badgeColor = '#dbeafe';
            $cardImage = $item->ticket_type->schedule->artist->profile_image ?? new Media();
            $eventLabel = 'Jazz';
            break;

        case EventType::Dance:
            $accentColor = '#be185d';
            $badgeColor = '#fce7f3';
            $cardImage = $item->ticket_type->schedule->artist->profile_image ?? new Media();
            $eventLabel = 'Dance';
            break;

        default:
            $accentColor = '#1e4b6e';
            $badgeColor = '#e5e7eb';
            $eventLabel = 'Event';
            break;
    }

    $timeText = $scheduleItem->start_time?->format('H:i') ?? '';
    $locationName = $scheduleItem->venue?->name ?? $scheduleItem->landmark->name ?? '';
    $durationText = (string) $scheduleItem->getDurationInMinutes();
    $quantityText = (string) ($item->quantity ?? 0);
    $subtotalText = '€' . number_format((float)($item->subtotal ?? 0), 2);
}

$imageSrc = trim((string)($cardImage->file_path ?? ''));
$imageAlt = htmlspecialchars($cardImage->alt_text ?? $eventName);
?>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
    style="width:100%; border-collapse:separate; border-spacing:0; margin-bottom:18px; background-color:#ffffff; border:1px solid #dbe3ec; border-radius:10px; overflow:hidden;">
    <tr>
        <td style="width:6px; background-color:<?= htmlspecialchars($accentColor) ?>; font-size:0; line-height:0;">
            &nbsp;</td>

        <td style="padding:18px 18px 18px 18px; vertical-align:top;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:top; padding-right:16px;">
                        <div style="margin-bottom:12px;">
                            <span
                                style="display:inline-block; background-color:<?= htmlspecialchars($badgeColor) ?>; color:#23313f; font-family:Montserrat, Arial, sans-serif; font-size:11px; line-height:12px; font-weight:800; letter-spacing:0.6px; text-transform:uppercase; padding:7px 10px; border-radius:4px;">
                                <?= htmlspecialchars($eventLabel) ?>
                            </span>
                        </div>

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                            style="border-collapse:collapse;">
                            <tr>
                                <td style="vertical-align:top; padding-right:14px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                        style="border-collapse:separate; border-spacing:0; background-color:<?= htmlspecialchars($badgeColor) ?>; border-radius:8px;">
                                        <tr>
                                            <td
                                                style="padding:12px 10px; min-width:50px; text-align:center; font-family:Montserrat, Arial, sans-serif; font-size:18px; line-height:20px; font-weight:800; color:#111111;">
                                                <?= htmlspecialchars($timeText) ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <td style="vertical-align:top;">
                                    <div
                                        style="font-family:Montserrat, Arial, sans-serif; font-size:17px; line-height:22px; font-weight:800; color:#111111; margin-bottom:10px;">
                                        <?= htmlspecialchars($eventName) ?>
                                    </div>

                                    <div
                                        style="font-family:Arial, sans-serif; font-size:14px; line-height:21px; color:#1f2933; margin-bottom:3px;">
                                        <span style="font-weight:700;">Location:</span>
                                        <span style="font-weight:400;"><?= htmlspecialchars($locationName) ?></span>
                                    </div>

                                    <div
                                        style="font-family:Arial, sans-serif; font-size:14px; line-height:21px; color:#1f2933; margin-bottom:3px;">
                                        <span style="font-weight:700;">Duration:</span>
                                        <span style="font-weight:400;"><?= htmlspecialchars($durationText) ?>
                                            minutes</span>
                                    </div>

                                    <div
                                        style="font-family:Arial, sans-serif; font-size:14px; line-height:21px; color:#1f2933;">
                                        <span style="font-weight:700;">Quantity:</span>
                                        <span style="font-weight:400;"><?= htmlspecialchars($quantityText) ?>
                                            ticket<?= ((int)$quantityText === 1 ? '' : 's') ?></span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td style="width:104px; vertical-align:top; text-align:right; white-space:nowrap;">
                        <div
                            style="font-family:Montserrat, Arial, sans-serif; font-size:15px; line-height:18px; font-weight:700; color:#5b6875; padding-top:2px; margin-bottom:8px;">
                            Price
                        </div>
                        <div
                            style="font-family:Montserrat, Arial, sans-serif; font-size:30px; line-height:32px; font-weight:900; color:#111111;">
                            <?= htmlspecialchars($subtotalText) ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>