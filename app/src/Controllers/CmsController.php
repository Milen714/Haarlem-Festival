<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class CmsController extends BaseController
{
    #[RequireRole([UserRole::ADMIN])]
    public function dashboard($vars = []): void
    {
        $this->cmsLayout('Cms/Dashboard', [
            'title' => 'CMS Dashboard'
        ]);
    }
}