<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Services\JazzService;

class JazzController extends BaseController
{
    private JazzService $jazzService;

    public function __construct()
    {
        $this->jazzService = new JazzService();
    }

    public function index($vars = [])
    {
        try {
            $this->view('Jazz/index', $this->jazzService->loadJazzOverview());
        } catch (ResourceNotFoundException $e) {

            $this->notFound();
        } catch (ApplicationException $e) {

            error_log("Jazz page configuration error: " . $e->getMessage());
            $this->internalServerError();
        } catch (\Throwable $e) {

            error_log("Jazz page error: " . $e->getMessage());
            $this->internalServerError();
        }
    }

    public function schedule($vars = []): void
    {
        try {
            $this->view('Jazz/schedule', $this->jazzService->loadJazzSchedule());
        } catch (ResourceNotFoundException $e) {

            $this->notFound();
        } catch (ApplicationException $e) {

            error_log("Jazz schedule configuration error: " . $e->getMessage());
            $this->internalServerError();
        } catch (\Throwable $e) {

            error_log("Jazz schedule page error: " . $e->getMessage());
            $this->internalServerError();
        }
    }
}
