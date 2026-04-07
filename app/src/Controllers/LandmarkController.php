<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Services\Interfaces\ILandmarkService;
use App\Services\Interfaces\ILogService;
use App\Services\LandmarkService;
use App\Services\LogService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Models\Landmark;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;

class LandmarkController extends BaseController
{
    private ILandmarkService $landmarkService;
    private ILogService $logService;

    public function __construct()
    {
        $this->landmarkService = new LandmarkService();
        $this->logService = new LogService();
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index(): void
    {
        try {
            $landmarks = $this->landmarkService->getAllLandmarks();

            $this->cmsLayout('Cms/Landmarks/Index', [
                'title'     => 'Manage Landmarks',
                'landmarks' => $landmarks
            ]);
        } catch (\Throwable $e) {
            $this->logService->exception('Landmark', $e);
            $this->internalServerError();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        $images = $this->prepareImages();

        $this->cmsLayout('Cms/Landmarks/LandmarkForm', [
            'title'    => 'Create New Landmark',
            'landmark' => null,
            'action'   => '/cms/landmarks/store',
            'images'   => $images
        ]);
    }

    private function prepareImages(?Landmark $landmark = null): array
    {
        $slotsSchema = [
            ['label' => 'Introduction',   'name' => 'img_intro'],
            ['label' => 'History',        'name' => 'img_history'],
            ['label' => 'Practical Info', 'name' => 'img_practical']
        ];

        $mediaItems = [];
        if ($landmark !== null && !empty($landmark->gallery) && !empty($landmark->gallery->media_items)) {
            $mediaItems = array_values($landmark->gallery->media_items);
        }

        $imageSlots = [];
        foreach ($slotsSchema as $index => $slot) {
            $imageSlots[] = [
                'label' => $slot['label'],
                'name'  => $slot['name'],
                'media' => $mediaItems[$index]->media ?? null,
            ];
        }

        return $imageSlots;
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []): void
    {
        try {
            $this->landmarkService->createLandmark($_POST, $_FILES);
            $_SESSION['success'] = 'Landmark created successfully.';
            $this->redirect('/cms/landmarks');
        } catch (ValidationException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/landmarks/create');
        } catch (\Throwable $e) {
            $this->logService->exception('Landmark', $e);
            $this->internalServerError();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $id = (int)($vars['id'] ?? 0);
        if ($id <= 0) { $this->notFound(); return; }

        try {
            $landmark = $this->landmarkService->getLandmarkById($id);

            if (!$landmark) {
                $this->notFound();
                return;
            }

            $images = $this->prepareImages($landmark);

            $this->cmsLayout('Cms/Landmarks/LandmarkForm', [
                'title'    => 'Edit Landmark: ' . $landmark->name,
                'landmark' => $landmark,
                'action'   => "/cms/landmarks/update/{$landmark->landmark_id}",
                'images'   => $images
            ]);
        } catch (\Throwable $e) {
            $this->logService->exception('Landmark', $e);
            $this->internalServerError();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $id = (int)($vars['id'] ?? 0);
        if ($id <= 0) { $this->notFound(); return; }

        try {
            $this->landmarkService->updateLandmark($id, $_POST, $_FILES);
            $_SESSION['success'] = 'Landmark updated successfully.';
            $this->redirect('/cms/landmarks');
        } catch (ValidationException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/landmarks/edit/{$id}");
        } catch (ResourceNotFoundException $e) {
            $this->notFound();
        } catch (\Throwable $e) {
            $this->logService->exception('Landmark', $e);
            $this->internalServerError();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {
        $id = (int)($vars['id'] ?? 0);

        try {
            $this->landmarkService->deleteLandmark($id);
            $this->redirect('/cms/landmarks');
        } catch (\Throwable $e) {
            $this->logService->exception('Landmark', $e);
            $_SESSION['error'] = 'Failed to delete landmark.';
            $this->redirect('/cms/landmarks');
        }
    }
}
