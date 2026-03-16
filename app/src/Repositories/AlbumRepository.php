<?php
namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IAlbumRepository;
use App\Models\MusicEvent\Album;
use PDO;
use PDOException;

class AlbumRepository extends Repository implements IAlbumRepository
{
    public function getAlbumsByArtistId(int $artistId): array
    {
        try {
            $pdo = $this->connect();

            $query = '
            SELECT a.*,
                m.media_id,
                m.file_path,
                m.alt_text
            FROM ALBUM a
            LEFT JOIN MEDIA m ON a.cover_image_id = m.media_id
            WHERE a.artist_id = :artistId
            ORDER BY a.release_year DESC';

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':artistId', $artistId, PDO::PARAM_INT);
            $stmt->execute();

            $albumsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $albums = [];
            foreach ($albumsData as $albumData) {
                $album = new Album();
                $album->fromPDOData($albumData);
                $albums[] = $album;
            }
            return $albums;
        } catch (PDOException $e) {
            error_log("Error fetching albums for artist ID $artistId: " . $e->getMessage());
            throw new PDOException("Could not fetch albums for the artist.", 0, $e);
        }
    }   
}