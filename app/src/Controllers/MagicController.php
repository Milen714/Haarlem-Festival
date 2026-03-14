<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Services\Interfaces\IPageService;
use App\ViewModels\Magic\MagicAccessibility;
class MagicController extends BaseController
{
    private IPageService $pageService;

    public function __construct()
    {
        $this->pageService = new PageService();
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
            $pageModel = new MagicAccessibility($pageData);
            $this->View('Magic/MagicLandingRef', ['pageModel' => $pageModel, 'title' => $pageData->title]);
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