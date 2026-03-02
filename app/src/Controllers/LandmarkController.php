<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\LandmarkService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class LandmarkController extends BaseController
{
    private LandmarkService $landmarkService;

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
        
        $this->cmsLayout('Cms/Landmarks/Form', [
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
            // Pasamos $_POST (textos) y $_FILES (imágenes) al servicio
            $landmark = $this->landmarkService->createLandmark($_POST, $_FILES);
                        
            $this->redirect('/cms/landmarks');
            
        } 
        catch (\Exception $e) {
            error_log("Landmark create error: " . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void //show the edit view for the user
    {
        $this->startSession();
      
        $slug = $vars['slug'] ?? '';

        try {
            $landmark = $this->landmarkService->getLandmarkBySlug($slug);

            if (!$landmark) {
                $this->redirect('/cms/landmarks');
                return;
            }

            // Reutilizamos la MISMA vista del formulario, pero le pasamos los datos
            $this->cmsLayout('Cms/Landmarks/Form', [
                'title' => 'Edit Landmark: ' . $landmark->name,
                'landmark' => $landmark,
                'action' => "/cms/landmarks/update/{$slug}"
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

        $slug = $vars['slug'] ?? '';

        try {
            $landmark = $this->landmarkService->updateLandmark($slug, $_POST, $_FILES);
            
            $this->redirect('/cms/landmarks');
            
        } 
        catch (\Exception $e) {
            error_log("Landmark update error: " . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void //delete landmark 
    {
        $this->startSession();
        $slug = $vars['slug'] ?? '';

        try {
            $this->landmarkService->deleteLandmark($slug);
                        
        } 
        catch (\Exception $e) {
            error_log("Landmark delete error: " . $e->getMessage());
        }

        $this->redirect('/cms/landmarks');
    }

}