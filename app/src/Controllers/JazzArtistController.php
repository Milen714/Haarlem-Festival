<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Services\JazzService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;

class JazzArtistController extends BaseController
{
    private JazzService $jazzService;
    private ILogService $logService;

    /**
     * Wires up JazzService, which is responsible for validating the artist belongs to
     * the Jazz event and assembling all data needed for the artist detail page.
     */
    public function __construct()
    {
        $this->jazzService = new JazzService();
        $this->logService = new LogService();
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

            $this->logService->exception('Jazz', $e);
            $this->internalServerError();
        } catch (\Throwable $e) {

            $this->logService->exception('Jazz', $e);
            $this->internalServerError();
        }
    }
}
