<?php
namespace App\Controllers;

use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Models\Payment\OrderItem;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UserFacingException;
use App\Services\Interfaces\ILogService;
use App\Services\LogService;
use App\Services\Interfaces\IScheduleService;
use App\Services\ScheduleService;
use App\ViewModels\ShoppingCart\PaidTicketsViewModel;
use App\Services\Interfaces\IUserService;
use App\Services\UserService;

class PersonalProgramController extends BaseController
{
    private IOrderService $orderService;
    private ILogService $logService;
    private IScheduleService $scheduleService;
    private IUserService $userService;

    public function __construct() {
        $this->orderService = new OrderService();
        $this->logService = new LogService();
        $this->scheduleService = new ScheduleService();
        $this->userService = new UserService();
    }

    /**
     * Display user's personal program (paid tickets)
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function personalProgram()
    {
        $date = ($_GET['date'] ?? null) ?: null; // Optional date filter in 'Y-m-d' format, empty string treated as null
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
            $allPaidOrders = ($date !== null) ? $this->orderService->getPaidTicketsByUser($userId, null) : null;
            $viewModel = new PaidTicketsViewModel($paidOrders, $date, $showMyTicketsSection, $allPaidOrders);
            $availableDates = $this->scheduleService->getAvailableDates();

            $this->view('ShoppingCart/PersonalProgram', ['viewModel' => $viewModel, 'availableDates' => $availableDates]);
        } catch (ValidationException | UserFacingException $e) {
            $this->logService->info('PersonalProgram', $e->getMessage());
            $this->view('ShoppingCart/PersonalProgram', ['viewModel' => null, 'availableDates' => [], 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->logService->exception('PersonalProgram', $e);
            $this->view('ShoppingCart/PersonalProgram', ['viewModel' => null, 'availableDates' => [], 'error' => 'An error occurred while loading your personal program. Please try again later.']);
        }
    }

    /**
     * Returns only the program content partial, no layout
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function programContent()
    {
        $date = ($_GET['date'] ?? null) ?: null;
        $userId = $this->getLoggedInUser()?->id;
        if (!$userId) { http_response_code(401); return; }

        try {
            if ($date !== null && !$this->isValidDateFormat($date)) {
                throw new ValidationException('Invalid date format.');
            }
            $paidOrders = $this->orderService->getPaidTicketsByUser($userId, $date);
            $viewModel = new PaidTicketsViewModel($paidOrders, $date, false);
            echo $this->renderViewToString('ShoppingCart/Partials/MyProgramPartial', ['viewModel' => $viewModel]);
        } catch (ValidationException $e) {
            http_response_code(400);
            echo '<p class="text-red-500">Invalid date.</p>';
        } catch (\Exception $e) {
            $this->logService->exception('PersonalProgram', $e);
            http_response_code(500);
            echo '<p class="text-red-500">An error occurred.</p>';
        }
    }

    /**
     * Validate if a string is in Y-m-d format and is a valid date
     */
    private function isValidDateFormat(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Generates share token for the logged-in user or returns the existing one
     */
    #[RequireRole([UserRole::ADMIN, UserRole::CUSTOMER, UserRole::EMPLOYEE])]
    public function generateShareToken(array $_params = [])
    {
        $sessionUser = $this->getLoggedInUser();
        if (!$sessionUser) {
            $this->sendErrorResponse('Unauthorized', 401);
            return;
        }

        try {
            $paidOrders = $this->orderService->getPaidTicketsByUser($sessionUser->id);
            if (empty($paidOrders)) {
                $this->sendErrorResponse('You have no paid tickets to share.', 400);
                return;
            }

            // Re-fetch from DB to get current share_token state
            $user = $this->userService->getUserById($sessionUser->id);
            if (!$user->share_token) {
                $token = bin2hex(random_bytes(24));
                $this->userService->saveShareToken($user->id, $token);
                $user->share_token = $token;
            }

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $url = $scheme . '://' . $host . '/shared-program/' . $user->share_token;

            $this->sendSuccessResponse(['url' => $url]);
        } catch (\Exception $e) {
            $this->logService->exception('PersonalProgram', $e);
            $this->sendErrorResponse('Could not generate share link. Please try again.', 500);
        }
    }

    /**
     * Public view of a shared program
     */
    public function sharedProgram(array $params = [])
    {
        $token = $params['token'] ?? null;
        if (!$token) {
            $this->notFound();
            return;
        }

        try {
            $user = $this->userService->findByShareToken($token);
            if (!$user) {
                $this->notFound();
                return;
            }

            $paidOrders = $this->orderService->getPaidTicketsByUser($user->id);
            $viewModel = new PaidTicketsViewModel($paidOrders, null, false);

            $this->view('ShoppingCart/SharedProgram', [
                'viewModel' => $viewModel,
                'sharedByUser' => $user,
            ]);
        } catch (\Exception $e) {
            $this->logService->exception('PersonalProgram', $e);
            $this->internalServerError();
        }
    }


}