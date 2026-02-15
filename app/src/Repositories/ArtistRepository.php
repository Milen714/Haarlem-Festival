<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IArtistRepository;
use App\Models\MusicEvent\Artist;
use PDO;
use PDOException;

class ArtistRepository extends Repository implements IArtistRepository
{
    /**
     * Gets all  the artists for a specific event(dance/jazz) with their profile images hell yeah bby
     */
    public function getArtistsByEventId(int $eventId): array
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                    a.artist_id,
                    a.name,
                    a.slug,
                    a.bio,
                    a.featured_quote,
                    a.website,
                    a.spotify_url,
                    a.youtube_url,
                    a.soundcloud_url,
                    m.media_id,
                    m.file_path,
                    m.alt_text,
                    ea.is_headliner,
                    ea.performance_order,
                    GROUP_CONCAT(g.name SEPARATOR ', ') as genres
                FROM ARTIST a
                INNER JOIN EVENT_ARTIST ea ON a.artist_id = ea.artist_id
                LEFT JOIN MEDIA m ON a.profile_image_id = m.media_id
                LEFT JOIN ARTIST_GENRE ag ON a.artist_id = ag.artist_id
                LEFT JOIN GENRE g ON ag.genre_id = g.genre_id
                WHERE ea.event_id = :event_id
                AND a.deleted_at IS NULL
                GROUP BY a.artist_id
                ORDER BY ea.is_headliner DESC, ea.performance_order ASC, a.name ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("Error fetching artists by event: " . $e->getMessage());
        }
    }

    /**
     * Gets the  artist by  a slug
     */
    public function getArtistBySlug(string $slug): ?object
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                    a.*,
                    m.file_path as profile_image_path,
                    m.alt_text as profile_image_alt
                FROM ARTIST a
                LEFT JOIN MEDIA m ON a.profile_image_id = m.media_id
                WHERE a.slug = :slug
                AND a.deleted_at IS NULL
                LIMIT 1
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ?: null;
        } catch (PDOException $e) {
            die("Error fetching artist by slug: " . $e->getMessage());
        }
    }

    /**
     * Gets  the artist by ID if needed
     */
    public function getArtistById(int $artistId): ?object
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                    a.*,
                    m.file_path as profile_image_path,
                    m.alt_text as profile_image_alt
                FROM ARTIST a
                LEFT JOIN MEDIA m ON a.profile_image_id = m.media_id
                WHERE a.artist_id = :artist_id
                AND a.deleted_at IS NULL
                LIMIT 1
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ?: null;
        } catch (PDOException $e) {
            die("Error fetching artist by ID: " . $e->getMessage());
        }
    }
}