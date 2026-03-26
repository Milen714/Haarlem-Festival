<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Services\Interfaces\ILandmarkService;
use App\Services\LandmarkService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Models\Landmark;

class LandmarkController extends BaseController
{
    private ILandmarkService $landmarkService;

    public function __construct()
    {        
        $this->landmarkService = new LandmarkService();
    }
    
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index(): void {
        $this->startSession();
        
        $landmarks = $this->landmarkService->getAllLandmarks();
    
        $this->cmsLayout('Cms/Landmarks/Index', [
            'title' => 'Manage Landmarks',
            'landmarks' => $landmarks
        ]);
    }

    #[RequireRole([UserRole::ADMIN])] //show the view for the user 
    public function create($vars = []): void
    {
        $this->startSession();

        $images = $this->prepareImages();
        
        $this->cmsLayout('Cms/Landmarks/LandmarkForm', [
            'title' => 'Create New Landmark',
            'landmark' => null, 
            'action' => '/cms/landmarks/store',
            'images' => $images
        ]);
    }

    private function prepareImages(?Landmark $landmark = null): array
    {
    $slotsSchema = [
        ['label' => 'Introduction', 'name' => 'img_intro'],
        ['label' => 'History',      'name' => 'img_history'],
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

    #[RequireRole([UserRole::ADMIN])] //post the creation of the landmark
    public function store($vars = []): void
    {
        $this->startSession();

        try {
            $this->landmarkService->createLandmark($_POST, $_FILES);
                        
            $this->redirect('/cms/landmarks');
            exit();
            
        } 
        catch (\Exception $e) {
            error_log("Landmark create error: " . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void //show the edit view for the user
    {
        $this->startSession();
      
        $id = $vars['id'] ?? '';

        try {
            $landmark = $this->landmarkService->getLandmarkById($id);

            $images = $this->prepareImages($landmark);       

            if (!$landmark) {
                $this->redirect('/cms/landmarks');
                return;
            }

            $this->cmsLayout('Cms/Landmarks/LandmarkForm', [
                'title' => 'Edit Landmark: ' . $landmark->name,
                'landmark' => $landmark,
                'action' => "/cms/landmarks/update/{$landmark->landmark_id}",
                'images' => $images
            ]);


        } 
        catch (\Exception $e) {
            error_log("Landmark edit error: " . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])] //post updated landmark
    public function update($vars = []): void
    {
        $this->startSession();

        $id = $vars['id'] ?? '';

        try {
            $landmark = $this->landmarkService->updateLandmark($id, $_POST, $_FILES);
            
            $this->redirect('/cms/landmarks');
            
        } 
        catch (\Exception $e) {
            error_log("Landmark update error: " . $e->getMessage());
            die("An error occurred while updating the landmark: " . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void //delete landmark 
    {
       $this->startSession();
        $id = $vars['id'] ?? ''; 

        try {
            $this->landmarkService->deleteLandmark($id);
        } 
        catch (\Exception $e) {
            error_log("Landmark delete error: " . $e->getMessage());
        }

        $this->redirect('/cms/landmarks');
    }

}