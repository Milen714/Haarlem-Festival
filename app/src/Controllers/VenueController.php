<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\VenueService;
use App\Services\MediaService;
use App\Repositories\VenueRepository;
use App\Repositories\MediaRepository;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class VenueController extends BaseController
{
    private VenueService $venueService;

    public function __construct()
    {
        $venueRepository = new VenueRepository();
        $mediaService = new MediaService(new MediaRepository());
        $this->venueService = new VenueService($venueRepository, $mediaService);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []): void
    {
        try {
            $venues = $this->venueService->getAllVenues();
            
            $this->cmsLayout('Cms/Venues/Index', [
                'title' => 'Manage Venues',
                'venues' => $venues
            ]);
        } catch (\Exception $e) {
            error_log("Venue list error: " . $e->getMessage());
            // Don't redirect - show error on the same page
            $this->cmsLayout('Cms/Venues/Index', [
                'title' => 'Manage Venues',
                'venues' => [],
                'error' => 'Failed to load venues: ' . $e->getMessage()
            ]);
        }
    }
    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        $this->cmsLayout('Cms/Venues/Form', [
            'title' => 'Create New Venue',
            'venue' => null,
            'action' => '/cms/venues/store'
        ]);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []): void
    {
        $this->startSession();

        try {
            $venue = $this->venueService->createFromRequest($_POST, $_FILES);
            
            $_SESSION['success'] = "Venue '{$venue->name}' created successfully!";
            $this->redirect('/cms/venues');
            
        } catch (\Exception $e) {
            error_log("Venue create error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/venues/create');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $venueId = (int)($vars['id'] ?? 0);

        try {
            $venue = $this->venueService->getVenueById($venueId);

            if (!$venue) {
                $this->handleError('Venue not found');
                return;
            }

            $this->cmsLayout('Cms/Venues/Form', [
                'title' => 'Edit Venue: ' . $venue->name,
                'venue' => $venue,
                'action' => "/cms/venues/update/{$venueId}"
            ]);

        } catch (\Exception $e) {
            error_log("Venue edit error: " . $e->getMessage());
            $this->handleError('Failed to load venue: ' . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $this->startSession();
        $venueId = (int)($vars['id'] ?? 0);

        try {
            $venue = $this->venueService->updateFromRequest($venueId, $_POST, $_FILES);
            
            $_SESSION['success'] = "Venue '{$venue->name}' updated successfully!";
            $this->redirect('/cms/venues');
            
        } catch (\Exception $e) {
            error_log("Venue update error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/venues/edit/{$venueId}");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {
        $this->startSession();
        $venueId = (int)($vars['id'] ?? 0);

        try {
            $venue = $this->venueService->getVenueById($venueId);
            
            if (!$venue) {
                throw new \Exception('Venue not found');
            }

            $venueName = $venue->name;
            $this->venueService->deleteVenue($venueId);
            
            $_SESSION['success'] = "Venue '{$venueName}' deleted successfully!";
            
        } catch (\Exception $e) {
            error_log("Venue delete error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/cms/venues');
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


        private function handleError(string $message): void
    {
        $this->startSession();
        $_SESSION['error'] = $message;
        // Instead of redirecting to /cms/venues, go to dashboard
        $this->redirect('/cms');
    }
}