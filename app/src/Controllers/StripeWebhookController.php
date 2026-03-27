<?php

namespace App\Controllers;

use App\Models\Enums\OrderStatus;
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

                    $pdf_path = $this->sendTicketEmail($order); // Generate PDF and send email with tickets

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
            $this->jsonResponse(['received' => true], 200);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'temporary failure'], 500);
        }
    }
    private function sendTicketEmail(Order $order): string
    {
        $fileName      = $this->ticketFulfillmentService->generatePDFName($order);
        $ticketPdfPath = $this->ticketFulfillmentService->getTicketPdfPath($fileName . '.pdf');
        $mailTo        = $order->user->email ?? 'paami97@gmail.com';

        try {
            $this->orderService->generateTicketHashes($order->order_id);
            $order = $this->orderService->getOrderById($order->order_id);

            $mailTo    = $order->user->email ?? $mailTo;
            $viewModel = new ShoppingCartViewModel($order);

            $this->ticketFulfillmentService->generatePDF(
                $this->renderViewToString('Email/TicketsPDF', ['viewModel' => $viewModel]),
                $fileName,
                'A4',
                'landscape',
                false,  // never stream PDF to HTTP response from the webhook
                true    // always save to disk
            );

            $this->logService->info('StripeWebhook', 'PDF generated', ['path' => $ticketPdfPath]);

            $this->mailService->sendEmail(
                $mailTo,
                "Your Festival Tickets - " . $order->reference_number,
                $this->renderViewToString('Email/TicketsMailBody', ['viewModel' => $viewModel]),
                [$ticketPdfPath]
            );
        } catch (\Throwable $e) {
            $this->logService->error('StripeWebhook', 'Failed to send ticket email', ['to' => $mailTo], $e->getTraceAsString());
        }

        return $fileName . '.pdf';
    }
}
