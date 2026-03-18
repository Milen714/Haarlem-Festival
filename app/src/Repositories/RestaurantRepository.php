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
use App\Models\Yummy\Dish;
use PDO;
use PDOException;

class RestaurantRepository extends Repository implements IRestaurantRepository
{
    
    public function getAllRestaurants(int $eventId, ?int $cuisineId = null): array{
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
            r.deleted_at,
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt,
            v.venue_id AS venue_id,
            v.name AS venue_name,
            v.street_address AS venue_street_address,
            v.city AS venue_city,
            v.capacity AS venue_capacity,
            v.postal_code AS venue_postal_code,
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
            Throw new \Exception("Failed to fetch restaurants");
        }

    } 

    public function showAllRestaurants(): array{
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
            r.deleted_at,
            m.media_id AS main_image_id,
            m.file_path AS restaurant_image_path,
            m.alt_text AS restaurant_image_alt,
            v.venue_id AS venue_id,
            v.name AS venue_name,
            v.street_address AS venue_street_address,
            v.city AS venue_city,
            v.capacity AS venue_capacity,
            v.postal_code AS venue_postal_code,
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

            
            AND r.deleted_at IS NULL
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
            Throw new \Exception("Failed to fetch restaurants");
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
            $restaurant->dishes = $this->getDishessByRestaurant($id);
            $restaurant->gallery = $this->getRestaurantGallery($row['gallery_id'] ?? null);
            
            return $restaurant;

        } catch (\Throwable $e) {
            die($e->getMessage());
            // Log error or handle as needed
            // error_log("Error fetching restaurant by ID: " . $e->getMessage());
            // Throw new \Exception("Failed to fetch restaurant with ID: $id");
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
            $cuisines[] = [
                'id' => $row['cuisine_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'icon_url' => $row['icon_url']
            ];
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
                course_details_html, 
                special_notes_html, 
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
                :course_details_html, 
                :special_notes_html, 
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
    public function getSessions(): array{
        $pdo = $this->connect();
        $sql = "
        SELECT rs.*, st.name AS session_type_name
        FROM RESTAURANT_SESSION rs
        LEFT JOIN SESSION_TYPE st
        ON rs.session_type_id = st.session_type_id
        ORDER BY rs.restaurant_id, rs.session_number
        ";

        $getSession = $pdo->prepare($sql);
        $getSession->execute();
        $sessions = [];
        while ($row = $getSession->fetch(PDO::FETCH_ASSOC)) {
            $session = new Session();
            $session->fromPDOData($row);
            $sessions[] = $session;
        }
        return $sessions;
    }

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

    public function getSessionById(int $restaurantId, int $sessionNumber): ?Session
    {
        $pdo = $this->connect();
        $sql = "
            SELECT rs.*, st.name AS session_type_name
            FROM RESTAURANT_SESSION rs
            LEFT JOIN SESSION_TYPE st
            ON rs.session_type_id = st.session_type_id
            WHERE rs.restaurant_id = :restaurant_id
            AND rs.session_number = :session_number
            LIMIT 1
        ";

        $getSession = $pdo->prepare($sql);
        $getSession->execute([
            'restaurant_id' => $restaurantId,
            'session_number' => $sessionNumber
        ]);
        $row = $getSession->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }        
        $session = new Session();
        $session->fromPDOData($row);
        return $session;
    }

    public function createSession(Session $session): int
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
            'restaurant_id' => $session->restaurant,
            'session_type_id' => $session->session_id,
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
            'session_number' => $session->session_number
        ]);

        return $pdo->lastInsertId();
    }

    public function updateSession(Session $session): bool
    {
        $pdo = $this->connect();
        $sql = "
            UPDATE RESTAURANT_SESSION
            SET session_type_id = :session_type_id, start_time = :start_time, end_time = :end_time
            WHERE restaurant_id = :restaurant_id
            AND session_number = :session_number
        ";
        $update = $pdo->prepare($sql);
        return $update->execute([
            'session_type_id' => $session->session_id,
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
            'restaurant_id' => $session->restaurant,
            'session_number' => $session->session_number
        ]);
    }

    public function deleteSession(int $restaurantId, int $sessionNumber): bool
    {
        $pdo = $this->connect();
        //because theres no actual id and there will be multiple per restaurant
        $sql = "
            DELETE FROM RESTAURANT_SESSION
            WHERE restaurant_id = :restaurant_id
            AND session_number = :session_number
        ";
        $delete = $pdo->prepare($sql);
        return $delete->execute([
            'restaurant_id' => $restaurantId,
            'session_number' => $sessionNumber
        ]);
    }

    //Dish Crud
    public function getDishes(): array
    {
        $pdo =$this->connect();
        $sql= "
            SELECT d.*, m.media_id,
            m.file_path,
            m.alt_text FROM DISH d
            JOIN MEDIA m ON m.image_id = media_id
            WHERE deleted_at IS NULL
            ORDER BY display_order
        ";
        $getDishes = $pdo->prepare($sql);
        $getDishes->execute();
        $dishes = [];
        while ($row = $getDishes->fetch(PDO::FETCH_ASSOC)) {
            $dish = new Dish();
            $dish->fromPDOData($row);
            $dishes[] = $dish;
        }

        return $dishes;
    }

    public function getDishessByRestaurant(int $restaurantId): array
    {
       $pdo =$this->connect();
        $sql= "
            SELECT d.*,
            m.file_path,
            m.alt_text FROM DISH d
            JOIN MEDIA m ON m.media_id = d.image_id
            WHERE restaurant_id = :restaurant_id
            AND deleted_at IS NULL
            ORDER BY display_order
        ";
        $getDishes = $pdo->prepare($sql);
        $getDishes->execute([
            'restaurant_id' => $restaurantId
        ]);
        $dishes = [];
        while ($row = $getDishes->fetch(PDO::FETCH_ASSOC)) {
            $dish = new Dish();
            $dish->fromPDOData($row);
            $dishes[] = $dish;
        }

        return $dishes;
    }

    public function getDishById(int $id): ?Dish
    {
        $pdo = $this->connect();
        $sql = "
            SELECT d.*, m.file_path,
            m.alt_text FROM DISH d
            JOIN MEDIA m ON m.image_id = media_id
            WHERE dish_id = :dish_id
            AND deleted_at IS NULL
            LIMIT 1
        ";
        $getDish = $pdo->prepare($sql);
        $getDish->execute([
            'dish_id' => $id
        ]);
        $row = $getDish->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $dish = new Dish();
        $dish->fromPDOData($row);
        return $dish;
    }

    public function createDish(Dish $dish): int
    {
         $pdo = $this->connect();
         $sql = "
            INSERT INTO DISH
        (
            restaurant_id,
            name,
            description_html,
            is_featured,
            display_order,
            is_vegetarian,
            is_vegan,
            allergens
        )
        VALUES
        (
            :restaurant_id,
            :name,
            :description_html,
            :is_featured,
            :display_order,
            :is_vegetarian,
            :is_vegan,
            :allergens
        )
         ";
         $create = $pdo->prepare($sql);
         $create->execute([
            'restaurant_id' => $dish->restaurant,
            'name' => $dish->name,
            'description_html' => $dish->description_html,
            'is_featured' => $dish->is_featured,
            'display_order' => $dish->display_order,
            'is_vegetarian' => $dish->is_vegetarian,
            'is_vegan' => $dish->is_vegan,
            'allergens' => $dish->allergens
         ]);
         return (int)$pdo->lastInsertId();
    }

    public function updateDish(Dish $dish): bool
    {
        $pdo = $this->connect();
        $sql = "
        UPDATE DISH
        SET
            name = :name,
            description_html = :description_html,
            is_featured = :is_featured,
            display_order = :display_order,
            is_vegetarian = :is_vegetarian,
            is_vegan = :is_vegan,
            allergens = :allergens
        WHERE dish_id = :dish_id
    ";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        'dish_id' => $dish->dish_id,
        'name' => $dish->name,
        'description_html' => $dish->description_html,
        'is_featured' => $dish->is_featured,
        'display_order' => $dish->display_order,
        'is_vegetarian' => $dish->is_vegetarian,
        'is_vegan' => $dish->is_vegan,
        'allergens' => $dish->allergens
    ]);
    }

    public function deleteDish(int $id): bool
    {
        $pdo =$this->connect();
        $sql = "
            UPDATE DISH
            SET deleted_at = NOW()
            WHERE dish_id = :dish_id
        ";

        $delete = $pdo->prepare($sql);
        return $delete->execute([
            'dish_id' => $id
        ]);
    }
    
}