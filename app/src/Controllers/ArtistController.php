<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use App\Services\ArtistService;
use App\Services\Interfaces\IArtistService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class ArtistController extends BaseController
{
    private IArtistService $artistService;

    public function __construct()
    {
        $this->artistService = new ArtistService();
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
        } catch (\Throwable $e) {
            error_log("Artist list error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to load artists.';
            $this->redirect('/cms/artists');
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
        try {
            $artist = $this->artistService->createFromRequest($_POST, $_FILES);

            if ($artist) {
                $_SESSION['success'] = "Artist '{$artist->name}' created successfully!";
            }
            $this->redirect('/cms/artists');
        } catch (ValidationException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/artists/create');
        } catch (ApplicationException | \Throwable $e) {
            error_log("Artist create error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create artist.';
            $this->redirect('/cms/artists/create');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $artistId = (int)($vars['id'] ?? 0);

        try {

            $artist = $this->artistService->getArtistByIdWithGallery($artistId);

            if (!$artist) {
                throw new ResourceNotFoundException('Artist not found.');
            }
            $this->cmsLayout('Cms/Artists/Form', [
                'title' => 'Edit Artist: ' . $artist->name,
                'artist' => $artist,
                'action' => "/cms/artists/update/{$artistId}"
            ]);
        } catch (ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/artists');
        } catch (\Throwable $e) {
            error_log("Artist edit error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to load artist.';
            $this->redirect('/cms/artists');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []): void
    {
        $artistId = (int)($vars['id'] ?? 0);

        try {
            $artist = $this->artistService->updateArtistWithGalleryFromRequest($artistId, $_POST, $_FILES);

            $_SESSION['success'] = "Artist '{$artist->name}' updated successfully!";
            $this->redirect("/cms/artists/edit/{$artistId}");
        } catch (ValidationException | ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/artists/edit/{$artistId}");
        } catch (ApplicationException | \Throwable $e) {
            error_log("Artist update error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update artist.';
            $this->redirect("/cms/artists/edit/{$artistId}");
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []): void
    {
        $artistId = (int)($vars['id'] ?? 0);

        try {
            $artist = $this->artistService->getArtistById($artistId);

            if (!$artist) {
                throw new ResourceNotFoundException('Artist not found.');
            }

            $artistName = $artist->name;
            $this->artistService->deleteArtist($artistId);

            $_SESSION['success'] = "Artist '{$artistName}' deleted successfully!";
        } catch (ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (\Throwable $e) {
            error_log("Artist delete error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete artist.';
        }

        $this->redirect('/cms/artists');
    }

    #[RequireRole([UserRole::ADMIN])]
    public function removeGalleryImage($vars = []): void
    {
        $artistId = (int)($vars['artistId'] ?? 0);
        $mediaId  = (int)($vars['mediaId']  ?? 0);

        try {
            $this->artistService->removeGalleryImage($artistId, $mediaId);
            $_SESSION['success'] = 'Gallery image removed.';
        } catch (\Throwable $e) {
            error_log("Remove gallery image error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to remove gallery image.';
        }

        $this->redirect("/cms/artists/edit/{$artistId}");
    }
}
