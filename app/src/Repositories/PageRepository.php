<?php
namespace App\Repositories;

use App\CmsModels\CmsPageModel;
use App\CmsModels\PageSection;
use App\CmsModels\Enums\PageType;
use App\CmsModels\Page;
use App\Framework\Repository;
use App\Repositories\Interfaces\IPageRepository;
use App\Repositories\MediaRepository;
use App\Models\EventCategory;
use PDO;
use PDOException;

class PageRepository extends Repository implements IPageRepository
{
    private MediaRepository $mediaRepository; 

    public function __construct() 
    { 
        parent::__construct(); 
        $this->mediaRepository = new MediaRepository(); 
    }

    public function getPageData(PageType $type): Page
    {
        try {
            $pdo = $this->connect();
            $query = "SELECT 
                p.page_id, p.page_type, p.slug, p.title AS page_title, p.sidebar_html, p.event_category_id,
                s.section_id, s.section_type, s.title AS section_title, s.content_html, 
                s.media_id, s.display_order AS sec_order, s.cta_text, s.cta_url, s.gallery_id, 
                ec.event_id AS event_category_id, ec.title AS event_category_title,
                ec.type AS event_category_type, ec.category_description AS event_category_description, ec.slug AS event_category_slug
            FROM PAGES p
            LEFT JOIN EVENT_CATEGORIES ec ON p.event_category_id = ec.event_id
            LEFT JOIN PAGE_SECTIONS s ON p.page_id = s.page_id
            WHERE p.page_type = :type
            ORDER BY s.display_order ASC";

            $stmt = $pdo->prepare($query);
            $typeValue = $type->value;
            $stmt->bindParam(':type', $typeValue, PDO::PARAM_STR);
            $stmt->execute();

            $page = $stmt->fetch(PDO::FETCH_ASSOC);
            $pageModel = new Page();

            if ($page) {
                $pageModel->fromPDOData($page);
                do {
                    if ($page['section_id']) {
                        $section = new PageSection();
                        $section->fromPDOData($page);
                        
                        if (!empty($page['media_id'])) {
                            $section->media = $this->mediaRepository->getMediaById((int)$page['media_id']);
                        }
                        
                        if (!empty($page['gallery_id'])) {
                            $section->gallery = $this->mediaRepository->getGalleryById((int)$page['gallery_id']);
                        }
                        
                        $pageModel->addContentSection($section);
                    }
                } while ($page = $stmt->fetch(PDO::FETCH_ASSOC));
            }
            
            return $pageModel;
        } catch (PDOException $e) {
            die("Error fetching page data: " . $e->getMessage());
        }
    }

    public function updatePage(Page $page): bool
    {
        try {
            $pdo = $this->connect();
            // Updated to the unified PAGES table
            $query = "UPDATE PAGES SET title = :title, page_type = :page_type, slug = :slug, sidebar_html = :sidebar_html WHERE page_id = :page_id";
            $stmt = $pdo->prepare($query);
            
            $stmt->bindParam(':title', $page->title, PDO::PARAM_STR);
            $pageTypeValue = $page->page_type->value;
            $stmt->bindParam(':page_type', $pageTypeValue, PDO::PARAM_STR);
            $stmt->bindParam(':slug', $page->slug, PDO::PARAM_STR);
            $stmt->bindParam(':sidebar_html', $page->sidebar_html, PDO::PARAM_STR);
            $stmt->bindParam(':page_id', $page->page_id, PDO::PARAM_INT);
            $stmt->execute();

            foreach ($page->content_sections as $section) {
                $this->updatePageSectionById($section);
            }

            return $stmt->rowCount() >= 0; // Returns true even if no rows changed but query succeeded
        } catch (PDOException $e) {
            die("Error updating page: " . $e->getMessage());
        }
    }

    public function updatePageSectionById(PageSection $section): bool 
    { 
        try { 
            $pdo = $this->connect(); 
            // Updated to the unified PAGE_SECTIONS table
            $query = "UPDATE PAGE_SECTIONS 
            SET section_type = :section_type, title = :title, content_html = :content_html, 
                media_id = :media_id, display_order = :display_order, 
                cta_text = :cta_text, cta_url = :cta_url, gallery_id = :gallery_id 
            WHERE section_id = :section_id"; 

            $stmt = $pdo->prepare($query); 
            
            $sectionTypeValue = $section->section_type->value;
            $stmt->bindParam(':section_type', $sectionTypeValue, PDO::PARAM_STR); 
            $stmt->bindParam(':title', $section->title, PDO::PARAM_STR); 
            $stmt->bindParam(':content_html', $section->content_html, PDO::PARAM_STR); 

            $mediaId = $section->media ? $section->media->media_id : null; 
            $stmt->bindValue(':media_id', $mediaId, $mediaId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

            $stmt->bindParam(':display_order', $section->display_order, PDO::PARAM_INT); 
            $stmt->bindParam(':cta_text', $section->cta_text, PDO::PARAM_STR); 
            $stmt->bindParam(':cta_url', $section->cta_url, PDO::PARAM_STR); 

            $galleryId = $section->gallery ? $section->gallery->gallery_id : null; 
            $stmt->bindValue(':gallery_id', $galleryId, $galleryId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

            $stmt->bindParam(':section_id', $section->section_id, PDO::PARAM_INT); 
            
            return $stmt->execute(); 
        } catch (PDOException $e) { 
            die("Error updating section: " . $e->getMessage()); 
        }
    }
}