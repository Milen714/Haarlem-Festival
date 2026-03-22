<?php 
namespace App\Controllers;

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
        ob_start(); 
        $json = json_decode(file_get_contents('php://input'), true);
        $hash = $json['hash'] ?? '';
         ob_end_clean();
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
            $time = date('H:i on d M', strtotime($item->scanned_at));
            $this->jsonResponse([
                'success' => false, 
                'message' => "⚠️ ALREADY SCANNED at $time"
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