<?php

namespace App\Repositories;

use App\Models\Venue;
use App\Models\Yummy\Session;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Framework\Repository;
use App\Models\Restaurant;
use App\Models\Cuisine;
use App\Models\Gallery;
use App\Models\Media;
use PDO;
use PDOException;

class RestaurantRepository extends Repository implements IRestaurantRepository
{
    private function getBaseQuery(){
        return '
            SELECT 
            r.restaurant_id,
            r.event_id,
            r.venue_id,
            r.name AS restaurant_name,
            r.short_description AS restaurant_short_description,
            r.welcome_text AS restaurant_welcome_text,
            r.price_category AS restaurant_price_category,
            r.stars AS restaurant_stars,
            r.review_count AS restaurant_review_count,
            r.website_url AS restaurant_website_url,
            r.deleted_at,
            -- media
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt,
            -- venue
            v.venue_id AS venue_id,
            v.name AS venue_name,
            v.street_address AS venue_street_address,
            v.city AS venue_city,
            v.capacity AS venue_capacity,
            v.postal_code AS venue_postal_code,
            -- cuisine
            c.cuisine_id,
            c.name AS cuisine_name

            FROM RESTAURANT r

            LEFT JOIN MEDIA m 
                ON r.main_image_id = m.media_id

            LEFT JOIN VENUE v 
                ON r.venue_id = v.venue_id

            LEFT JOIN RESTAURANT_CUISINE rc
                ON r.restaurant_id = rc.restaurant_id

            LEFT JOIN CUISINE_TYPE c
                ON rc.cuisine_id = c.cuisine_id
        ';
    }
    
