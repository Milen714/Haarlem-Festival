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
    public function getCuisineById(int $id): ?Cuisine
    {
        $pdo = $this->connect();
        $sql = "
            SELECT *
            FROM CUISINE_TYPE
            WHERE cuisine_id = :cuisine_id LIMIT 1
        ";

        $getCuisine = $pdo->prepare($sql);
        $getCuisine->execute([
            'cuisine_id' => $id
        ]);
        $row = $getCuisine->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $cuisine = new Cuisine();
        $cuisine->fromPDOData($row);
        return $cuisine;
    }

    public function createCuisine(Cuisine $cuisine): int
    {
        $pdo = $this->connect();
        $sql = "
            INSERT INTO CUISINE_TYPE
            (name, description, icon_url)
            VALUES
            (:name, :description, :icon_url)
        ";

        $create = $pdo->prepare($sql);
        $create->execute([
            'name' => $cuisine->name,
            'description' => $cuisine->description,
            'icon_url' => $cuisine->icon
        ]);
        return (int)$pdo->lastInsertId();
    }

    public function updateCuisine(Cuisine $cuisine): bool
    {
        $pdo = $this->connect();
        $sql = "
            UPDATE CUISINE_TYPE 
            SET
            name = :name, description = :description, icon_url = :icon_url
            WHERE cuisine_id = :cuisine_id
        ";  
        $update = $pdo->prepare($sql);
        return $update->execute([
            'name' => $cuisine->name,
            'description' => $cuisine->description,
            'icon_url' => $cuisine->icon,
            'cuisine_id' => $cuisine->cuisine_Id
        ]);
    }

    public function deleteCuisine(int $id): bool
    {
        //need to delete relationship first otherwise it would crash.
         $pdo = $this->connect();

        try {

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                DELETE FROM RESTAURANT_CUISINE
                WHERE cuisine_id = :cuisine_id
            ");

            $stmt->execute([
                'cuisine_id' => $id
            ]);

            $stmt = $pdo->prepare("
                DELETE FROM CUISINE_TYPE
                WHERE cuisine_id = :cuisine_id
            ");

            $stmt->execute([
                'cuisine_id' => $id
            ]);

            $pdo->commit();

            return true;

        } catch (\PDOException $e) {

            $pdo->rollBack();
            throw $e;

        }
    }

}