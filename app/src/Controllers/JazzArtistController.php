<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Services\JazzService;

class JazzArtistController extends BaseController
{
    private JazzService $jazzService;

    /**
     * Wires up JazzService, which is responsible for validating the artist belongs to
     * the Jazz event and assembling all data needed for the artist detail page.
     */
    public function __construct()
    {
        $this->jazzService = new JazzService();
    }

    /**
     * Renders the Jazz artist detail page for the given URL slug.
     * Extracts the slug from route variables, delegates data assembly to JazzService,
     * and maps missing/unauthorized artist lookups to a 404 response.
     *
     * @param array $vars  Route variables — expects a 'slug' key with the artist's URL slug.
     *
     * @return void
     */
    public function detail(array $vars = []): void
    {
        $slug = $vars['slug'] ?? null;

        if (!$slug) {
            $this->notFound();
            return;
        }

        try {
            $this->view('Jazz/artist-detail', $this->jazzService->loadJazzArtistProfile($slug));
        } catch (ResourceNotFoundException $e) {

            $this->notFound();
        } catch (ApplicationException $e) {

            error_log('Jazz event configuration error: ' . $e->getMessage());
            $this->internalServerError();
        } catch (\Throwable $e) {

            error_log('Jazz artist detail error: ' . $e->getMessage());
            $this->internalServerError();
        }
    }
}
