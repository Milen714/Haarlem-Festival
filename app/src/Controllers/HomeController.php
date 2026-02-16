<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Repositories\PageRepository;
use App\CmsModels\Enums\PageType;
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

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
        $this->pageRepository = new PageRepository();
        $this->pageService = new PageService($this->pageRepository);
        $this->mediaRepository = new MediaRepository();
        $this->mediaService = new MediaService($this->mediaRepository);
    }

    public function index($vars = [])
    {
        $pageData = $this->pageService->getPageData(PageType::homepage);
        $this->view('Home/Landing', ['title' => $pageData->title, 'pageData' => $pageData]);
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
        $this->cmsLayout('Home/Landing', ['message' => $message, 'title' => 'The Festival Home', 'user' => $user]);
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
        $this->cmsLayout('Cms/WysiwygDemo', ['message' => "Thi", 'title' => 'WYSIWYG Editor Demo', 'param' => $vars['param'] ?? 'noParam']);
    }

    public function wysiwygDemoPost($vars = [])
    {
        $html = $_POST['content'] ?? '';
        $this->cmsLayout('Cms/WysiwgDemoPreview', ['content' => $html, 'title' => 'WYSIWYG Editor Result']);
    }

    public function homePage($vars = [])
    {
        header('Content-Type: application/json');
        $pageData = $this->pageService->getPageData(PageType::homepage);
        echo json_encode($pageData);
    }

    public function updateHomePage($vars = [])
    {
        $pageData = $this->pageService->getPageData(PageType::homepage);
        $this->cmsLayout('Cms/UpdateHomepage', ['pageData' => $pageData, 'title' => 'Edit Home Page']);
    }

    public function updateHomePagePost($vars = [])
    {
        // Start session for messages
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Process section media uploads BEFORE processing post data
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                // Check if it's a section media upload (e.g., "section_media_0")
                if (strpos($key, 'section_media_') === 0 && $file['error'] === UPLOAD_ERR_OK) {
                    $sectionIndex = (int)str_replace('section_media_', '', $key);

                    $existingMediaId = !empty($_POST['sections'][$sectionIndex]['media_id'])
                        ? (int)$_POST['sections'][$sectionIndex]['media_id']
                        : null;

                    $altText = $_POST['sections'][$sectionIndex]['alt_text'] ?? 'Section image';
                    $category = 'Home/Sections'; // ← Your folder structure

                    // Upload or replace
                    if ($existingMediaId) {
                        $result = $this->mediaService->replaceMedia(
                            $existingMediaId,
                            $file,
                            $category,
                            $altText
                        );

                        if (!$result['success']) {
                            $_SESSION['error'] = $result['error'];
                            header('Location: /home-update');
                            exit;
                        }
                    } else {
                        $result = $this->mediaService->uploadAndCreate(
                            $file,
                            $category,
                            $altText
                        );

                        // Update POST data with new media_id and file_path
                        if ($result['success']) {
                            $_POST['sections'][$sectionIndex]['media_id'] = $result['media']->media_id;
                            $_POST['sections'][$sectionIndex]['file_path'] = $result['media']->file_path;
                        } else {
                            $_SESSION['error'] = $result['error'];
                            header('Location: /home-update');
                            exit;
                        }
                    }
                }
            }
        }

        // Continue with existing page update logic
        $pageData = new \App\CmsModels\Page();
        $pageData->fromPostData($_POST);
        $success = $this->pageService->updatePage($pageData);

        if ($success) {
            $_SESSION['success'] = 'Page updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update page.';
        }

        header('Location: /home-update');
        exit;
    }

    public function testJazz($vars = [])
    {
        $media = new \App\CmsModels\Page();
        $this->view('Jazz/index', ['title' => 'Test Jazz Page', 'message' => "asdaksjfhlkasfj;asjd;kasjklas;LASJDF;ALS"]);
    }

    public function YummyHome($vars = [])
    {
        $this->view('Yummy/HomePage', ['id' => 1]);
    }
}