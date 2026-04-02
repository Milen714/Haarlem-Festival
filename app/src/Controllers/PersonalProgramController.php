<?php
namespace App\Controllers;

use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\Interfaces\ITicketService;
use App\Services\TicketService;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Services\Interfaces\ITicketFulfillmentService;
use App\Services\TicketFulfillmentService;
use App\Models\Payment\OrderItem;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UserFacingException;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;
use App\ViewModels\ShoppingCart\PaidTicketsViewModel;

class PersonalProgramController extends BaseController
{
    private ITicketService $ticketService;
    private IOrderService $orderService;
    private ITicketFulfillmentService $ticketFulfillmentService;
    private ILogService $logService;

    public function __construct() {
        $this->ticketService = new TicketService();
        $this->orderService = new OrderService();
        $this->ticketFulfillmentService = new TicketFulfillmentService();
        $this->logService = new LogService();
    }

    /**
     * Display user's personal program (paid tickets)
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function personalProgram()
    {
        $date = $_GET['date'] ?? null; // Optional date filter in 'Y-m-d' format
        $showMyTicketsSection = ($_GET['showMyTicketSection'] ?? 'false') === 'true'; // Read section preference
        
        try {
            // Validate user is logged in
            $userId = $this->getLoggedInUser()?->id;
            if (!$userId) {
                $this->notFound();
                return;
            }

            // Validate date format if provided
            if ($date !== null) {
                if (!$this->isValidDateFormat($date)) {
                    throw new ValidationException('Invalid date format. Expected Y-m-d format.');
                }
            }

            $paidOrders = $this->orderService->getPaidTicketsByUser($userId, $date);
            $viewModel = new PaidTicketsViewModel($paidOrders, $date, $showMyTicketsSection);
            
            $this->view('ShoppingCart/PersonalProgram', ['viewModel' => $viewModel]);
        } catch (ValidationException $e) {
            $this->logService->info('PersonalProgram', 'Validation error: ' . $e->getMessage());
            $this->view('ShoppingCart/PersonalProgram', ['viewModel' => null, 'error' => $e->getMessage()]);
        } catch (UserFacingException $e) {
            $this->logService->info('PersonalProgram', 'User-facing error: ' . $e->getMessage());
            $this->view('ShoppingCart/PersonalProgram', ['viewModel' => null, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->logService->exception('PersonalProgram', $e);
            $this->view('ShoppingCart/PersonalProgram', ['viewModel' => null, 'error' => 'An error occurred while loading your personal program. Please try again later.']);
        }
    }

    /**
     * Validate if a string is in Y-m-d format and is a valid date
     */
    private function isValidDateFormat(string $date): bool
    {
        $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($datePattern, $date)) {
            return false;
        }

        // Check if it's a valid date
        $parts = explode('-', $date);
        return checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]);
    }

}