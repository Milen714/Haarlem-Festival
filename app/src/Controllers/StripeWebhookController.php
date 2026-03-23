<?php

namespace App\Controllers;

use App\Models\Enums\OrderStatus;
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
use Exception;

class StripeWebhookController
{
    private IPaymentService $paymentService;
    private IOrderService   $orderService;
    private ITicketService  $ticketService;
    private IMailService    $mailService;
    private ITicketFulfillmentService $ticketFulfillmentService;


    public function __construct()
    {
        $this->paymentService = new PaymentService();
        $this->orderService   = new OrderService();
        $this->ticketService  = new TicketService();
        $this->mailService    = new MailService();
        $this->ticketFulfillmentService = new TicketFulfillmentService();
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
                    $this->orderService->updateOrderStatus($order->order_id, OrderStatus::Paid);
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
}