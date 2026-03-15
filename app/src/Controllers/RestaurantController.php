<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\RestaurantService;
use App\Services\VenueService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\IVenueService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;

class RestaurantController extends BaseController{
    private IRestaurantService $restaurantService;
    private IVenueService $venueService;
    public function __construct()
    {
        $this->venueService = new VenueService();
        $this->restaurantService = new RestaurantService();

    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []){
         try {
            $restaurants = $this->restaurantService->showAllRestaurants();

            $this->cmsLayout('Cms/Restaurants/index', [
                'title' => 'Manage Restaurants', 'restaurants' => $restaurants
            ]);
         } catch (\Exception $e) {
            error_log('Restaurants listing error:' . $e->getMessage());
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
         }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []){
         $venues = $this->venueService->getAllVenues();
        $this->cmsLayout('Cms/Restaurants/Form', [
            'title' => 'Create New Restaurant',
            'restaurant' => null,
            'venues' => $venues,
            'action' => '/cms/restaurants/store'
        ]);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []){
         $this->startSession();
        try {
            $restaurant = $this->restaurantService->createFromRequest($_POST, $_FILES);
            $_SESSION['success'] = "Restaurant [$restaurant->name] created succesfully! ";
            $this->redirect('cms/restaurants');

        } catch (\Exception $e) {
            error_log("Restaurant creation error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/restaurants/create');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []){
       
        $restaurantId = (int)($vars['id'] ?? 0);
        try {

            $restaurant = $this->restaurantService->getRestaurantById($restaurantId);
            
            $venues = $this->venueService->getAllVenues();
            if(!$restaurant){
                $this->internalServerError('Restaurant not Found!');
                return;
            }

            $this->cmsLayout('Cms/Restaurants/Form', [
                'title' => "Edit Restaurant {$restaurant->name}",
                'restaurant' => $restaurant,
                'venues' => $venues,
                'action' => "/cms/restaurants/update/{$restaurantId}"
            ]);

        } catch (\Exception $e) {
            error_log("Restaurant edit error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage(); 
            $this->redirect('/cms/restaurants');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []){
        $this->startSession();
        $restaurantId = (int)($vars['id'] ?? 0);
        try {
            $restaurant = $this->restaurantService->updateFromRequest($restaurantId, $_POST, $_FILES);
            $_SESSION['success'] = "Restaurant {$restaurant->name} updated successfully";

            $this->redirect('/cms/restaurants');
        } catch (\Exception $e) {
             error_log("Restaurant edit error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
             $this->redirect("/cms/restaurants/edit/{$restaurantId}"); 
        }
    }

     #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []){
        $this->startSession();
        $restaurantId = (int)($vars['id'] ?? 0);
        try {
            $restaurant = $this->restaurantService->getRestaurantById($restaurantId);
            if(!$restaurant){
                throw new \Exception('Restaurant not Found!');
            }

            $this->restaurantService->deleteRestaurant($restaurantId);
            $_SESSION['success'] = "Restaurant {$restaurant->name} was deleted successfully";

        } catch (\Exception $e) {
            error_log("Restaurant deletion error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }
        $this->redirect('/cms/restaurants');
    }

     private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}