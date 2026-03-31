<?php
namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use App\Services\Interfaces\IAlbumService;
use App\Services\Interfaces\IMediaService;
use App\Services\Interfaces\ILogService;
use App\Repositories\AlbumRepository;
use App\Repositories\Interfaces\IAlbumRepository;
use App\Models\MusicEvent\Album;

class AlbumService implements IAlbumService
{
    private IAlbumRepository $albumRepository;
    private IMediaService $mediaService;
    private ILogService $logService;

    public function __construct() {
        $this->albumRepository = new AlbumRepository();
        $this->mediaService    = new MediaService();
        $this->logService      = new LogService();
    }

    public function getAlbumsByArtistId(int $artistId): array
    {
        return $this->albumRepository->getAlbumsByArtistId($artistId);
    }

    public function getAlbumById(int $albumId): ?Album
    {
        return $this->albumRepository->getAlbumById($albumId);
    }

    public function createFromRequest(array $postData, array $files): Album
    {
        $this->validateAlbumData($postData);

        $album = new Album();
        $album->fromPostData($postData);

        $album = $this->handleCoverImageUpload($album, $files);

        $success = $this->albumRepository->create($album);

        if (!$success) {
            throw new \Exception('Failed to create album in database.');
        }

        return $album;
    }

    public function updateFromRequest(int $albumId, array $postData, array $files): Album
    {
        $album = $this->albumRepository->getAlbumById($albumId);

        if (!$album) {
            throw new ResourceNotFoundException('Album not found.');
        }

        $this->validateAlbumData($postData);

        $album->fromPostData($postData);
        $album->album_id = $albumId;

        $album = $this->handleCoverImageUpload($album, $files);

        $success = $this->albumRepository->update($album);

        if (!$success) {
            throw new \Exception('Failed to update album in database.');
        }

        return $album;
    }

    public function deleteAlbum(int $albumId): bool
    {
        $album = $this->albumRepository->getAlbumById($albumId);

        if (!$album) {
            throw new ResourceNotFoundException('Album not found.');
        }

        if ($album->cover_image?->media_id) {
            $this->mediaService->deleteMedia($album->cover_image->media_id);
        }

        return $this->albumRepository->delete($albumId);
    }

    private function validateAlbumData(array $data): void
    {
        if (empty($data['name'])) {
            throw new ValidationException('Album name is required.');
        }

        if (empty($data['artist_id'])) {
            throw new ValidationException('Artist is required.');
        }
    }

    /**
     * Skips silently when no file is present or the upload errored.
     * Calls replaceMedia() when the album already has a cover image (update path),
     * otherwise uploadAndCreate() (create path).
     */
    private function handleCoverImageUpload(Album $album, array $files): Album
    {
        if (!isset($files['cover_image']) || $files['cover_image']['error'] !== UPLOAD_ERR_OK) {
            return $album;
        }

        $isUpdate = $album->cover_image !== null && isset($album->cover_image->media_id);

        try {
            if ($isUpdate) {
                $result = $this->mediaService->replaceMedia(
                    $album->cover_image->media_id,
                    $files['cover_image'],
                    'Albums',
                    $album->name . ' cover'
                );
            } else {
                $result = $this->mediaService->uploadAndCreate(
                    $files['cover_image'],
                    'Albums',
                    $album->name . ' cover'
                );
            }

            if ($result['success'] && isset($result['media'])) {
                $album->cover_image = $result['media'];
            } else {
                $errorMsg = $result['error'] ?? 'Unknown error';
                throw new \Exception('Failed to upload image: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            $this->logService->exception('Album', $e, ['album' => $album->name]);
            throw new \Exception('Failed to upload cover image: ' . $e->getMessage());
        }

        return $album;
    }
}
