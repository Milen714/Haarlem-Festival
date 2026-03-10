<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\ScheduleService;
use App\Repositories\UserRepository;
use App\Repositories\PageRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\VenueRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\RestaurantRepository;
use App\Services\ArtistService;
use App\Services\VenueService;
use App\Services\PageService;
use App\Services\LandmarkService;
use App\Services\RestaurantService;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Repositories\MediaRepository;
use App\Services\MediaService;
use App\ViewModels\Home\ScheduleList;
use App\ViewModels\Home\StartingPoints;

class HomeController extends BaseController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private PageService $pageService;
    private PageRepository $pageRepository;
    private LandmarkService $landmarkService;
    private MediaService $mediaService;
    private MediaRepository $mediaRepository;
    private ScheduleRepository $scheduleRepository;
    private ScheduleService $scheduleService;
    private VenueRepository $venueRepository;
    private VenueService $venueService;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);

        $this->pageRepository = new PageRepository();
        $this->pageService = new PageService($this->pageRepository);

        $this->mediaRepository = new MediaRepository();
        $this->mediaService = new MediaService($this->mediaRepository);

        $this->venueRepository = new VenueRepository();
        $this->venueService = new VenueService($this->venueRepository, $this->mediaService);

        $artistService = new ArtistService(new ArtistRepository(), $this->mediaService);
        $restaurantService = new RestaurantService(new RestaurantRepository(), $this->mediaService);
        $this->landmarkService = new LandmarkService();

        $this->scheduleRepository = new ScheduleRepository();
        $this->scheduleService = new ScheduleService(
            $this->scheduleRepository,
            $this->venueService,
            $artistService,
            $restaurantService,
            $this->landmarkService
        );
    }

    public function index()
    {
        $this->view('ShoppingCart/wishList', [ ]);
    }


}