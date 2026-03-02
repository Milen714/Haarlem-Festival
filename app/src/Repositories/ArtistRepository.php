<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IArtistRepository;
use App\Models\MusicEvent\Artist;
use App\Models\MusicEvent\Album;
use App\Models\Media;
use App\Models\Gallery;
use App\Models\GalleryMedia;
use PDO;
use PDOException;

class ArtistRepository extends Repository implements IArtistRepository
{
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
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $artists = [];
            foreach ($results as $row) {
                $artist = new Artist();
                $artist->fromPDOData($row);
                $artists[] = $artist;
            }
            
            return $artists;
        } catch (PDOException $e) {
            error_log("Error fetching artists by event: " . $e->getMessage());
            throw new \RuntimeException("Failed to fetch artists for event {$eventId}", 0, $e);
        }
    }

   public function getArtistBySlug(string $slug): ?Artist
    {
    try {
        $pdo = $this->connect();

        $query = "
            SELECT 
                a.*,
                m.media_id,
                m.file_path,
                m.alt_text,
                GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') as genres
            FROM ARTIST a
            LEFT JOIN MEDIA m ON a.profile_image_id = m.media_id
            LEFT JOIN ARTIST_GENRE ag ON a.artist_id = ag.artist_id
            LEFT JOIN GENRE g ON ag.genre_id = g.genre_id
            WHERE a.slug = :slug
            AND a.deleted_at IS NULL
            GROUP BY a.artist_id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $artist = new Artist();
        $artist->fromPDOData($result);

        // Fetch gallery + media items if artist has a gallery
        if (!empty($result['gallery_id'])) {
            $galleryQuery = "
                SELECT 
                    g.gallery_id,
                    g.title AS gallery_title,
                    g.created_at,
                    m.media_id,
                    m.file_path,
                    m.alt_text,
                    gm.display_order
                FROM GALLERY g
                INNER JOIN GALLERY_MEDIA gm ON g.gallery_id = gm.gallery_id
                INNER JOIN MEDIA m ON gm.media_id = m.media_id
                WHERE g.gallery_id = :gallery_id
                ORDER BY gm.display_order ASC
            ";

            $gStmt = $pdo->prepare($galleryQuery);
            $gStmt->bindParam(':gallery_id', $result['gallery_id'], PDO::PARAM_INT);
            $gStmt->execute();
            $galleryRows = $gStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($galleryRows) {
                $gallery = new \App\Models\Gallery();
                $gallery->fromPDOData([
                    'gallery_id'    => $galleryRows[0]['gallery_id'],
                    'gallery_title' => $galleryRows[0]['gallery_title'],
                    'created_at'    => $galleryRows[0]['created_at'],
                ]);

                foreach ($galleryRows as $row) {
                    $media = new \App\Models\Media();
                    $media->fromPDOData([
                        'media_id'  => $row['media_id'],
                        'file_path' => $row['file_path'],
                        'alt_text'  => $row['alt_text'],
                    ]);
                    $galleryMedia = new \App\Models\GalleryMedia();
                    $galleryMedia->fromPDOData([
                        'gallery_id'    => $row['gallery_id'],
                        'display_order' => $row['display_order'],
                    ]);
                    $galleryMedia->media = $media;
                    $gallery->addGalleryMedia($galleryMedia);
                }

                $artist->gallery = $gallery;
            }
        }

        // Fetch albums
        $albumQuery = "
            SELECT 
                al.album_id,
                al.artist_id,
                al.name,
                al.release_year,
                al.description,
                al.spotify_url,
                m.media_id   AS cover_media_id,
                m.file_path  AS cover_file_path,
                m.alt_text   AS cover_alt_text
            FROM ALBUM al
            LEFT JOIN MEDIA m ON al.cover_image_id = m.media_id
            WHERE al.artist_id = :artist_id
            ORDER BY al.release_year DESC
        ";

        $aStmt = $pdo->prepare($albumQuery);
        $aStmt->bindParam(':artist_id', $artist->artist_id, PDO::PARAM_INT);
        $aStmt->execute();
        $albumRows = $aStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($albumRows as $row) {
            $album = new \App\Models\MusicEvent\Album();
            $album->fromPDOData($row);
            if (!empty($row['cover_media_id'])) {
                $coverMedia = new \App\Models\Media();
                $coverMedia->fromPDOData([
                    'media_id'  => $row['cover_media_id'],
                    'file_path' => $row['cover_file_path'],
                    'alt_text'  => $row['cover_alt_text'],
                ]);
                $album->cover_image = $coverMedia;
            }
            $artist->albums[] = $album;
        }

        return $artist;

    } catch (PDOException $e) {
        error_log("Error fetching artist by slug: " . $e->getMessage());
        throw new \RuntimeException("Failed to fetch artist by slug: {$slug}", 0, $e);
    }
}

    public function getArtistById(int $artistId): ?Artist
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                    a.*,
                    m.media_id,
                    m.file_path,
                    m.alt_text
                FROM ARTIST a
                LEFT JOIN MEDIA m ON a.profile_image_id = m.media_id
                WHERE a.artist_id = :artist_id
                AND a.deleted_at IS NULL
                LIMIT 1
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return null;
            }
            
            $artist = new Artist();
            $artist->fromPDOData($result);
            return $artist;
        } catch (PDOException $e) {
            error_log("Error fetching artist by ID: " . $e->getMessage());
            throw new \RuntimeException("Failed to fetch artist by ID: {$artistId}", 0, $e);
        }
    }

    public function getAllArtists(): array
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                SELECT 
                a.*,
                a.profile_image_id,
                m.media_id,
                m.file_path,
                m.alt_text,
                GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') as genres,
                COUNT(DISTINCT ea.event_id) as event_count
                FROM ARTIST a
                LEFT JOIN MEDIA m ON a.profile_image_id = m.media_id
                LEFT JOIN ARTIST_GENRE ag ON a.artist_id = ag.artist_id
                LEFT JOIN GENRE g ON ag.genre_id = g.genre_id
                LEFT JOIN EVENT_ARTIST ea ON a.artist_id = ea.artist_id
                WHERE a.deleted_at IS NULL
                GROUP BY a.artist_id
                ORDER BY a.name ASC
            ";

            $stmt = $pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $artists = [];
            foreach ($results as $row) {
                $artist = new Artist();
                $artist->fromPDOData($row);
                $artists[] = $artist;
            }
            
            return $artists;
        } catch (PDOException $e) {
            error_log("Error fetching all artists: " . $e->getMessage());
            throw new \RuntimeException("Failed to fetch artists", 0, $e);
        }
    }
  
        public function create(Artist $artist): bool
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                INSERT INTO ARTIST (
                    name, 
                    slug, 
                    bio,
                    featured_quote,
                    press_quote,
                    collaborations,
                    profile_image_id,
                    website,
                    spotify_url,
                    youtube_url,
                    soundcloud_url
                ) VALUES (
                    :name,
                    :slug,
                    :bio,
                    :featured_quote,
                    :press_quote,
                    :collaborations,
                    :profile_image_id,
                    :website,
                    :spotify_url,
                    :youtube_url,
                    :soundcloud_url
                )
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':name',             $artist->name);
            $stmt->bindValue(':slug',             $artist->slug);
            $stmt->bindValue(':bio',              $artist->bio);
            $stmt->bindValue(':featured_quote',   $artist->featured_quote);
            $stmt->bindValue(':press_quote',      $artist->press_quote);
            $stmt->bindValue(':collaborations',   $artist->collaborations);
            $profileImageId = $artist->profile_image?->media_id ?? null;
            $stmt->bindValue(':profile_image_id', $profileImageId, PDO::PARAM_INT);
            $stmt->bindValue(':website',          $artist->website);
            $stmt->bindValue(':spotify_url',      $artist->spotify_url);
            $stmt->bindValue(':youtube_url',      $artist->youtube_url);
            $stmt->bindValue(':soundcloud_url',   $artist->soundcloud_url);
            
            $result = $stmt->execute();
            
            if ($result) {
                $artist->artist_id = (int)$pdo->lastInsertId();
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error creating artist: " . $e->getMessage());
            throw new \RuntimeException("Failed to create artist", 0, $e);
        }
    }

       public function update(Artist $artist): bool
    {
        try {
            $pdo = $this->connect();
            
            $query = "
                UPDATE ARTIST SET
                    name             = :name,
                    slug             = :slug,
                    bio              = :bio,
                    featured_quote   = :featured_quote,
                    press_quote      = :press_quote,
                    collaborations   = :collaborations,
                    profile_image_id = :profile_image_id,
                    website          = :website,
                    spotify_url      = :spotify_url,
                    youtube_url      = :youtube_url,
                    soundcloud_url   = :soundcloud_url
                WHERE artist_id = :artist_id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':artist_id',        $artist->artist_id, PDO::PARAM_INT);
            $stmt->bindValue(':name',             $artist->name);
            $stmt->bindValue(':slug',             $artist->slug);
            $stmt->bindValue(':bio',              $artist->bio);
            $stmt->bindValue(':featured_quote',   $artist->featured_quote);
            $stmt->bindValue(':press_quote',      $artist->press_quote);
            $stmt->bindValue(':collaborations',   $artist->collaborations);
            $profileImageId = $artist->profile_image?->media_id ?? null;
            $stmt->bindValue(':profile_image_id', $profileImageId, PDO::PARAM_INT);
            $stmt->bindValue(':website',          $artist->website);
            $stmt->bindValue(':spotify_url',      $artist->spotify_url);
            $stmt->bindValue(':youtube_url',      $artist->youtube_url);
            $stmt->bindValue(':soundcloud_url',   $artist->soundcloud_url);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating artist: " . $e->getMessage());
            throw new \RuntimeException("Failed to update artist", 0, $e);
        }
    }

    public function delete(int $artistId): bool
    {
        try {
            $pdo = $this->connect();

            $query = "UPDATE ARTIST SET deleted_at = NOW() WHERE artist_id = :artist_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':artist_id', $artistId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting artist: " . $e->getMessage());
            throw new \RuntimeException("Failed to delete artist", 0, $e);
        }
    }

    public function isArtistInEvent(int $artistId, int $eventId): bool
{
    try {
        $pdo = $this->connect();

        $stmt = $pdo->prepare("
            SELECT 1
            FROM EVENT_ARTIST
            WHERE event_id = :event_id
              AND artist_id = :artist_id
            LIMIT 1
        ");

        $stmt->bindValue(':event_id', $eventId, \PDO::PARAM_INT);
        $stmt->bindValue(':artist_id', $artistId, \PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    } catch (\PDOException $e) {
        error_log('Error checking artist in event: ' . $e->getMessage());
        return false;
    }
}
}