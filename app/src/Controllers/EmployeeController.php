<?php 
namespace App\Controllers;

use App\Framework\BaseController;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class EmployeeController extends BaseController
{
    private IOrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    #[RequireRole([UserRole::EMPLOYEE])]
    public function validateScan(): void
    {
        $json = json_decode(file_get_contents('php://input'), true);
        $hash = $json['hash'] ?? '';
        if (empty($hash)) {
            $this->jsonResponse(['success' => false, 'message' => 'No scan data provided'], 400);
            return;
        }

        $item = $this->orderService->getOrderItemByHash($hash);

        if (!$item) {
            $this->jsonResponse(['success' => false, 'message' => '❌ TICKET NOT FOUND'], 404);
            return;
        }

        if ($item->is_scanned) {
            if ($item->scanned_at) {
                $ts = strtotime($item->scanned_at);
                $time = date('H:i', $ts);
                $date = date('d M Y', $ts);
            }

            $this->jsonResponse([
                'success'  => false,
                'message'  => '⚠️ Already Scanned',
                'description' => isset($ts) 
                    ? "This ticket was checked in at {$time} on {$date}."
                    : "This ticket has already been used.",
                'scanned_at' => $item->scanned_at,
            ], 400);
            return;
        }

        $this->orderService->markAsScanned($item->orderitem_id);

        $this->jsonResponse([
            'success' => true,
            'message' => "✅ VALID: Check in {$item->quantity} person(s).",
            'description' => $item->ticket_type->description ?? 'Standard Entry'
        ], 200);
    }
    #[RequireRole([UserRole::EMPLOYEE])]
    public function scanPage(): void
    {
        $this->view('Account/Scanner');
    }
}