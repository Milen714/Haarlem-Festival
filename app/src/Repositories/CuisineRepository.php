<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ICuisineRepository;
use App\Framework\Repository;
use App\Models\Cuisine;
use PDOException;
use PDO;


class CuisineRepository extends Repository implements ICuisineRepository{
    
    public function getCuisines(): array
    {
        $pdo = $this->connect();
        $sql =
        "
            SELECT cuisine_id, name, description, icon_url
            FROM CUISINE_TYPE
            ORDER BY name ASC
        ";

        try {
            $getCuisines = $pdo->query($sql);
            $results = $getCuisines->fetchAll(PDO::FETCH_ASSOC);

            $cuisines = [];

            foreach($results as $row){
                $cuisine = new Cuisine();
                $cuisine->fromPDOData($row);
                $cuisines[] = $cuisine;
            }

            return $cuisines;
        } catch (PDOException $e) {
            error_log("Error fetching cuisines: " . $e->getMessage());
            throw new \Exception("Failed to fetch cuisines");
        }
    }

    public function getCuisineByRestaurant(int $restaurantId): array
    {
        $pdo = $this->connect();
        $sql = "
        SELECT c.cuisine_Id, c.name, c.description, c.icon_url
        FROM CUISINE_TYPE c
        INNER JOIN RESTAURANT_CUISINE rc
        ON c.cuisine_id = rc.cuisine_id
        WHERE rc.restaurant_id = :restaurant_id
        ORDER BY c.name ASC
        ";

        try {
            $getCuisine = $pdo->prepare($sql);
            $getCuisine->execute(['restaurant_id' => $restaurantId]);

            $results = $getCuisine->fetchAll(PDO::FETCH_ASSOC);

            $cuisines = [];

            foreach($results as $row){
                $cuisine = new Cuisine();
                $cuisine->fromPDOData($row);
                $cuisines[] = $cuisine;
            }

            return $cuisines;

        } catch (PDOException $e) {
           error_log("Error fetching cuisines: " . $e->getMessage());
            throw new \Exception("Failed to fetch cuisines");
        }
    }
}