    public function getAllRestaurants(int $eventId, ?int $cuisineId = null): array{
        $pdo = $this->connect();
        $sql = $this->getBaseQuery() . "
            WHERE r.event_id = :event_id
            AND r.deleted_at IS NULL
        ";

        if ($cuisineId !== null) {
            $sql .= " AND rc.cuisine_id = :cuisine_id";
        }

        $sql .= " ORDER BY r.restaurant_id";
            try {
                $stmt = $pdo->prepare($sql);
                $params = ['event_id' => $eventId];

                if($cuisineId !== null){
                    $params['cuisine_id'] = $cuisineId;
                }

                $stmt->execute($params);
                
                $restaurants = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $id = $row['restaurant_id'];

                if (!isset($restaurants[$id])) {

                    $restaurant = new Restaurant();
                    $restaurant->fromPDOData($row);

                    if(!empty($row['venue_id'])){
                        $venue = new Venue();
                        $venue->venue_id = $row['venue_id'];
                        $venue->name = $row['venue_name'];
                        $venue->street_address = $row['venue_street_address'];
                        $venue->city = $row['venue_city'];
                        $venue->postal_code = $row['venue_postal_code'];
                        $venue->capacity = $row['venue_capacity'];
                        $restaurant->venue = $venue;
                    }

                    $restaurant->cuisines = [];

                    $restaurants[$id] = $restaurant;
                }

                // Add cuisines (avoid duplicates)
                if (!empty($row['cuisine_id'])) {
                    $restaurants[$id]->cuisines[$row['cuisine_id']] = [
                        'id' => $row['cuisine_id'],
                        'name' => $row['cuisine_name']
                    ];
                }
            }
            return array_values($restaurants);
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error fetching all restaurants: " . $e->getMessage());
            Throw new PDOException("Failed to fetch restaurants");
        }

    } 

    public function showAllRestaurants(): array{
       $pdo = $this->connect();
        $sql = $this->getBaseQuery() . "
            WHERE r.deleted_at IS NULL
            ORDER BY r.restaurant_id
        ";
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                
                $restaurants = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $id = $row['restaurant_id'];

                if (!isset($restaurants[$id])) {

                    $restaurant = new Restaurant();
                    $restaurant->fromPDOData($row);

                    if(!empty($row['venue_id'])){
                        $venue = new Venue();
                        $venue->venue_id = $row['venue_id'];
                        $venue->name = $row['venue_name'];
                        $venue->street_address = $row['venue_street_address'];
                        $venue->city = $row['venue_city'];
                        $venue->postal_code = $row['venue_postal_code'];
                        $venue->capacity = $row['venue_capacity'];
                        $restaurant->venue = $venue;
                    }

                    $restaurant->cuisines = [];

                    $restaurants[$id] = $restaurant;
                }

                // Add cuisines (avoid duplicates)
                if (!empty($row['cuisine_id'])) {
                    $restaurants[$id]->cuisines[$row['cuisine_id']] = [
                        'id' => $row['cuisine_id'],
                        'name' => $row['cuisine_name']
                    ];
                }
            }
            return array_values($restaurants);
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error fetching all restaurants: " . $e->getMessage());
            Throw new PDOException("Failed to fetch restaurants");
        } 
    }

    public function getRestaurantById(int $id): ?Restaurant{
        $pdo = $this->connect();
        $sql = '
            SELECT 
            r.*,
            r.chef_img,
            r.gallery_id,
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt,
            cm.alt_text AS chef_img_alt,
            cm.media_id AS chef_img_id,
            cm.file_path AS chef_img_path,
            bm.media_id AS banner_img_id,
            bm.file_path AS banner_img_path,
            bm.alt_text AS banner_img_alt,
            v.venue_id,
            v.name AS venue_name,
            v.street_address AS venue_street_address,
            v.city AS venue_city,
            v.postal_code AS venue_postal_code

            FROM RESTAURANT r
            LEFT JOIN MEDIA m 
                ON r.main_image_id = m.media_id
            LEFT JOIN MEDIA cm
                ON r.chef_img = cm.media_id
            LEFT JOIN MEDIA bm 
                ON r.banner_img = bm.media_id
               
            LEFT JOIN VENUE v 
                ON r.venue_id = v.venue_id
            
            WHERE r.restaurant_id = :restaurant_id
            AND r.deleted_at IS NULL
            LIMIT 1
        ';

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['restaurant_id' => $id]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            $restaurant = new Restaurant();
            $restaurant->fromPDOData($row);
            
            //load the new relations
            $restaurant->cuisines = $this->getRestaurantCuisines($id);
            $restaurant->sessions = $this->getSessionsByRestaurant($id);
            $restaurant->gallery = $this->getRestaurantGallery($row['gallery_id'] ?? null);
            
            return $restaurant;

        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error fetching restaurant by ID: " . $e->getMessage());
            Throw new PDOException("Failed to fetch restaurant with ID: $id");
        }
        
    }
    public function getRestaurantGallery(?int $galleryId){
            if(!$galleryId){
                return null;
            }

            $pdo = $this->connect();
            $sql = 'SELECT m.media_id, m.file_path, m.alt_text, gm.display_order
                FROM GALLERY_MEDIA gm
                LEFT JOIN MEDIA m 
                    ON gm.media_id = m.media_id
                    WHERE gm.gallery_id = :gallery_id
                    ORDER BY gm.display_order
            ';
            $getGallery = $pdo->prepare($sql);
            $getGallery->execute(['gallery_id' => $galleryId]);

            $gallery = new Gallery();
            $gallery->media_items = [];
            while ($row = $getGallery->fetch(PDO::FETCH_ASSOC)) {
                $media = new Media();
                $media->fromPDOData($row);
                $gallery->media_items[] = $media;
            }

            return $gallery;
    }
    public function getRestaurantCuisines(int $restaurantId){
        $pdo = $this->connect();
        $sql = "
            SELECT c.* 
            FROM CUISINE_TYPE c
            INNER JOIN RESTAURANT_CUISINE rc
            ON c.cuisine_id = rc.cuisine_id
            WHERE rc.restaurant_id = :restaurant_id
            ORDER BY c.name
        ";
        $getCuisines = $pdo->prepare($sql);
        $getCuisines->execute([
            'restaurant_id' => $restaurantId
        ]);
        $cuisines = [];
        while ($row = $getCuisines->fetch(PDO::FETCH_ASSOC)) {
            $cuisine = new Cuisine();
            $cuisine->fromPDOData($row);
            $cuisines[] = $cuisine;
        }

        return $cuisines;
    }
    public function getRestaurantBySlug(string $slug): ?Restaurant{
    $pdo = $this->connect();    
    $sql = "
            SELECT 
            r.restaurant_id,
            r.event_id,
            r.venue_id,
            r.chef_name,
            r.chef_bio_text,
            r.name AS restaurant_name,
            r.short_description AS restaurant_short_description,
            r.welcome_text AS restaurant_welcome_text,
            r.price_category AS restaurant_price_category,
            r.stars AS restaurant_stars,
            r.review_count AS restaurant_review_count,
            r.website_url AS restaurant_website_url,
            r.deleted_at,
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt
            FROM RESTAURANT r
            LEFT JOIN MEDIA m ON r.main_image_id = m.media_id
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
            Throw new PDOException("Failed to fetch restaurant with slug: $slug");
        }
    }

    public function getRestaurantsByEventId(int $eventId): array{
        $pdo = $this->connect();
        $sql = "
        SELECT 
        r.restaurant_id,
        r.event_id,
        r.venue_id,
        r.chef_name,
        r.chef_bio_text,
        r.chef_img,
        r.name AS restaurant_name,
        r.short_description AS restaurant_short_description,
        r.welcome_text AS restaurant_welcome_text,
        r.price_category AS restaurant_price_category,
        r.stars AS restaurant_stars,
        r.review_count AS restaurant_review_count,
        r.website_url AS restaurant_website_url,
        r.deleted_at,   
        m.media_id AS main_image_id,
        m.file_path AS restaurant_image_path,
        m.alt_text AS restaurant_image_alt
        FROM RESTAURANT r
        LEFT JOIN MEDIA m ON r.main_image_id = m.media_id
        LEFT JOIN VENUE v ON r.venue_id = v.venue_id
        LEFT JOIN RESTAURANT_CUISINE rc ON r.restaurant_id = rc.restaurant_id
        LEFT JOIN CUISINE_TYPE c ON rc.cuisine_id = c.cuisine_id
        WHERE r.event_id = :event_id
        AND r.deleted_at IS NULL
        AND (:cuisine_id IS NULL OR rc.cuisine_id = :cuisine_id)
        ORDER BY r.restaurant_id
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindParam(':cuisine_id', $cuisineId, PDO::PARAM_INT);
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
            throw new \RuntimeException("Error fetching restaurants by event ID: " . $e->getMessage());
        }
    }

    public function createRestaurant(Restaurant $restaurant): int{
    $pdo = $this->connect();   
    $sql = "
            INSERT INTO RESTAURANT (
                name, 
                short_description, 
                welcome_text, 
                price_category, 
                stars, 
                review_count, 
                website_url, 
                venue_id, 
                chef_name,
                chef_bio_text,
            ) VALUES (
                :name, 
                :short_description, 
                :welcome_text, 
                :price_category, 
                :stars, 
                :review_count, 
                :website_url,
                :venue_id,
                :chef_name,
                :chef_bio_text,
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
            $stmt->bindValue(':venue_id', $restaurant->venue_id, PDO::PARAM_INT);
            $stmt->bindValue(':chef_name', $restaurant->chef_name, PDO::PARAM_STR);
            $stmt->bindValue(':chef_bio_text', $restaurant->chef_bio_text, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return (int)$pdo->lastInsertId();
            } else {
                throw new \Exception("Failed to create restaurant");
            }
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error creating restaurant: " . $e->getMessage());
            throw new \RuntimeException("Failed to create restaurant: " . $e->getMessage());
        } 
    }
    public function updateRestaurant( Restaurant $restaurant): bool{
        //should add slug to database and update it here as well  
        $pdo = $this->connect();   
        $sql = "
            UPDATE RESTAURANT
            SET 
                name = :name,
                short_description = :short_description,
                welcome_text = :welcome_text,
                price_category = :price_category,
                stars = :stars,
                review_count = :review_count,
                website_url = :website_url,
                venue_id = :venue_id,
                chef_name = :chef_name,
                chef_bio_text = :chef_bio_text
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
            $stmt->bindValue(':chef_name', $restaurant->chef_name, PDO::PARAM_STR);
            $stmt->bindValue(':chef_bio_text', $restaurant->chef_bio_text, PDO::PARAM_STR);
            $stmt->bindValue(':venue_id', $restaurant->venue_id, PDO::PARAM_INT);
            $stmt->bindValue(':restaurant_id', $restaurant->restaurant_id, PDO::PARAM_INT);   
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating restaurant: " . $e->getMessage());
            throw new \RuntimeException("Failed to update restaurant with ID: " . $restaurant->restaurant_id . " - " . $e->getMessage());
        }
    }
    public function deleteRestaurant(int $id): bool{
        try {
            $pdo = $this->connect();
            $sql = "UPDATE RESTAURANT SET deleted_at = NOW() WHERE restaurant_id = :restaurant_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':restaurant_id', $id, PDO::PARAM_INT);
            return $stmt->execute([':restaurant_id' => $id]);
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error deleting restaurant: " . $e->getMessage());
            throw new \RuntimeException("Failed to delete restaurant with ID: $id - " . $e->getMessage());
        }
    }

    //Session Crud

    public function getAllSessionsTypes(): array
    {
        $pdo = $this->connect();
        $sql = "
            SELECT * 
            FROM SESSION_TYPE
            ORDER BY name
        ";
        $getSessionType = $pdo->prepare($sql);
        $getSessionType->execute();

        return $getSessionType->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionsByRestaurant(int $restaurantId): array
    {
        $pdo = $this->connect();
        $sql = "
            SELECT rs.*, st.name AS session_type_name, st.icon_url as session_type_icon
            FROM RESTAURANT_SESSION rs
            LEFT JOIN SESSION_TYPE st
            ON rs.session_type_id = st.session_type_id   
            WHERE rs.restaurant_id = :restaurant_id
            ORDER BY rs.session_number     
        ";

        $getSessions = $pdo->prepare($sql);
        $getSessions->execute(['restaurant_id' => $restaurantId]);
        $sessions = [];

        while ($row = $getSessions->fetch(PDO::FETCH_ASSOC)) {
            $session = new Session();
            $session->fromPDOData($row);
            $sessions[] = $session;
        }
        return $sessions;
    }

    public function createSession(Session $session): Session
    {
        $pdo = $this->connect();
        $sql = "
            INSERT INTO RESTAURANT_SESSION
            (restaurant_id, session_type_id, start_time, end_time, session_number)
            VALUES
            (:restaurant_id, :session_type_id, :start_time, :end_time, :session_number)
        ";
        $createSession = $pdo->prepare($sql);
        $createSession->execute([
            'restaurant_id' => $session->restaurantId,
            'session_type_id' => $session->session_id,
            'start_time' => $session->start_time->format('Y-m-d H:i:s'),
            'end_time' => $session->end_time->format('Y-m-d H:i:s'),
            'session_number' => $session->session_number
        ]);

        return $session;
    }

    public function deleteSessionsByRestaurant(int $restaurantId): bool
    {
        $pdo = $this->connect();
        $sql = "
            DELETE FROM RESTAURANT_SESSION
            WHERE restaurant_id = :restaurant_id
        ";
        $delete = $pdo->prepare($sql);
        return $delete->execute([
            'restaurant_id' => $restaurantId
        ]);
    }

    //to update the cuisines in restaurant cms
    public function syncRestaurantCuisines(int $restaurantId, $cuisineIds): void{
        $pdo = $this->connect();

        //delete old so no duplicates
        $stmt = $pdo->prepare('
            DELETE FROM RESTAURANT_CUISINE
            WHERE restaurant_id = :restaurant_id
        ');
        $stmt->execute(['restaurant_id' => $restaurantId]);

        //insert new list 
        $stmt = $pdo->prepare('
            INSERT INTO RESTAURANT_CUISINE (restaurant_id, cuisine_id)
            VALUES (:restaurant_id, :cuisine_id)
        ');
        //there are multiple in one setting so it goes through each
        foreach ($cuisineIds as $cuisineId){
            $stmt->execute([
                'restaurant_id' => $restaurantId,
                'cuisine_id' => $cuisineId
            ]);
        }
    }

     public function createGalleryForRestaurant(int $restaurantId, string $title = 'Restaurant Gallery'): int
    {
        try {
            $pdo = $this->connect();

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO GALLERY (title) VALUES (:title)");
            $stmt->bindValue(':title', $title);
            $stmt->execute();
            $galleryId = (int) $pdo->lastInsertId();

            $stmt2 = $pdo->prepare("UPDATE RESTAURANT SET gallery_id = :gallery_id WHERE restaurant_id = :restaurant_id");
            $stmt2->bindValue(':gallery_id', $galleryId, PDO::PARAM_INT);
            $stmt2->bindValue(':restaurant_id', $restaurantId, PDO::PARAM_INT);
            $stmt2->execute();

            $pdo->commit();

            return $galleryId;
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Error creating gallery for restaurant: " . $e->getMessage());
            throw new PDOException("Failed to create gallery for restaurant {$restaurantId}", 0, $e);
        }
    }

    
    //Insert a media item into a gallery at the given display order.
    public function addMediaToGallery(int $galleryId, int $mediaId, int $displayOrder): bool
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare(
                "INSERT INTO GALLERY_MEDIA (gallery_id, media_id, display_order) VALUES (:gallery_id, :media_id, :display_order)"
            );
            $stmt->bindValue(':gallery_id',    $galleryId,    PDO::PARAM_INT);
            $stmt->bindValue(':media_id',      $mediaId,      PDO::PARAM_INT);
            $stmt->bindValue(':display_order', $displayOrder, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding media to gallery: " . $e->getMessage());
            throw new PDOException("Failed to add media to gallery", 0, $e);
        }
    }

    
    //Remove a specific media item from a gallery.
    public function removeMediaFromGallery(int $galleryId, int $mediaId): bool
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare(
                "DELETE FROM GALLERY_MEDIA WHERE gallery_id = :gallery_id AND media_id = :media_id"
            );
            $stmt->bindValue(':gallery_id', $galleryId, PDO::PARAM_INT);
            $stmt->bindValue(':media_id',   $mediaId,   PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error removing media from gallery: " . $e->getMessage());
            throw new PDOException("Failed to remove media from gallery", 0, $e);
        }
    }

    
    //Get the next display_order value for a gallery (max + 1).
    public function getNextGalleryOrder(int $galleryId): int
    {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare(
                "SELECT COALESCE(MAX(display_order), 0) + 1 FROM GALLERY_MEDIA WHERE gallery_id = :gallery_id"
            );
            $stmt->bindValue(':gallery_id', $galleryId, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting next gallery order: " . $e->getMessage());
            throw new PDOException("Failed to get next gallery order", 0, $e);
        }
    }
    
}