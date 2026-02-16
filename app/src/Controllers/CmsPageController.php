<?php

namespace App\Controllers\CMS;

use App\Controllers\BaseController;
use App\Services\PageService;
use App\Services\MediaService;
use App\Repositories\PageRepository;
use App\Repositories\MediaRepository;
use App\CmsModels\Enums\PageType;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class CmsPageController extends BaseController
{
    private PageService $pageService;
    private MediaService $mediaService;

    public function __construct()
    {
        $this->pageService = new PageService(new PageRepository());
        $this->mediaService = new MediaService(new MediaRepository());
    }

    /**
     * Show page editor by slug
     * 
     * @param array $vars ['slug' => 'home', 'events-jazz', 'events-dance', etc.]
     */
    #[RequireRole([UserRole::ADMIN])]
    public function editBySlug($vars = []): void
    {
        $slug = $vars['slug'] ?? 'home';

        try {
            // Get page data by slug
            $pageData = $this->pageService->getPageBySlug($slug);

            if (!$pageData) {
                $this->handleError("Page not found: {$slug}");
                return;
            }

            // Get configuration based on slug
            $config = $this->getPageConfigBySlug($slug, $pageData);

            $this->cmsLayout('Cms/UpdatePage', [
                'pageData' => $pageData,
                'pageTitle' => $config['pageTitle'],
                'submitUrl' => $config['submitUrl'],
                'backUrl' => $config['backUrl'],
                'uploadCategory' => $config['uploadCategory'],
                'slug' => $slug,
                'title' => $config['pageTitle']
            ]);
        } catch (\Exception $e) {
            $this->handleError("Error loading page: " . $e->getMessage());
        }
    }

    /**
     * Handle page update submission
     */
    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate POST data
        if (empty($_POST['page_id']) || empty($_POST['slug'])) {
            $_SESSION['error'] = 'Invalid form submission';
            $this->redirectBack();
            return;
        }

        $slug = $_POST['slug'];

        try {
            // Determine upload category from slug
            $uploadCategory = $this->getUploadCategoryBySlug($slug);

            // Process file uploads
            $uploadResult = $this->processFileUploads($uploadCategory);

            if (!$uploadResult['success']) {
                $_SESSION['error'] = $uploadResult['error'];
                $this->redirectBack();
                return;
            }

            // Update page content
            $updateResult = $this->updatePageContent();

            if ($updateResult['success']) {
                $_SESSION['success'] = 'Page updated successfully!';
            } else {
                $_SESSION['error'] = $updateResult['error'];
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Update failed: " . $e->getMessage();
        }

        // Redirect back to edit page
        header("Location: /cms/page/edit/{$slug}");
        exit;
    }

    /**
     * Process all file uploads in the request
     */
    private function processFileUploads(string $category): array
    {
        if (empty($_FILES)) {
            return ['success' => true, 'error' => null];
        }

        foreach ($_FILES as $key => $file) {
            if (strpos($key, 'section_media_') === 0 && $file['error'] === UPLOAD_ERR_OK) {
                $result = $this->processSectionMediaUpload($key, $file, $category);

                if (!$result['success']) {
                    return $result;
                }
            }
        }

        return ['success' => true, 'error' => null];
    }

    /**
     * Process a single section media upload
     */
    private function processSectionMediaUpload(string $key, array $file, string $category): array
    {
        $sectionIndex = (int)str_replace('section_media_', '', $key);

        if (!isset($_POST['sections'][$sectionIndex])) {
            return ['success' => false, 'error' => "Section {$sectionIndex} not found"];
        }

        $existingMediaId = !empty($_POST['sections'][$sectionIndex]['media_id'])
            ? (int)$_POST['sections'][$sectionIndex]['media_id']
            : null;

        $altText = $_POST['sections'][$sectionIndex]['alt_text'] ?? 'Section image';

        try {
            if ($existingMediaId) {
                // Replace existing media
                $result = $this->mediaService->replaceMedia(
                    $existingMediaId,
                    $file,
                    $category,
                    $altText
                );

                if (!$result['success']) {
                    return ['success' => false, 'error' => "Failed to replace image: {$result['error']}"];
                }
            } else {
                // Create new media
                $result = $this->mediaService->uploadAndCreate(
                    $file,
                    $category,
                    $altText
                );

                if ($result['success']) {
                    $_POST['sections'][$sectionIndex]['media_id'] = $result['media']->media_id;
                } else {
                    return ['success' => false, 'error' => "Failed to upload image: {$result['error']}"];
                }
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => "Upload error: {$e->getMessage()}"];
        }

        return ['success' => true, 'error' => null];
    }

    /**
     * Update page content in database
     */
    private function updatePageContent(): array
    {
        try {
            $pageData = new \App\CmsModels\Page();
            $pageData->fromPostData($_POST);

            $success = $this->pageService->updatePage($pageData);

            if (!$success) {
                return ['success' => false, 'error' => 'Failed to save page content'];
            }

            return ['success' => true, 'error' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => "Database error: {$e->getMessage()}"];
        }
    }

    /**
     * Get configuration based on slug
     */
    private function getPageConfigBySlug(string $slug, $pageData): array
    {
        // Map slugs to configurations
        $configs = [
            'home' => [
                'pageTitle' => 'Edit Homepage',
                'submitUrl' => '/cms/page/update',
                'backUrl' => '/cms',
                'uploadCategory' => 'Home/Sections'
            ],
            'events-jazz' => [
                'pageTitle' => 'Edit Jazz Event Page',
                'submitUrl' => '/cms/page/update',
                'backUrl' => '/cms',
                'uploadCategory' => 'Jazz/JazzHome'
            ],
            'events-dance' => [
                'pageTitle' => 'Edit Dance Event Page',
                'submitUrl' => '/cms/page/update',
                'backUrl' => '/cms',
                'uploadCategory' => 'Dance/DanceHome'
            ],
            'events-history' => [
                'pageTitle' => 'Edit History Event Page',
                'submitUrl' => '/cms/page/update',
                'backUrl' => '/cms',
                'uploadCategory' => 'History/Images'
            ],
            'events-yummy' => [
                'pageTitle' => 'Edit Yummy Event Page',
                'submitUrl' => '/cms/page/update',
                'backUrl' => '/cms',
                'uploadCategory' => 'Yummy/Restaurants'
            ],
            'events-magic' => [
                'pageTitle' => 'Edit Magic Event Page',
                'submitUrl' => '/cms/page/update',
                'backUrl' => '/cms',
                'uploadCategory' => 'Magic/Shows'
            ],
            'history-tour' => [
                'pageTitle' => 'Edit History Tour Page',
                'submitUrl' => '/cms/page/update',
                'backUrl' => '/cms/page/edit/events-history',
                'uploadCategory' => 'History/Events'
            ]
        ];

        // Return config or default
        return $configs[$slug] ?? [
            'pageTitle' => 'Edit ' . $pageData->title,
            'submitUrl' => '/cms/page/update',
            'backUrl' => '/cms',
            'uploadCategory' => 'Home/Content'
        ];
    }

    /**
     * Get upload category based on slug
     */
    private function getUploadCategoryBySlug(string $slug): string
    {
        $categories = [
            'home' => 'Home/Sections',
            'events-jazz' => 'Jazz/JazzHome',
            'events-dance' => 'Dance/DanceHome',
            'events-history' => 'History/Images',
            'events-yummy' => 'Yummy/Restaurants',
            'events-magic' => 'Magic/Shows',
            'history-tour' => 'History/Events'
        ];

        return $categories[$slug] ?? 'Home/Content';
    }

    /**
     * Handle errors gracefully
     */
    private function handleError(string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['error'] = $message;
        header('Location: /cms');
        exit;
    }

    /**
     * Redirect back to referring page
     */
    private function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/cms';
        header("Location: {$referer}");
        exit;
    }
}