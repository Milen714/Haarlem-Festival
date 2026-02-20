<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ArtistService;
use App\Services\MediaService;
use App\Repositories\ArtistRepository;
use App\Repositories\MediaRepository;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class ArtistController extends BaseController
{
    private ArtistService $artistService;

    public function __construct()
    {
        $artistRepository = new ArtistRepository();
        $mediaService = new MediaService(new MediaRepository());
        $this->artistService = new ArtistService($artistRepository, $mediaService);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []): void
    {
        try {
            $artists = $this->artistService->getAllArtists();
            
            $this->cmsLayout('Cms/Artists/Index', [
                'title' => 'Manage Artists',
                'artists' => $artists
            ]);
        } catch (\Exception $e) {
            error_log("Artist list error: " . $e->getMessage());
            $this->handleError('Failed to load artists: ' . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        $this->cmsLayout('Cms/Artists/Form', [
            'title' => 'Create New Artist',
            'artist' => null,
            'action' => '/cms/artists/store'
        ]);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []): void
    {
        $this->startSession();

        try {
            $artist = $this->artistService->createFromRequest($_POST, $_FILES);
            
            $_SESSION['success'] = "Artist '{$artist->name}' created successfully!";
            $this->redirect('/cms/artists');
            
        } catch (\Exception $e) {
            error_log("Artist create error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/artists/create');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $artistId = (int)($vars['id'] ?? 0);

        try {
            $artist = $this->artistService->getArtistById($artistId);

            if (!$artist) {
                $this->handleError('Artist not found');
                return;
            }

            $this->cmsLayout('Cms/Artists/Form', [
                'title' => 'Edit Artist: ' . $artist->name,
                'artist' => $artist,
                'action' => "/cms/artists/update/{$artistId}"
            ]);

        } catch (\Exception $e) {
            error_log("Artist edit error: " . $e->getMessage());
            $this->handleError('Failed to load artist: ' . $e->getMessage());
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $this->startSession();
        $artistId = (int)($vars['id'] ?? 0);

        try {
            $artist = $this->artistService->updateFromRequest($artistId, $_POST, $_FILES);
            
            $_SESSION['success'] = "Artist '{$artist->name}' updated successfully!";
            $this->redirect('/cms/artists');
            
        } catch (\Exception $e) {
            error_log("Artist update error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/artists/edit/{$artistId}");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {
        $this->startSession();
        $artistId = (int)($vars['id'] ?? 0);

        try {
            $artist = $this->artistService->getArtistById($artistId);
            
            if (!$artist) {
                throw new \Exception('Artist not found');
            }

            $artistName = $artist->name;
            $this->artistService->deleteArtist($artistId);
            
            $_SESSION['success'] = "Artist '{$artistName}' deleted successfully!";
            
        } catch (\Exception $e) {
            error_log("Artist delete error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/cms/artists');
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function handleError(string $message): void
    {
        $this->startSession();
        $_SESSION['error'] = $message;
        $this->redirect('/cms/artists');
    }
}