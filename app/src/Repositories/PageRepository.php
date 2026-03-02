<?php
namespace App\Repositories;

use App\CmsModels\PageSection;
use App\CmsModels\Enums\PageType;
use App\CmsModels\Page;
use App\Framework\Repository;
use App\Repositories\Interfaces\IPageRepository;
use App\Repositories\MediaRepository;
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

    public function getPageBySlug(string $slug): Page
    {
        try {
            $pdo = $this->connect();
            $query = "SELECT 
                p.page_id, p.page_type, p.slug, p.title AS page_title, p.sidebar_html, p.event_category_id,
                s.section_id, s.section_type, s.title AS section_title, s.content_html, s.content_html_2,
                s.media_id, s.display_order AS sec_order, s.cta_text, s.cta_url, s.gallery_id,
                m.media_id AS section_media_id, m.file_path AS section_media_file_path, m.alt_text AS section_media_alt_text,
                g.gallery_id AS section_gallery_id, g.title AS section_gallery_title,
                gm.media_id AS gallery_media_id, gm.display_order AS gallery_media_display_order,
                gm_media.media_id AS gm_media_id, gm_media.file_path AS gm_media_file_path, gm_media.alt_text AS gm_media_alt_text,
                ec.event_id AS event_category_id, ec.title AS event_category_title,
                ec.type AS event_category_type, ec.category_description AS event_category_description, ec.slug AS event_category_slug
            FROM PAGES p
            LEFT JOIN EVENT_CATEGORIES ec ON p.event_category_id = ec.event_id
            LEFT JOIN PAGE_SECTIONS s ON p.page_id = s.page_id
            LEFT JOIN MEDIA m ON s.media_id = m.media_id
            LEFT JOIN GALLERY g ON s.gallery_id = g.gallery_id
            LEFT JOIN GALLERY_MEDIA gm ON g.gallery_id = gm.gallery_id
            LEFT JOIN MEDIA gm_media ON gm.media_id = gm_media.media_id
            WHERE p.slug = :slug
            ORDER BY s.display_order ASC, gm.display_order ASC";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();

            $page = $stmt->fetch(PDO::FETCH_ASSOC);
            $pageModel = new Page();
            $processedSections = []; // Track which sections we've already added

            if ($page) {
                $pageModel->fromPDOData($page);
                do {
                    if ($page['section_id']) {
                        // Only create and add the section once
                        if (!in_array($page['section_id'], $processedSections)) {
                            $section = new PageSection();
                            $section->fromPDOData($page);
                            
                            // Hydrate media from joined data (no extra query needed)
                            if (!empty($page['section_media_id'])) {
                                $media = new \App\Models\Media();
                                $media->media_id = (int)$page['section_media_id'];
                                $media->file_path = $page['section_media_file_path'];
                                $media->alt_text = $page['section_media_alt_text'];
                                $section->media = $media;
                            }
                            
                            // Initialize gallery (will be populated with media items below)
                            if (!empty($page['section_gallery_id'])) {
                                $gallery = new \App\Models\Gallery();
                                $gallery->fromPDOData($page);
                                $section->gallery = $gallery;
                            }
                            
                            $pageModel->addContentSection($section);
                            $processedSections[] = $page['section_id'];
                        }
                        
                        // Add gallery media items if they exist
                        if (!empty($page['section_gallery_id']) && !empty($page['gallery_media_id'])) {
                            $section = $pageModel->content_sections[count($pageModel->content_sections) - 1];
                            $galleryMedia = new \App\Models\GalleryMedia();
                            $galleryMedia->fromPDOData($page);
                            // Create media object for gallery media from joined data
                            if (!empty($page['gm_media_id'])) {
                                $media = new \App\Models\Media();
                                $media->media_id = (int)$page['gm_media_id'];
                                $media->file_path = $page['gm_media_file_path'];
                                $media->alt_text = $page['gm_media_alt_text'];
                                $galleryMedia->media = $media;
                            }
                            $section->gallery->addGalleryMedia($galleryMedia);
                        }
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

            return $stmt->rowCount() >= 0; 
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
            SET section_type = :section_type, title = :title, content_html = :content_html, content_html_2 = :content_html_2,
                media_id = :media_id, display_order = :display_order, 
                cta_text = :cta_text, cta_url = :cta_url, gallery_id = :gallery_id 
            WHERE section_id = :section_id"; 

            $stmt = $pdo->prepare($query); 
            
            $sectionTypeValue = $section->section_type->value;
            $stmt->bindParam(':section_type', $sectionTypeValue, PDO::PARAM_STR); 
            $stmt->bindParam(':title', $section->title, PDO::PARAM_STR); 
            $stmt->bindParam(':content_html', $section->content_html, PDO::PARAM_STR); 
            $stmt->bindParam(':content_html_2', $section->content_html_2, PDO::PARAM_STR); 

            $mediaId = $section->media ? $section->media->media_id : null; 
            $stmt->bindValue(':media_id', $mediaId, $mediaId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

            $stmt->bindParam(':display_order', $section->display_order, PDO::PARAM_INT); 
            $stmt->bindParam(':cta_text', $section->cta_text, PDO::PARAM_STR); 
            $stmt->bindParam(':cta_url', $section->cta_url, PDO::PARAM_STR); 

            $galleryId = $section->gallery ? $section->gallery->gallery_id : null; 
            $stmt->bindValue(':gallery_id', $galleryId, $galleryId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

            $stmt->bindParam(':section_id', $section->section_id, PDO::PARAM_INT); 
            
            $stmt->execute();
            if ($mediaId !== null) {
                $this->mediaRepository->updateMedia($section->media);
            }
            return $stmt->rowCount() >= 0; 
        } catch (PDOException $e) { 
            die("Error updating section: " . $e->getMessage()); 
        }
    }
    public function getPageSlugs(): array
    {
        try {
            $pdo = $this->connect();
            $query = "SELECT slug, title, page_type FROM PAGES";
            $stmt = $pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching page slugs: " . $e->getMessage());
        }
    }
}