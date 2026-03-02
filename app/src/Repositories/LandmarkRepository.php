<?php

namespace App\Repositories;

use App\Models\History\Landmark;
use App\Framework\Repository;
use PDO;

class LandmarkRepository extends Repository 
{
    /** @return Landmark[] */
    public function getAll(): array
    {
        $sql = "SELECT * FROM landmarks ORDER BY display_order ASC";
        
        $stmt = $this->pdo->query($sql); 
        
        $landmarks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $landmark = new Landmark();
            $landmark->fromPDOData($row);
            $landmarks[] = $landmark;
        }

        return $landmarks;
    }

    public function getBySlug(string $slug): ?Landmark
    {
        $sql = "SELECT * FROM landmarks WHERE landmark_slug = :slug LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }

        $landmark = new Landmark();
        $landmark->fromPDOData($row);
        
        return $landmark;
    }

    public function insert(Landmark $landmark): Landmark
    {
        $sql = "INSERT INTO landmarks (
                    event_id, name, short_description, landmark_slug, 
                    intro_title, intro_content, why_visit_title, why_visit_content, 
                    detail_history_title, detail_history_content, display_order
                ) VALUES (
                    :event_id, :name, :short_description, :landmark_slug, 
                    :intro_title, :intro_content, :why_visit_title, :why_visit_content, 
                    :detail_history_title, :detail_history_content, :display_order
                )";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':event_id', $landmark->event_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $landmark->name, PDO::PARAM_STR);
        $stmt->bindParam(':short_description', $landmark->short_description, PDO::PARAM_STR);
        $stmt->bindParam(':landmark_slug', $landmark->landmark_slug, PDO::PARAM_STR);
        $stmt->bindParam(':intro_title', $landmark->intro_title, PDO::PARAM_STR);
        $stmt->bindParam(':intro_content', $landmark->intro_content, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_title', $landmark->why_visit_title, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_content', $landmark->why_visit_content, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_title', $landmark->detail_history_title, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_content', $landmark->detail_history_content, PDO::PARAM_STR);
        $stmt->bindParam(':display_order', $landmark->display_order, PDO::PARAM_INT);

        $stmt->execute();

        $landmark->landmark_id = (int)$this->pdo->lastInsertId(); //to get the new id
        
        return $landmark;
    }

    public function update(Landmark $landmark): Landmark
    {
        $sql = "UPDATE landmarks SET 
                    event_id = :event_id,
                    name = :name,
                    short_description = :short_description,
                    landmark_slug = :landmark_slug,
                    intro_title = :intro_title,
                    intro_content = :intro_content,
                    why_visit_title = :why_visit_title,
                    why_visit_content = :why_visit_content,
                    detail_history_title = :detail_history_title,
                    detail_history_content = :detail_history_content,
                    display_order = :display_order
                WHERE landmark_id = :landmark_id";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':landmark_id', $landmark->landmark_id, PDO::PARAM_INT);
        $stmt->bindParam(':event_id', $landmark->event_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $landmark->name, PDO::PARAM_STR);
        $stmt->bindParam(':short_description', $landmark->short_description, PDO::PARAM_STR);
        $stmt->bindParam(':landmark_slug', $landmark->landmark_slug, PDO::PARAM_STR);
        $stmt->bindParam(':intro_title', $landmark->intro_title, PDO::PARAM_STR);
        $stmt->bindParam(':intro_content', $landmark->intro_content, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_title', $landmark->why_visit_title, PDO::PARAM_STR);
        $stmt->bindParam(':why_visit_content', $landmark->why_visit_content, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_title', $landmark->detail_history_title, PDO::PARAM_STR);
        $stmt->bindParam(':detail_history_content', $landmark->detail_history_content, PDO::PARAM_STR);
        $stmt->bindParam(':display_order', $landmark->display_order, PDO::PARAM_INT);

        $stmt->execute();

        return $landmark;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM landmarks WHERE landmark_id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}