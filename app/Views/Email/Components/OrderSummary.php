<?php 
namespace App\Views\Email\Components;
?>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
    style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="padding-bottom:24px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                style="border-collapse:collapse; font-family:Montserrat, Arial, sans-serif;">
                <tr>
                    <td style="font-size:18px; line-height:24px; font-weight:800; color:#111111; padding-bottom:10px;">
                        Order Summary
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="border-bottom:3px solid #1e4b6e; width:112px; line-height:1px; font-size:1px;">
                            &nbsp;</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding-bottom:8px;">
            <?php
            foreach ($viewModel?->order->orderItems ?? [] as $item) {
                $scheduleItem = $item->ticket_type->schedule;
                $ticketType = $item->ticket_type;
                include __DIR__ . '/OrderItem.php';
            }
            ?>
        </td>
    </tr>

    <tr>
        <td>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                style="width:100%; border-collapse:collapse; margin-top:8px; font-family:Montserrat, Arial, sans-serif;">
                <tr>
                    <td style="padding:8px 0; font-size:15px; line-height:22px; font-weight:700; color:#25313c;">
                        Tickets / Reservations (<?= (int)($viewModel?->nCartItems ?? 0) ?>)
                    </td>
                    <td align="right"
                        style="padding:8px 0; font-size:15px; line-height:22px; font-weight:800; color:#111111;">
                        €<?= number_format((float)($viewModel?->subtotal ?? 0.0), 2) ?>
                    </td>
                </tr>

                <?php if (($viewModel?->reservationFees ?? 0.0) > 0): ?>
                <tr>
                    <td style="padding:8px 0; font-size:15px; line-height:22px; font-weight:700; color:#25313c;">
                        Reservation Fees
                    </td>
                    <td align="right"
                        style="padding:8px 0; font-size:15px; line-height:22px; font-weight:800; color:#111111;">
                        €<?= number_format((float)$viewModel->reservationFees, 2) ?>
                    </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td colspan="2" style="padding:18px 0 14px 0;">
                        <div style="border-top:1px solid #e2e8f0; line-height:1px; font-size:1px;">&nbsp;</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:6px 0; font-size:15px; line-height:22px; font-weight:700; color:#4b5a67;">
                        Subtotal
                    </td>
                    <td align="right"
                        style="padding:6px 0; font-size:15px; line-height:22px; font-weight:800; color:#111111;">
                        €<?= number_format((float)($viewModel?->subtotal ?? 0.0), 2) ?>
                    </td>
                </tr>

                <tr>
                    <td style="padding:6px 0; font-size:15px; line-height:22px; font-weight:700; color:#4b5a67;">
                        Service Fee (2.5%)
                    </td>
                    <td align="right"
                        style="padding:6px 0; font-size:15px; line-height:22px; font-weight:800; color:#111111;">
                        €<?= number_format((float)($viewModel?->serviceFee ?? 0.0), 2) ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="padding-top:24px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                            style="width:100%; border-collapse:separate; border-spacing:0; background-color:#f8fbff; border:1px solid #dbe6f0; border-radius:10px;">
                            <tr>
                                <td
                                    style="padding:18px 20px; font-size:18px; line-height:24px; font-weight:800; color:#1e4b6e; letter-spacing:0.6px; text-transform:uppercase; font-family:Montserrat, Arial, sans-serif;">
                                    Total
                                </td>
                                <td align="right"
                                    style="padding:18px 20px; font-size:40px; line-height:42px; font-weight:900; color:#000000; font-family:Montserrat, Arial, sans-serif;">
                                    €<?= number_format((float)($viewModel?->total ?? 0.0), 2) ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>