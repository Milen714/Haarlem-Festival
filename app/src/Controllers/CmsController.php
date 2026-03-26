<?php

namespace App\Controllers;

use App\Framework\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\PageService;
use App\Services\Interfaces\IPageService;

class CmsController extends BaseController
{
    private IPageService $pageService;
    
    public function __construct()
    {
        $this->pageService = new PageService();
    }
    #[RequireRole([UserRole::ADMIN])]
    public function dashboard($vars = []): void
    {
        $pageSlugs = $this->pageService->getPageSlugs();
        
        $this->cmsLayout('Cms/Dashboard', [
            'title' => 'CMS Dashboard',
            'pageSlugs' => $pageSlugs
        ]);
    }
}