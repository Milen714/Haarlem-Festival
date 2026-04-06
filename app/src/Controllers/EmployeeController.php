<?php 
namespace App\Controllers;

use App\Framework\BaseController;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ApplicationException;
use App\Exceptions\ValidationException;

class EmployeeController extends BaseController
{
    private IOrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    #[RequireRole([UserRole::EMPLOYEE, UserRole::ADMIN])]
    public function validateScan(): void
    {
        try {
            $json = $this->getPostData();
            if (!$json) {
                throw new ValidationException('Invalid JSON input received');
            }

            $hash = $json['hash'] ?? '';
            if (empty($hash) || !is_string($hash)) {
                throw new ValidationException('Valid scan hash is required');
            }

            $item = $this->orderService->getOrderItemByHash($hash);
            if (!$item) {
                error_log("Scan validation failed: Ticket hash not found - {$hash}");
                throw new ResourceNotFoundException('Ticket not found');
            }

            if ($item->is_scanned) {
                $scanInfo = $this->formatScanInfo($item->scanned_at);
                
                error_log("Duplicate scan attempt for ticket ID {$item->orderitem_id}");
                $this->sendSuccessResponse([
                    'success'  => false,
                    'message'  => '⚠️ Already Scanned',
                    'description' => $scanInfo['description'],
                    'scanned_at' => $item->scanned_at,
                    'scan_time' => $scanInfo['time'],
                    'scan_date' => $scanInfo['date']
                ], 400);
                return;
            }

            $this->orderService->markAsScanned($item->orderitem_id);
            
            error_log("Ticket successfully scanned: ID {$item->orderitem_id}, Quantity: {$item->quantity}");
            
            $this->sendSuccessResponse([
                'success' => true,
                'message' => "✅ VALID: Check in {$item->quantity} person(s).",
                'description' => $item->ticket_type->description ?? 'Standard Entry',
                'ticket_id' => $item->orderitem_id,
                'quantity' => $item->quantity
            ], 200);

        } catch (ValidationException $e) {
            error_log("Validation error in scan validation: " . $e->getMessage());
            $this->sendSuccessResponse([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        } catch (ResourceNotFoundException $e) {
            error_log("Resource not found in scan validation: " . $e->getMessage());
            $this->sendSuccessResponse([
                'success' => false, 
                'message' => '❌ TICKET NOT FOUND'
            ], 404);
        } catch (ApplicationException $e) {
            error_log("Application error in scan validation: " . $e->getMessage());
            $this->sendSuccessResponse([
                'success' => false, 
                'message' => 'System error occurred. Please try again.'
            ], 500);
        } catch (\Throwable $e) {
            error_log("Unexpected error in scan validation: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
            $this->sendSuccessResponse([
                'success' => false, 
                'message' => 'An unexpected error occurred while validating the ticket.'
            ], 500);
        }
    }

    private function formatScanInfo(?string $scannedAt): array
    {
        if (!$scannedAt) {
            return [
                'description' => 'This ticket has already been used.',
                'time' => null,
                'date' => null
            ];
        }

        try {
            $ts = strtotime($scannedAt);
            $time = date('H:i', $ts);
            $date = date('d M Y', $ts);
            
            return [
                'description' => "This ticket was checked in at {$time} on {$date}.",
                'time' => $time,
                'date' => $date
            ];
        } catch (\Exception $e) {
            error_log("Error formatting scan info: " . $e->getMessage());
            return [
                'description' => 'This ticket has already been used.',
                'time' => null,
                'date' => null
            ];
        }
    }

    #[RequireRole([UserRole::EMPLOYEE, UserRole::ADMIN])]
    public function scanPage(): void
    {
        try {
            $this->view('Account/Scanner');
        } catch (\Exception $e) {
            error_log("Error loading scanner page: " . $e->getMessage());
            throw new ApplicationException('Failed to load scanner page. Please try again later.', 0, $e);
        }
    }
}