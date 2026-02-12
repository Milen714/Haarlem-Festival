<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Repositories\HomePageRepository;
use App\CmsModels\Enums\TheFestivalPageType;
use App\Services\HomePageService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Repositories\MediaRepository;
use App\Services\MediaService;

class HomeController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private HomePageService $homePageService;
    private HomePageRepository $homePageRepository;
    private MediaService $mediaService;
    private MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->homePageRepository = new HomePageRepository();
        $this->homePageService = new HomePageService($this->homePageRepository);
        $this->mediaRepository = new MediaRepository();
        $this->mediaService = new MediaService($this->mediaRepository);
    }
    public function index($vars = [])
    {
        $pageData = $this->homePageService->getPageData(TheFestivalPageType::homepage);
        $this->view('Home/Landing', ['title' => 'The Festival Home', 'pageData' => $pageData] );
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
        
        $pageData = $this->homePageService->getPageData(TheFestivalPageType::homepage);
        echo json_encode($pageData);
    }
    public function updateHomePage($vars = [])
    {
        
        $pageData = $this->homePageService->getPageData(TheFestivalPageType::homepage);
        $this->cmsLayout('Cms/UpdateHomepage', ['pageData' => $pageData, 'title' => 'Edit Home Page'] );
    }
    public function updateHomePagePost($vars = [])
    {
        // var_dump($_POST);
        // die();
        header('Content-Type: application/json');
        $pageData = new \App\CmsModels\TheFestivalPage();
        $pageData->fromPostData($_POST);
        $success = $this->homePageService->updatePage($pageData);
        
        echo json_encode(['success' => $success, 'pageData' => $pageData]);
    }
    public function testJazz($vars = [])
    {
        $media = new \App\CmsModels\TheFestivalPage();
        $this->view('Jazz/index', ['title' => 'Test Jazz Page' , 'message' => "asdaksjfhlkasfj;asjd;kasjklas;LASJDF;ALS"] );
    }
    public function YummyHome($vars = [])
    {
        $this->view('Yummy/HomePage', ['id'=> 1] );
    }
}