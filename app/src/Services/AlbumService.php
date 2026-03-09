<?php
namespace App\Services;
use App\Services\Interfaces\IAlbumService;
use App\Repositories\Interfaces\IAlbumRepository;

class AlbumService implements IAlbumService
{
    private IAlbumRepository $albumRepository;

    public function __construct(IAlbumRepository $albumRepository) {
        $this->albumRepository = $albumRepository;
    }

    public function getAlbumsByArtistId(int $artistId): array
    {
        return $this->albumRepository->getAlbumsByArtistId($artistId);
    }
}