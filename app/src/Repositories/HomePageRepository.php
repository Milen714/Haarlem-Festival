<?php
namespace App\Repositories;

use App\CmsModels\CmsPageModel;
use App\CmsModels\TheFestivalSection;
use App\CmsModels\Enums\TheFestivalPageType;
use App\CmsModels\TheFestivalPage;
use App\Framework\Repository;
use App\Repositories\Interfaces\IHomePageRepository;

use PDO;
use PDOException;
class HomePageRepository extends Repository implements IHomePageRepository
{
    public function getPageData(TheFestivalPageType $type): TheFestivalPage
    {
        try{
			$pdo = $this->connect();
			$query = "SELECT 
                p.page_id, p.page_type, p.slug, p.title AS page_title, p.hero_media_id, p.hero_gallery_id, p.sidebar_html,
                pp.media_id AS page_media_id, pp.file_path AS page_media_path, pp.alt_text AS page_media_alt,
                s.section_id, s.section_type, s.title AS section_title, s.content_html, s.media_id, s.caption, s.display_order AS sec_order,
                s.cta_text , s.cta_url,
                a.item_id, a.title AS acc_title, a.content_html AS acc_content, a.display_order AS acc_order,
                m.media_id AS media_id, m.file_path AS file_path, m.alt_text AS alt_text
            FROM home_page_table p
            LEFT JOIN home_page_sections s ON p.page_id = s.page_id
            LEFT JOIN MEDIA pp ON p.hero_media_id = pp.media_id
            LEFT JOIN home_accordion_items a ON s.section_id = a.section_id
            LEFT JOIN MEDIA m ON s.media_id = m.media_id
            WHERE p.page_type = :type
            ORDER BY s.display_order ASC, a.display_order ASC
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
                        $pageModel->addContentSection($section);
                    }
                } while ($page = $stmt->fetch(PDO::FETCH_ASSOC));
            }
			
			return $pageModel;
		}catch(PDOException $e){
			die("Error fetching user: " . $e->getMessage());
		}
    }
}