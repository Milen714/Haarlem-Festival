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

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
    style="width:100%; border-collapse:collapse; margin:0; padding:0; background-color:#eef2f6;">
    <tr>
        <td align="center" style="padding:32px 12px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                style="max-width:720px; width:100%; border-collapse:separate; border-spacing:0; background-color:#ffffff; border:1px solid #d9e1ea; border-radius:12px;">
                <tr>
                    <td align="center" style="padding:40px 32px 18px 32px;">
                        <div
                            style="font-family:Montserrat, Arial, sans-serif; font-size:18px; line-height:24px; font-weight:700; letter-spacing:0.5px; color:#1e4b6e; text-transform:uppercase; margin:0 0 6px 0;">
                            See You There
                        </div>
                        <div
                            style="font-family:Montserrat, Arial, sans-serif; font-size:38px; line-height:42px; font-weight:800; color:#1e4b6e; text-transform:uppercase; margin:0 0 12px 0;">
                            Thank You
                        </div>
                        <div
                            style="font-family:Arial, sans-serif; font-size:16px; line-height:24px; font-weight:400; color:#52606d; margin:0;">
                            Your order has been received successfully. Below is your booking summary.
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px 32px 32px;">
                        <div style="border-top:1px solid #e6ebf1; line-height:1px; font-size:1px;">&nbsp;</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px 40px 32px;">
                        <?php
                            $showProceedButton = false;
                            include __DIR__ . '/Components/OrderSummary.php';
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>