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
            throw new PDOException("Could not fetch albums for the artist.", 0, $e);
        }
    }

    public function getAlbumById(int $albumId): ?Album
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
            WHERE a.album_id = :albumId';

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':albumId', $albumId, PDO::PARAM_INT);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return null;
            }

            $album = new Album();
            $album->fromPDOData($data);
            return $album;
        } catch (PDOException $e) {
            throw new PDOException("Could not fetch album.", 0, $e);
        }
    }

    public function create(Album $album): bool
    {
        try {
            $pdo = $this->connect();

            $coverImageId = $album->cover_image?->media_id ?? null;

            $stmt = $pdo->prepare('
                INSERT INTO ALBUM (artist_id, name, release_year, description, cover_image_id, spotify_url)
                VALUES (:artist_id, :name, :release_year, :description, :cover_image_id, :spotify_url)
            ');

            $stmt->bindParam(':artist_id',     $album->artist_id,    PDO::PARAM_INT);
            $stmt->bindParam(':name',          $album->name,         PDO::PARAM_STR);
            $stmt->bindParam(':release_year',  $album->release_year, PDO::PARAM_STR);
            $stmt->bindParam(':description',   $album->description,  PDO::PARAM_STR);
            $stmt->bindParam(':cover_image_id', $coverImageId,       PDO::PARAM_INT);
            $stmt->bindParam(':spotify_url',   $album->spotify_url,  PDO::PARAM_STR);

            $result = $stmt->execute();

            if ($result) {
                $album->album_id = (int)$pdo->lastInsertId();
            }

            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Could not create album.", 0, $e);
        }
    }

    public function update(Album $album): bool
    {
        try {
            $pdo = $this->connect();

            $coverImageId = $album->cover_image?->media_id ?? null;

            $stmt = $pdo->prepare('
                UPDATE ALBUM
                SET artist_id      = :artist_id,
                    name           = :name,
                    release_year   = :release_year,
                    description    = :description,
                    cover_image_id = :cover_image_id,
                    spotify_url    = :spotify_url
                WHERE album_id = :album_id
            ');

            $stmt->bindParam(':album_id',      $album->album_id,     PDO::PARAM_INT);
            $stmt->bindParam(':artist_id',     $album->artist_id,    PDO::PARAM_INT);
            $stmt->bindParam(':name',          $album->name,         PDO::PARAM_STR);
            $stmt->bindParam(':release_year',  $album->release_year, PDO::PARAM_STR);
            $stmt->bindParam(':description',   $album->description,  PDO::PARAM_STR);
            $stmt->bindParam(':cover_image_id', $coverImageId,       PDO::PARAM_INT);
            $stmt->bindParam(':spotify_url',   $album->spotify_url,  PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Could not update album.", 0, $e);
        }
    }

    public function delete(int $albumId): bool
    {
        try {
            $pdo = $this->connect();

            $stmt = $pdo->prepare('DELETE FROM ALBUM WHERE album_id = :albumId');
            $stmt->bindParam(':albumId', $albumId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Could not delete album.", 0, $e);
        }
    }
}
