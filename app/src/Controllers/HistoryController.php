<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Repositories\PageRepository;
use App\CmsModels\Enums\PageType;

class HistoryController extends BaseController
{
    private PageService $pageService;

    public function __construct()
    {
        $this->pageService = new PageService(new PageRepository());
    }

    // public function index($vars = [])
    // {
    //     $pageData = $this->pageService->getPageData(PageType::homepage); 

    //     $this->view('History/HistoryHomepage', [
    //         'title' => 'Haarlem History',
    //         'pageData' => $pageData
    //     ]);
    // }
}