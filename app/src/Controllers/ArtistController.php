<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use App\Services\ArtistService;
use App\Services\Interfaces\IArtistService;
use App\Services\AlbumService;
use App\Services\Interfaces\IAlbumService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class ArtistController extends BaseController
{
    private IArtistService $artistService;
    private IAlbumService $albumService;
    private ILogService $logService;

    /**
     * Wires up ArtistService, AlbumService, and LogService.
     */
    public function __construct()
    {
        $this->artistService = new ArtistService();
        $this->albumService  = new AlbumService();
        $this->logService    = new LogService();
    }

    /**
     * Renders the CMS artist listing page showing all active artists.
     * Redirects to the listing with a session error if data cannot be loaded.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
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
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to load artists.';
            $this->redirect('/cms/artists');
        }
    }

    /**
     * Renders the empty artist create form.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []): void
    {
        $this->cmsLayout('Cms/Artists/Form', [
            'title' => 'Create New Artist',
            'artist' => null,
            'action' => '/cms/artists/store'
        ]);
    }

    /**
     * Handles the artist create form submission.
     * Passes $_POST and $_FILES to ArtistService, sets a flash message, and redirects.
     * Distinguishes between ValidationException (user error) and other failures (server error)
     * so the right message is shown.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables (unused here, required by the router contract).
     *
     * @return void
     */
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
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to create artist.';
            $this->redirect('/cms/artists/create');
        }
    }

    /**
     * Renders the artist edit form pre-populated with the current artist data including gallery.
     * Uses getArtistByIdWithGallery() so the gallery section renders with existing images.
     * Redirects to the listing with an error if the artist does not exist.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the artist's primary key.
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []): void
    {
        $artistId = (int)($vars['id'] ?? 0);

        try {

            $artist = $this->artistService->getArtistByIdWithGallery($artistId);

            if (!$artist) {
                throw new ResourceNotFoundException('Artist not found.');
            }

            $albums = $this->albumService->getAlbumsByArtistId($artistId);

            $this->cmsLayout('Cms/Artists/Form', [
                'title'   => 'Edit Artist: ' . $artist->name,
                'artist'  => $artist,
                'action'  => "/cms/artists/update/{$artistId}",
                'albums'  => $albums,
            ]);
        } catch (ResourceNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/artists');
        } catch (\Throwable $e) {
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to load artist.';
            $this->redirect('/cms/artists');
        }
    }

    /**
     * Handles the artist update form submission, including profile image and gallery operations.
     * Delegates to updateArtistWithGalleryFromRequest() which handles all update logic in one call.
     * On success, redirects back to the edit form so the user can see their changes.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the artist's primary key.
     *
     * @return void
     */
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
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to update artist.';
            $this->redirect("/cms/artists/edit/{$artistId}");
        }
    }

    /**
     * Handles the artist delete action.
     * Fetches the artist first to get their name for the success message, then soft-deletes them.
     * Always redirects to the artist listing regardless of outcome.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'id' key with the artist's primary key.
     *
     * @return void
     */
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
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to delete artist.';
        }

        $this->redirect('/cms/artists');
    }

    /**
     * Removes a single image from an artist's gallery by unlinking it from GALLERY_MEDIA.
     * Redirects back to the artist edit page so the gallery renders without the removed image.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects 'artistId' and 'mediaId' keys.
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function removeGalleryImage($vars = []): void
    {
        $artistId = (int)($vars['artistId'] ?? 0);
        $mediaId  = (int)($vars['mediaId']  ?? 0);

        try {
            $this->artistService->removeGalleryImage($artistId, $mediaId);
            $_SESSION['success'] = 'Gallery image removed.';
        } catch (\Throwable $e) {
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to remove gallery image.';
        }

        $this->redirect("/cms/artists/edit/{$artistId}");
    }

    /**
     * Handles adding a new album for an artist from the edit form.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects an 'artistId' key.
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function addAlbum($vars = []): void
    {
        $artistId = (int)($vars['artistId'] ?? 0);

        try {
            $albumData = [
                'artist_id'    => $artistId,
                'name'         => $_POST['album_name']         ?? '',
                'release_year' => $_POST['album_release_year'] ?? '',
                'description'  => $_POST['album_description']  ?? '',
                'spotify_url'  => $_POST['album_spotify_url']  ?? '',
            ];
            $album = $this->albumService->createFromRequest($albumData, $_FILES);
            $_SESSION['success'] = "Album '{$album->name}' added successfully!";
        } catch (ValidationException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (\Throwable $e) {
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to add album.';
        }

        $this->redirect("/cms/artists/edit/{$artistId}");
    }

    /**
     * Handles removing an album from an artist.
     * Restricted to ADMIN role.
     *
     * @param array $vars  Route variables — expects 'artistId' and 'albumId' keys.
     *
     * @return void
     */
    #[RequireRole([UserRole::ADMIN])]
    public function removeAlbum($vars = []): void
    {
        $artistId = (int)($vars['artistId'] ?? 0);
        $albumId  = (int)($vars['albumId']  ?? 0);

        try {
            $this->albumService->deleteAlbum($albumId);
            $_SESSION['success'] = 'Album removed successfully.';
        } catch (\Throwable $e) {
            $this->logService->exception('Artist', $e);
            $_SESSION['error'] = 'Failed to remove album.';
        }

        $this->redirect("/cms/artists/edit/{$artistId}");
    }
}
