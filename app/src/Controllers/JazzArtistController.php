<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Services\JazzService;
use App\Services\Interfaces\JazzServiceInterface;

class JazzArtistController extends BaseController
{
    private JazzServiceInterface $jazzService;

    public function __construct()
    {
        $this->jazzService = new JazzService();
    }

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