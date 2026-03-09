<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Interfaces\ILandmarkService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class LandmarkController extends BaseController
{
    private ILandmarkService $landmarkService;

    public function __construct()
    {        
        $this->landmarkService = new ILandmarkService();
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
        
        $this->cmsLayout('Cms/Landmarks/LandmarkForm', [
            'title' => 'Create New Landmark',
            'landmark' => null, 
            'action' => '/cms/landmarks/store' 
        ]);
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

            if (!$landmark) {
                $this->redirect('/cms/landmarks');
                return;
            }

            $this->cmsLayout('Cms/Landmarks/LandmarkForm', [
                'title' => 'Edit Landmark: ' . $landmark->name,
                'landmark' => $landmark,
                'action' => "/cms/landmarks/update/{$landmark->landmark_id}"
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