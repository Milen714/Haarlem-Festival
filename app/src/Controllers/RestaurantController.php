<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use App\Exceptions\ApplicationException;
use App\Services\RestaurantService;
use App\Services\VenueService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\IVenueService;
use App\Services\CuisineService;
use App\Models\Enums\UserRole;
use App\Middleware\RequireRole;
use App\Services\Interfaces\ICuisineService;
use App\Services\LogService;
use App\Services\Interfaces\ILogService;

class RestaurantController extends BaseController{
    private IRestaurantService $restaurantService;
    private ICuisineService $cuisineService;
    private IVenueService $venueService;
    private ILogService $logService;
    public function __construct()
    {
        $this->venueService = new VenueService();
        $this->restaurantService = new RestaurantService();
        $this->cuisineService = new CuisineService();
        $this->logService = new LogService();
    }

    #[RequireRole([UserRole::ADMIN])]
    public function index($vars = []){
         try {
            $restaurants = $this->restaurantService->showAllRestaurants();

            $this->cmsLayout('Cms/Restaurants/index', [
                'title' => 'Manage Restaurants', 'restaurants' => $restaurants
            ]);
         } catch (ResourceNotFoundException $e) {
            $this->logService->exception('Restaurant', $e);
            $_SESSION['error'] = 'Failed to fetch all restaurants';
         }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function showCuisines($vars = []){
         try {
            $cuisines = $this->cuisineService->getCuisines();

            $this->cmsLayout('Cms/Restaurants/Cuisines', [
                'title' => 'Manage cuisines', 'cuisines' => $cuisines
            ]);
         } catch (ResourceNotFoundException $e) {
            $this->logService->exception('Restaurant', $e);
            $_SESSION['error'] = 'Failed to fetch all cuisines';
         }
    }


    #[RequireRole([UserRole::ADMIN])]
    public function createCuisine($vars = []){
         try {

            $this->cmsLayout('Cms/Restaurants/cuisines', [
                'title' => 'Manage cuisines', 'cuisine' => null,
                'action' => '/cms/cuisines/store'
            ]);
         } catch (\Exception $e) {
            $this->logService->exception('Restaurant', $e);
            $this->internalServerError("Error loading homepage: " . $e->getMessage());
         }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function create($vars = []){
        $venues = $this->venueService->getAllVenues();
        $cuisines = $this->cuisineService->getCuisines();
        $sessionTypes = $this->restaurantService->getAllSessionsTypes();
        $this->cmsLayout('Cms/Restaurants/Form', [
            'title' => 'Create New Restaurant',
            'restaurant' => null,
            'venues' => $venues,
            'cuisines' => $cuisines,
            'sessionTypes' => $sessionTypes,
            'action' => '/cms/restaurants/store'
        ]);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function store($vars = []){
        
        try {
            $restaurant = $this->restaurantService->createFromRequest($_POST, $_FILES);
            $_SESSION['success'] = "Restaurant [$restaurant->name] created succesfully! ";
            $this->redirect('cms/restaurants');

        } catch (\Throwable | ValidationException $e) {
            $this->logService->exception('Restaurant', $e);
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/restaurants/create');
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function storeCuisine(){
        try {
            $this->cuisineService->createCuisineFromRequest($_POST);
            $_SESSION['success'] = "Cuisine created!";
            $this->redirect('/cms/restaurants/cuisines');
        } catch (\Throwable | ValidationException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/restaurants/cuisine/create');
        }
    }


    #[RequireRole([UserRole::ADMIN])]
    public function edit($vars = []){
       
        $restaurantId = (int)($vars['id'] ?? 0);
        try {

            $restaurant = $this->restaurantService->getRestaurantById($restaurantId);
            $cuisines = $this->cuisineService->getCuisines();
            $sessionTypes = $this->restaurantService->getAllSessionsTypes();
            
            $venues = $this->venueService->getAllVenues();
            if(!$restaurant){
                throw new ResourceNotFoundException('Restaurant not Found!');
                return;
            }

            $this->cmsLayout('Cms/Restaurants/Form', [
                'title' => "Edit Restaurant {$restaurant->name}",
                'restaurant' => $restaurant,
                'venues' => $venues,
                'cuisines' => $cuisines,
                'sessionTypes' => $sessionTypes,
                'action' => "/cms/restaurants/update/{$restaurantId}"
            ]);

        } catch (ResourceNotFoundException | ValidationException  $e) {
            $this->logService->exception('Restaurant', $e);
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/restaurants');
        }
    }
    
    #[RequireRole([UserRole::ADMIN])]
    public function editCuisine($vars = []){
        $id = (int)($vars['id'] ?? 0);

        $cuisine = $this->cuisineService->getCuisineById($id);

        if (!$cuisine) {
           throw new ResourceNotFoundException('Cuisine not Found!');
            return;
        }
        $this->cmsLayout('Cms/Restaurants/CuisineForm', [
            'title' => 'Edit Cuisine',
            'cuisine' => $cuisine,
            'action' => '/cms/restaurants/cuisines/upadte/{$id}'
        ]);
    }

    #[RequireRole([UserRole::ADMIN])]
    public function update($vars = []){
        $restaurantId = (int)($vars['id'] ?? 0);
        try {
            $restaurant = $this->restaurantService->updateFromRequest($restaurantId, $_POST, $_FILES);
            $_SESSION['success'] = "Restaurant {$restaurant->name} updated successfully";

            $this->redirect('/cms/restaurants');
        } catch (ApplicationException $e) {
            $this->logService->exception('Restaurant', $e);
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("/cms/restaurants/edit/{$restaurantId}"); 
        }
    }

    #[RequireRole([UserRole::ADMIN])]
    public function updateCuisine($vars = []){
        $id = (int)($vars['id'] ?? 0);
        try {
            $this->cuisineService->updateCuisineFromRequest($id, $_POST);

            $_SESSION['success'] = "Cuisine Updated!";
            $this->redirect('/cms/restaurants/cuisines');
        } catch (ApplicationException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/cms/restaurants/cuisines/edit/{$id}');
        }
    }

     #[RequireRole([UserRole::ADMIN])]
    public function delete($vars = []){
       
        $restaurantId = (int)($vars['id'] ?? 0);
        try {
            $restaurant = $this->restaurantService->getRestaurantById($restaurantId);
            if(!$restaurant){
                throw new \Exception('Restaurant not Found!');
            }

            $this->restaurantService->deleteRestaurant($restaurantId);
            $_SESSION['success'] = "Restaurant {$restaurant->name} was deleted successfully";

        } catch (ApplicationException $e) {
            $this->logService->exception('Restaurant', $e);
            $_SESSION['error'] = $e->getMessage();
        }
        $this->redirect('/cms/restaurants');
    }

    #[RequireRole([UserRole::ADMIN])]
    public function deleteCuisine($vars= []){
        $id = (int)($vars['id'] ?? 0);
        try {
            $this->cuisineService->deleteCuisine($id);
            $_SESSION['success'] = "Cuisine deleted!";
        } catch (ApplicationException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        $this->redirect('/cms/restaurants/cuisines');
    }

    #[RequireRole([UserRole::ADMIN])]
    public function removeGallery($vars = []): void
    {
        $restaurantId = (int)($vars['restaurantId'] ?? 0);
        $mediaId  = (int)($vars['mediaId']  ?? 0);

        try {
            $this->restaurantService->removeGalleryImage($restaurantId, $mediaId);
            $_SESSION['success'] = 'Gallery image removed.';
        } catch (\Throwable $e) {
            $this->logService->exception('Restaurant', $e);
            $_SESSION['error'] = 'Failed to remove gallery image.';
        }

        $this->redirect("/cms/restaurants/edit/{$restaurantId}");
    }
}