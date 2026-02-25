<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\ViewModels\Magic\MagicLanding;
use App\ViewModels\Magic\MagicAccessibility;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
class MagicController extends BaseController
{
    private PageService $pageService;

    public function __construct()
    {
        $this->pageService = new PageService(new \App\Repositories\PageRepository());
    }

    public function index($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            if (!$pageData) {
                //$this->notFound();
                //return;
                throw new \Exception("We are sorry, but the page you are looking for cannot be found. Please check the URL and try again.");
            }
            $pageModel = new MagicLanding($pageData);
            $this->view('Magic/Landing', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (\Exception $e) {
            $this->internalServerError("Error loading page: " . $e->getMessage());
        }
    }

    public function accessibility($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            if (!$pageData) {
                $this->notFound();
                return;
            }
            $pageModel = new MagicAccessibility($pageData);
            
            $this->view('Magic/MagicAccessibility', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (\Exception $e) {
            $this->internalServerError("Error loading page: " . $e->getMessage());
        }
        
    }
    public function lorentzFormula($vars = []): void
    {
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            if (!$pageData) {
                $this->notFound();
                return;
            }
            $pageModel = new MagicAccessibility($pageData);
            
            $this->view('Magic/MagicLorentz', ['pageModel' => $pageModel, 'title' => $pageData->title]);
        } catch (\Exception $e) {
            $this->internalServerError("Error loading page: " . $e->getMessage());
        }
        
    }

    
}