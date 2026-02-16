<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\ScheduleService;
use App\Repositories\UserRepository;
use App\Repositories\PageRepository;
use App\Repositories\ScheduleRepository;
use App\Services\PageService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Repositories\MediaRepository;
use App\Services\MediaService;

class HomeController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private PageService $pageService;
    private PageRepository $pageRepository;
    private MediaService $mediaService;
    private MediaRepository $mediaRepository;
    private ScheduleRepository $scheduleRepository;
    private ScheduleService $scheduleService;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->pageRepository = new PageRepository();
        $this->pageService = new PageService($this->pageRepository);
        $this->scheduleRepository = new ScheduleRepository();
        $this->scheduleService = new ScheduleService($this->scheduleRepository);
        $this->mediaRepository = new MediaRepository();
        $this->mediaService = new MediaService($this->mediaRepository);
    }
    public function index($vars = [])
    {
        //header('Content-Type: application/json');
        $pageData = $this->pageService->getPageBySlug('home');
        /**
     * 
     * TODO: For the homepage we will likely want to show a schedule overview of all events, so we can fetch all schedules and pass to view for now. In the future we can enhance this to only show upcoming events or featured events based on some criteria.
     */
        $schedule = $this->scheduleService->getAllSchedules();
        //echo json_encode($schedule);
        $this->view('Home/Landing', ['title' => $pageData->title, 'pageData' => $pageData, 'schedule' => $schedule] );
    }
    #[RequireRole([UserRole::ADMIN])]
    public function adminIndex($vars = [])
    {
        $user = $this->userService->getUserById(5);
        if ($user) {
            $message = "Welcome back, " . $user->fname . "!";
        } else {
            $message = "User not found.";
        }
        $this->cmsLayout('Home/Landing', ['message' => $message, 'title' => 'The Festival Home', 'user' => $user] );
    }
    
    public function setTheme($vars = [])
    {
        header('Content-Type: application/json');
        if (isset($_POST['theme'])) {
            $theme = $_POST['theme'];
            // Set a cookie wit 30 day expiry for the selected theme
            setcookie('theme', $theme, time() + (86400 * 30), '/');
            
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No theme selected']);
        }
    }
    public function wysiwygDemo($vars = [])
    {
        $this->cmsLayout('Cms/WysiwygDemo', ['message' => "Thi", 'title' => 'WYSIWYG Editor Demo', 'param' => $vars['param'] ?? 'noParam'] );
    }
    public function wysiwygDemoPost($vars = [])
    {
        $html = $_POST['content'] ?? '';

        $this->cmsLayout('Cms/WysiwgDemoPreview', ['content' => $html, 'title' => 'WYSIWYG Editor Result'] );
    }
    public function homePage($vars = [])
    {
        header('Content-Type: application/json');
        $slug = ltrim($_SERVER['REQUEST_URI'], '/');
        
        $pageData = $this->pageService->getPageBySlug('events-jazz');
        echo json_encode($pageData);
    }
    public function updateHomePage($vars = [])
    {
        
        $pageData = $this->pageService->getPageBySlug('home');
        $this->cmsLayout('Cms/UpdateHomepage', ['pageData' => $pageData, 'title' => 'Edit Home Page'] );
    }
    public function updateHomePagePost($vars = [])
    {
        // var_dump($_POST);
        // die();
        header('Content-Type: application/json');
        $pageData = new \App\CmsModels\Page();
        $pageData->fromPostData($_POST);
        $success = $this->pageService->updatePage($pageData);
        
        echo json_encode(['success' => $success, 'pageData' => $pageData]);
    }
    public function testJazz($vars = [])
    {
        $media = new \App\CmsModels\Page();
        $this->view('Jazz/index', ['title' => 'Test Jazz Page' , 'message' => "asdaksjfhlkasfj;asjd;kasjklas;LASJDF;ALS"] );
    }
    public function YummyHome($vars = [])
    {
        $this->view('Yummy/HomePage', ['id'=> 1] );
    }
}