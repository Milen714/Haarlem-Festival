<?php

namespace App\Controllers;

use App\Models\Enums\OrderStatus;
use App\Framework\BaseController;
use App\Models\Payment\Order;
use App\Services\Interfaces\IOrderService;
use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\ITicketService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\TicketService;
use App\Services\MailService;
use App\Services\Interfaces\IMailService;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Services\TicketFulfillmentService;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

class StripeWebhookController extends BaseController
{
    private IPaymentService $paymentService;
    private IOrderService   $orderService;
    private ITicketService  $ticketService;
    private IMailService    $mailService;
    private ITicketFulfillmentService $ticketFulfillmentService;
    private ILogService $logService;


    public function __construct()
    {
        $this->paymentService = new PaymentService();
        $this->orderService   = new OrderService();
        $this->ticketService  = new TicketService();
        $this->mailService    = new MailService();
        $this->ticketFulfillmentService = new TicketFulfillmentService();
        $this->logService = new LogService();
    }

    // Main handler for Stripe webhook events. Verifies the signature, processes the event, and updates order status accordingly.
    public function handle(array $params = []): void
    {

        $payload   = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            $event = $this->paymentService->verifyWebhookSignature($payload, $sigHeader);


            $session = $event->data->object;


            switch ($event->type) {
                case 'checkout.session.completed':
                    if (($session->payment_status ?? '') !== 'paid') {
                        break;
                    }
                    $order = $this->orderService->getOrderByStripeCheckoutSessionId($session->id);
                    if ($order === null || $order->status === OrderStatus::Paid) {
                        break;
                    }
                    $this->logService->info('StripeWebhook', 'Order fetched from checkout session', [
                        'order_id' => $order->order_id,
                        'items_count' => count($order->orderItems ?? [])
                    ]);
                    $this->orderService->updateOrderStatus($order->order_id, OrderStatus::Paid);

                    // Render views and have service generate PDF and send email
                    $viewModel = new ShoppingCartViewModel($order);
                    $pdfHtml = $this->renderViewToString('Email/TicketsPDF', ['viewModel' => $viewModel]);
                    $emailHtml = $this->renderViewToString('Email/TicketsMailBody', ['viewModel' => $viewModel]);
                    
                    $pdf_path = $this->ticketFulfillmentService->sendTicketEmail($order, $pdfHtml, $emailHtml);

                    $this->orderService->updateOrderStatus($order->order_id, OrderStatus::Fulfilled, $pdf_path);
                    break;

                case 'checkout.session.expired':
                    $order = $this->orderService->getOrderByStripeCheckoutSessionId($session->id);
                    if ($order === null || $order->status === OrderStatus::Cancelled) {
                        break;
                    }
                    $this->ticketService->releaseOrderItems($order->orderItems);
                    $this->orderService->updateOrderStatus($order->order_id, OrderStatus::Cancelled);
                    break;
            }
            $this->sendSuccessResponse(['received' => true], 200);
        } catch (\Throwable $e) {
            $this->logService->error('StripeWebhook', 'Unhandled exception in handle()', [], $e->getTraceAsString());
            $this->sendSuccessResponse(['error' => 'temporary failure'], 500);
        }
    }
}