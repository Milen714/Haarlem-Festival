<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use App\Services\VenueService;
use App\Services\Interfaces\IVenueService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class VenueController extends BaseController
{
    private IVenueService $venueService;
    private ILogService $logService;

    /**
     * Wires up VenueService, which handles all venue CRUD operations including
     * image upload and validation.
     */
    public function __construct()
    {
        $this->venueService = new VenueService();
        $this->logService = new LogService();
    }

    /**
     * Renders the CMS venue listing page showing all venues with their image and event count.
     * On failure, renders the page with an empty list and an error message rather than crashing.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []): void
    {
        try {
            $venues = $this->venueService->getAllVenues();

            $this->cmsLayout('Cms/Venues/Index', [
                'title' => 'Manage Venues',
                'venues' => $venues
            ]);
        } catch (\Throwable $e) {
            $this->logService->exception('Venue', $e);
            $this->cmsLayout('Cms/Venues/Index', [
                'title' => 'Manage Venues',
                'venues' => [],
                'error' => 'Failed to load venues.'
            ]);
        }
    }

    /**
     * Renders the empty venue create form.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        $this->cmsLayout('Cms/Venues/Form', [
            'title' => 'Create New Venue',
            'venue' => null,
            'action' => '/cms/venues/store'
        ]);
    }

    /**
     * Handles the venue create form submission.
     * Passes $_POST and $_FILES to VenueService, then redirects to the listing on success
     * or back to the create form with a session error on failure.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []): void
    {

        try {
            $venue = $this->venueService->createFromRequest($_POST, $_FILES);

            if ($venue) {
                $_SESSION['success'] = "Venue '{$venue->name}' created successfully!";
            }
            $this->redirect('/cms/venues');
        } catch (ValidationException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/venues/create');
        } catch (ApplicationException | \Throwable $e) {
            $this->logService->exception('Venue', $e);
            $_SESSION['error'] = 'Failed to create venue.';
            $this->redirect('/cms/venues/create');
        }
    }

    /**
     * Renders the venue edit form pre-populated with the current venue data.
     * Redirects to the CMS home with an error if the venue does not exist.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the venue's primary key.
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $venueId = (int)($vars['id'] ?? 0);

        try {
            $venue = $this->venueService->getVenueById($venueId);

            if (!$venue) {
                throw new ResourceNotFoundException('Venue not found.');
            }

            $this->cmsLayout('Cms/Venues/Form', [
                'title' => 'Edit Venue: ' . $venue->name,
                'venue' => $venue,
                'action' => "/cms/venues/update/{$venueId}"
            ]);
        } catch (ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/venues');
        } catch (\Throwable $e) {
            $this->logService->exception('Venue', $e);
            $_SESSION['error'] = 'Failed to load venue.';
            $this->redirect('/cms/venues');
        }
    }

    /**
     * Handles the venue update form submission.
     * Delegates to VenueService, then redirects to the listing on success
     * or back to the edit form with a session error on failure.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the venue's primary key.
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $venueId = (int)($vars['id'] ?? 0);

        try {
            $venue = $this->venueService->updateFromRequest($venueId, $_POST, $_FILES);

            if ($venue) {
                $_SESSION['success'] = "Venue '{$venue->name}' updated successfully!";
            }
            $this->redirect('/cms/venues');
        } catch (ValidationException | ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/venues/edit/{$venueId}");
        } catch (ApplicationException | \Throwable $e) {
            $this->logService->exception('Venue', $e);
            $_SESSION['error'] = 'Failed to update venue.';
            $this->redirect("/cms/venues/edit/{$venueId}");
        }
    }

    /**
     * Handles the venue delete action.
     * Fetches the venue first to get its name for the success message, then permanently deletes it.
     * Redirects to the venue listing regardless of outcome.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the venue's primary key.
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {

        $venueId = (int)($vars['id'] ?? 0);

        try {
            $venue = $this->venueService->getVenueById($venueId);

            if (!$venue) {
                throw new ResourceNotFoundException('Venue not found.');
            }

            $venueName = $venue->name;
            $this->venueService->deleteVenue($venueId);

            $_SESSION['success'] = "Venue '{$venueName}' deleted successfully!";
        } catch (ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (\Throwable $e) {
            $this->logService->exception('Venue', $e);
            $_SESSION['error'] = 'Failed to delete venue.';
        }

        $this->redirect('/cms/venues');
    }
}
