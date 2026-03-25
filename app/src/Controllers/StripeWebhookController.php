<?php

namespace App\Controllers;

use App\Models\Enums\OrderStatus;
use App\Models\Payment\Order;
use App\Models\Payment\OrderItem;
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
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;
use Exception;
use App\Services\LogService;

class StripeWebhookController extends BaseController
{
    private IPaymentService $paymentService;
    private IOrderService   $orderService;
    private ITicketService  $ticketService;
    private IMailService    $mailService;
    private ITicketFulfillmentService $ticketFulfillmentService;
    private LogService $logService;


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
                    $this->sendTicketEmail($order);
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
            http_response_code(200);
            echo json_encode(['received' => true]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'temporary failure']);
        }
    }
    private function sendTicketEmail(Order $order): void
    {
        $fileName = $this->ticketFulfillmentService->generatePDFName($order);
        $ticketPdfPath =  __DIR__ .  '/../../public/Assets/documents/' . $fileName . '.pdf';
        try {
            
            if(!isset($order)){
                $order = $this->orderService->createSessionCart();
            }
            $this->orderService->generateTicketHashes($order->order_id);
            $order = $this->orderService->getOrderById($order->order_id);

            $viewModel = new ShoppingCartViewModel($order);
            
            $this->ticketFulfillmentService->generatePDF($this->renderViewToString('Email/TicketsPDF', ['viewModel' => $viewModel]), $fileName);
            
            $this->logService->info('StripeWebhook', 'PDF generated', ['path' => $ticketPdfPath]);

            $mailTo = $order->user->email ?? 'paami97@gmail.com';
            $this->logService->info('StripeWebhook', 'Sending ticket email', ['to' => $mailTo]);

            $this->mailService->sendEmail(
                $mailTo,
                "Your Festival Tickets - " . $order->reference_number,
                $this->renderViewToString('Email/TicketsMailBody', ['viewModel' => $viewModel]),
                [$ticketPdfPath]
            );
            
            $this->logService->info('StripeWebhook', 'Ticket email sent successfully', ['to' => $mailTo]);
            
        } catch (\Throwable $e) {
            $this->logService->error('StripeWebhook', 'Failed to send ticket email', ['to' => $mailTo], $e->getTraceAsString());
        }
    }

}