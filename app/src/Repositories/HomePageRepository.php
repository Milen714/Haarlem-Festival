<?php
namespace App\Repositories;

use App\CmsModels\CmsPageModel;
use App\CmsModels\TheFestivalSection;
use App\CmsModels\Enums\TheFestivalPageType;
use App\CmsModels\TheFestivalPage;
use App\Framework\Repository;
use App\Repositories\Interfaces\IHomePageRepository;
use App\Repositories\MediaRepository;


use PDO;
use PDOException;
class HomePageRepository extends Repository implements IHomePageRepository
{
    private MediaRepository $mediaRepository; 
    public function __construct() 
    { 
        parent::__construct(); $this->mediaRepository = new MediaRepository(); 
    }
    public function getPageData(TheFestivalPageType $type): TheFestivalPage
    {
        try{
			$pdo = $this->connect();
			$query = "SELECT 
                p.page_id, p.page_type, p.slug, p.title AS page_title,p.sidebar_html,
                s.section_id, s.section_type, s.title AS section_title, s.content_html, s.media_id AS media_id, s.caption, s.display_order AS sec_order,
                s.cta_text , s.cta_url, s.gallery_id
                
            FROM home_page_table p
            LEFT JOIN home_page_sections s ON p.page_id = s.page_id
            WHERE p.page_type = :type
            ORDER BY s.display_order ASC
        ";
			$stmt = $pdo->prepare($query);
            $typeValue = $type->value;
			$stmt->bindParam(':type', $typeValue, PDO::PARAM_STR);
			$stmt->execute();
			$page = $stmt->fetch(PDO::FETCH_ASSOC);
            $pageModel = new TheFestivalPage();
            if($page){
                $pageModel->fromPDOData($page);
                do {
                    if ($page['section_id']) {
                        $section = new TheFestivalSection();
                        $section->fromPDOData($page);
                        if (!empty($page['media_id'])) {
                            $media = $this->mediaRepository->getMediaById((int)$page['media_id']);
                            $section->media = $media;
                        }
                        if (!empty($page['gallery_id'])){
                            $gallery = $this->mediaRepository->getGalleryById((int)$page['gallery_id']);
                            $section->gallery = $gallery;
                        }
                        $pageModel->addContentSection($section);
                    }
                } while ($page = $stmt->fetch(PDO::FETCH_ASSOC));
            }
			
			return $pageModel;
		}catch(PDOException $e){
			die("Error fetching user: " . $e->getMessage());
		}
    }
    public function updatePage(TheFestivalPage $page): bool
    {
        try {
            $pdo = $this->connect();
            $query = "UPDATE home_page_table SET title = :title, page_type = :page_type, slug = :slug, sidebar_html = :sidebar_html WHERE page_id = :page_id";
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
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die("Error updating page: " . $e->getMessage());
        }
    }

        public function updatePageSectionById(TheFestivalSection $section): bool 
        { 
            try { 
                $pdo = $this->connect(); 
                $query = "UPDATE home_page_sections 
                SET section_type = :section_type, title = :title, content_html = :content_html, media_id = :media_id, caption = :caption, 
                display_order = :display_order, cta_text = :cta_text, cta_url = :cta_url, gallery_id = :gallery_id 
                WHERE section_id = :section_id"; 
                $stmt = $pdo->prepare($query); 
                $sectionTypeValue = $section->section_type->value;
                $stmt->bindParam(':section_type', $sectionTypeValue, PDO::PARAM_STR); 
                $stmt->bindParam(':title', $section->title, PDO::PARAM_STR); 
                $stmt->bindParam(':content_html', $section->content_html, PDO::PARAM_STR); 
                $mediaId = $section->media ? $section->media->media_id : null; 
                if ($mediaId === null) {
                    $stmt->bindValue(':media_id', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':media_id', $mediaId, PDO::PARAM_INT);
                }
                $stmt->bindParam(':caption', $section->caption, PDO::PARAM_STR); 
                $stmt->bindParam(':display_order', $section->display_order, PDO::PARAM_INT); 
                $stmt->bindParam(':cta_text', $section->cta_text, PDO::PARAM_STR); 
                $stmt->bindParam(':cta_url', $section->cta_url, PDO::PARAM_STR); 
                $galleryId = $section->gallery ? $section->gallery->gallery_id : null; 
                if ($galleryId === null) {
                    $stmt->bindValue(':gallery_id', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':gallery_id', $galleryId, PDO::PARAM_INT);
                }
                $stmt->bindParam(':section_id', $section->section_id, PDO::PARAM_INT); 
                return $stmt->execute(); 
                } catch (PDOException $e) { 
                    die("Error updating section: " . $e->getMessage()); 
                }
        }
}