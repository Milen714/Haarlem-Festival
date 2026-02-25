<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\PageService;
use App\Repositories\PageRepository;

class CmsController extends BaseController
{
    private PageService $pageService;
    
    public function __construct()
    {
        $this->pageService = new PageService(new PageRepository());
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