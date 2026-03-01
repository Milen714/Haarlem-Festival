<?php

namespace App\Repositories;

use App\Repositories\Interfaces\IRestaurantRepository;
use App\Framework\Repository;
use App\Models\Restaurant;
use App\Models\Gallery;
use App\Models\Media;
use PDO;
use PDOException;

class RestaurantRepository extends Repository implements IRestaurantRepository
{
    
    public function getAllRestaurants(): array{
        $pdo = $this->connect();
        $sql = "
            SELECT 
            r.restaurant_id,
            r.event_id,
            r.venue_id,
            r.head_chef_id,
            r.name AS restaurant_name,
            r.short_description AS restaurant_short_description,
            r.welcome_text AS restaurant_welcome_text,
            r.price_category AS restaurant_price_category,
            r.stars AS restaurant_stars,
            r.review_count AS restaurant_review_count,
            r.website_url AS restaurant_website_url,
            r.course_details_html,
            r.special_notes_html,
            r.deleted_at,
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt,
            v.venue_id AS venue_id,
            v.name AS venue_name,
            v.address AS venue_address,
            v.city AS venue_city,
            v.postal_code AS venue_postal_code,
            gm.media_id AS gallery_media_id,
            gm.file_path AS gallery_image_path,
            gm.alt_text AS gallery_image_alt
            FROM restaurant r
            LEFT JOIN media m 
                ON r.main_image_id = m.media_id
            LEFT JOIN venue v 
                ON r.venue_id = v.venue_id
            LEFT JOIN gallery g 
                ON r.restaurant_id = g.restaurant_id
            LEFT JOIN media gm 
                ON g.media_id = gm.media_id
            WHERE r.deleted_at IS NULL
            ORDER BY r.restaurant_id;
        ";
        try {
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $restaurants = [];
            foreach ($results as $row) {
                $restaurant = new Restaurant();
                $restaurant->fromPDOData($row);
                $restaurants[] = $restaurant;
            }
            return $restaurants;
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error fetching all restaurants: " . $e->getMessage());
            Throw new \Exception("Failed to fetch restaurants");
        }

    }
    public function getRestaurantById(int $id): ?Restaurant{
        $pdo = $this->connect();
        $sql = "
            SELECT 
            r.restaurant_id,
            r.event_id,
            r.venue_id,
            r.head_chef_id,
            r.name AS restaurant_name,
            r.short_description AS restaurant_short_description,
            r.welcome_text AS restaurant_welcome_text,
            r.price_category AS restaurant_price_category,
            r.stars AS restaurant_stars,
            r.review_count AS restaurant_review_count,
            r.website_url AS restaurant_website_url,
            r.course_details_html,
            r.special_notes_html,
            r.deleted_at,
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt
        FROM restaurant r
        LEFT JOIN media m ON r.main_image_id = m.media_id
        WHERE r.restaurant_id = :restaurant_id
        AND r.deleted_at IS NULL
        ";

        try {
            $stmt = $pdo->prepare($sql);

            $restaurants = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['restaurant_id'];
                if (!isset($restaurants[$id])) {
                    $restaurant = new Restaurant();
                    $restaurant->fromPDOData($row);

                    $restaurant->gallery = new Gallery();
                    $restaurant->gallery->media_items = [];
                    $restaurants[$id] = $restaurant;
                }

                if ($row['main_image_id']) {
                     $media = new Media();
                    $media->fromPDOData([
                        'media_id' => $row['gallery_media_id'],
                        'file_path' => $row['gallery_image_path'],
                        'alt_text' => $row['gallery_image_alt'],
                    ]);

                    $restaurants[$id]->gallery->media_items[] = $media;
                }
            }

            return $restaurants[$id] ?? null;

        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error fetching restaurant by ID: " . $e->getMessage());
            Throw new \Exception("Failed to fetch restaurant with ID: $id");
        }
        
    }

    public function getRestaurantBySlug(string $slug): ?Restaurant{
    $pdo = $this->connect();    
    $sql = "
            SELECT 
            r.restaurant_id,
            r.event_id,
            r.venue_id,
            r.head_chef_id,
            r.name AS restaurant_name,
            r.short_description AS restaurant_short_description,
            r.welcome_text AS restaurant_welcome_text,
            r.price_category AS restaurant_price_category,
            r.stars AS restaurant_stars,
            r.review_count AS restaurant_review_count,
            r.website_url AS restaurant_website_url,
            r.course_details_html,
            r.special_notes_html,
            r.deleted_at,
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt
            FROM restaurant r
            LEFT JOIN media m ON r.main_image_id = m.media_id
            WHERE r.slug = :slug
            AND r.deleted_at IS NULL
            LIMIT 1
        ";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if(!$result){
                return null;
            }

            $restaurant = new Restaurant();
            $restaurant->fromPDOData($result);
            return $restaurant;
        }catch(PDOException $e){
            // Log error or handle as needed
            error_log("Error fetching restaurant by slug: " . $e->getMessage());
            Throw new \Exception("Failed to fetch restaurant with slug: $slug");
        }
    }

    public function getRestaurantsByEventId(int $eventId): array{
        $pdo = $this->connect();
        $sql = "
        SELECT 
            r.restaurant_id,
            r.event_id,
            r.venue_id,
            r.head_chef_id,
            
            r.name AS restaurant_name,
            r.short_description AS restaurant_short_description,
            r.welcome_text AS restaurant_welcome_text,
            r.price_category AS restaurant_price_category,
            r.stars AS restaurant_stars,
            r.review_count AS restaurant_review_count,
            r.website_url AS restaurant_website_url,
            
            r.course_details_html,
            r.special_notes_html,
            r.deleted_at,
            
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt

        FROM restaurant r
        LEFT JOIN media m ON r.main_image_id = m.media_id
        LEFT JOIN venue v ON r.venue_id = v.venue_id
        WHERE r.event_id = :event_id
        AND r.deleted_at IS NULL
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $restaurants = [];
            foreach ($results as $row) {
                $restaurant = new Restaurant();
                $restaurant->fromPDOData($row);
                $restaurants[] = $restaurant;
            }
            return $restaurants;
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error fetching restaurants by event ID: " . $e->getMessage());
            Throw new \Exception("Failed to fetch restaurants for event ID: $eventId");
        }
    }

    public function createRestaurant(Restaurant $restaurant): int{
    $pdo = $this->connect();   
    $sql = "
            INSERT INTO restaurant (
                name, 
                short_description, 
                welcome_text, 
                price_category, 
                stars, 
                review_count, 
                website_url, 
                course_details_html, 
                special_notes_html, 
                venue_id, 
                head_chef_id
            ) VALUES (
                :name, 
                :short_description, 
                :welcome_text, 
                :price_category, 
                :stars, 
                :review_count, 
                :website_url, 
                :course_details_html, 
                :special_notes_html, 
                :venue_id, 
                :head_chef_id
            )
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $restaurant->name, PDO::PARAM_STR);
            $stmt->bindValue(':short_description', $restaurant->short_description, PDO::PARAM_STR);
            $stmt->bindValue(':welcome_text', $restaurant->welcome_text, PDO::PARAM_STR);
            $stmt->bindValue(':price_category', $restaurant->price_category, PDO::PARAM_INT);
            $stmt->bindValue(':stars', $restaurant->stars, PDO::PARAM_INT); 
            $stmt->bindValue(':review_count', $restaurant->review_count, PDO::PARAM_INT);
            $stmt->bindValue(':website_url', $restaurant->website_url, PDO::PARAM_STR);
            $stmt->bindValue(':course_details_html', $restaurant->course_details_html, PDO::PARAM_STR);
            $stmt->bindValue(':special_notes_html', $restaurant->special_notes_html, PDO::PARAM_STR);
            $stmt->bindValue(':venue_id', $restaurant->venue_id, PDO::PARAM_INT);
            $stmt->bindValue(':head_chef_id', $restaurant->head_chef_id ?? null, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return (int)$pdo->lastInsertId();
            } else {
                throw new \Exception("Failed to create restaurant");
            }
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error creating restaurant: " . $e->getMessage());
            throw new \Exception("Failed to create restaurant");
        } 
    }
    public function updateRestaurant( Restaurant $restaurant): bool{
        //should add slug to database and update it here as well  
        $pdo = $this->connect();   
        $sql = "
            UPDATE restaurant
            SET 
                name = :name,
                short_description = :short_description,
                welcome_text = :welcome_text,
                price_category = :price_category,
                stars = :stars,
                review_count = :review_count,
                website_url = :website_url,
                course_details_html = :course_details_html,
                special_notes_html = :special_notes_html,
                venue_id = :venue_id,
                head_chef_id = :head_chef_id
            WHERE restaurant_id = :restaurant_id
            ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $restaurant->name, PDO::PARAM_STR);
            $stmt->bindValue(':short_description', $restaurant->short_description, PDO::PARAM_STR);
            $stmt->bindValue(':welcome_text', $restaurant->welcome_text, PDO::PARAM_STR);
            $stmt->bindValue(':price_category', $restaurant->price_category, PDO::PARAM_INT);
            $stmt->bindValue(':stars', $restaurant->stars, PDO::PARAM_INT); 
            $stmt->bindValue(':review_count', $restaurant->review_count, PDO::PARAM_INT);
            $stmt->bindValue(':website_url', $restaurant->website_url, PDO::PARAM_STR);
            $stmt->bindValue(':course_details_html', $restaurant->course_details_html, PDO::PARAM_STR);
            $stmt->bindValue(':special_notes_html', $restaurant->special_notes_html, PDO::PARAM_STR);
            $stmt->bindValue(':venue_id', $restaurant->venue_id, PDO::PARAM_INT);
            $stmt->bindValue(':head_chef_id', $restaurant->head_chef_id ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':restaurant_id', $restaurant->restaurant_id, PDO::PARAM_INT);   
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating restaurant: " . $e->getMessage());
            throw new \Exception("Failed to update restaurant with ID: " . $restaurant->restaurant_id);
        }
    }
    public function deleteRestaurant(int $id): bool{
        try {
            $pdo = $this->connect();
            $sql = "UPDATE restaurant SET deleted_at = NOW() WHERE restaurant_id = :restaurant_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':restaurant_id', $id, PDO::PARAM_INT);
            return $stmt->execute([':restaurant_id' => $id]);
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error deleting restaurant: " . $e->getMessage());
            Throw new \Exception("Failed to delete restaurant with ID: $id");
        }
    }
    
}
