<?php 

namespace App\Services;

use App\Models\Restaurant;
use App\Repositories\RestaurantRepository;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\IMediaService;

class RestaurantService implements IRestaurantService
{
    private IRestaurantRepository $restaurantRepository;
    private IMediaService $mediaService;

    public function __construct()
    {
        $this->restaurantRepository = new RestaurantRepository();
        $this->mediaService = new MediaService();
    }

    public function getAllRestaurants(int $eventId, ?int $cuisineId = null): array
    {
        return $this->restaurantRepository->getAllRestaurants($eventId, $cuisineId);
    }

    public function showAllRestaurants(): array
    {
        return $this->restaurantRepository->showAllRestaurants();
    }

    public function getRestaurantById(int $id): ?Restaurant
    {
        return $this->restaurantRepository->getRestaurantById($id);
    }

    public function getRestaurantBySlug(string $slug): ?Restaurant
    {
        return $this->restaurantRepository->getRestaurantBySlug($slug);
    }

    public function getRestaurantsByEventId(int $eventId): array
    {
        return $this->restaurantRepository->getRestaurantsByEventId($eventId);
    }

    public function createRestaurant(Restaurant $restaurant): int
    {
        return $this->restaurantRepository->createRestaurant($restaurant);
    }

    public function updateRestaurant(Restaurant $restaurant): bool
    {
        return $this->restaurantRepository->updateRestaurant($restaurant);
    }

    public function deleteRestaurant(int $id): bool
    {
        return $this->restaurantRepository->deleteRestaurant($id);
    }

    public function getRestaurantDetail(int $id){
        $restaurant = $this->restaurantRepository->getRestaurantById($id);

        if (!$restaurant) {
            return null;
        }
        $restaurant->sessions = $this->restaurantRepository->getSessionsByRestaurant($id);
        $restaurant->dishes = $this->restaurantRepository->getDishessByRestaurant($id);
       
        return $restaurant;
    }

    public function fillRestaurantFromPostData(Restaurant $restaurant, array $data){
        $restaurant->name = trim($data['name']);
        $restaurant->short_description = !empty($data['short_description']) ? trim($data['short_description']) : null;
        $restaurant->welcome_text = !empty($data['welcome_text']) ?  trim($data['welcome_text']) : null;
        $restaurant->price_category = !empty($data['price_category']) ?  (int)$data['price_category'] : null;
        $restaurant->stars = !empty($data['stars']) ?  (int)$data['stars'] : null;
        $restaurant->review_count = !empty($data['review_count']) ?  (int)$data['review_count'] : null;
        $restaurant->website_url = !empty($data['website_url']) ? trim($data['website_url']) : null;
        $restaurant->chef_name = !empty($data['chef_name']) ? trim($data['chef_name']) : null;
        $restaurant->chef_bio_text = !empty($data['chef_bio_text']) ? trim($data['chef_bio_chef']) : null;

        return $restaurant;
    }

    public function processRestaurantRequest(Restaurant $restaurant, array $postData, array $files): Restaurant{

        $restaurant = $this->fillRestaurantFromPostData($restaurant, $postData);

        $restaurant = $this->handleImageUpload($restaurant, $files);

        return $restaurant;
    }

    public function createFromRequest(array $postData, array $files): Restaurant{
        $restaurant = new Restaurant();
        $restaurant = $this->processRestaurantRequest($restaurant, $postData, $files);

        //adds event to database
        $restaurant->event_id = 1; //Yummy event

        $this->restaurantRepository->createRestaurant($restaurant);
        return $restaurant;
    }

    public function updateFromRequest(int $restaurantId, array $postData, array $files): Restaurant {
        $restaurant = $this->restaurantRepository->getRestaurantById($restaurantId);

        if(!$restaurant){
            throw new \Exception('Restaurant not found');
        }

        $restaurant = $this->processRestaurantRequest($restaurant, $postData, $files);

        $this->restaurantRepository->updateRestaurant($restaurant);
        return $restaurant;
    }

    public function handleImageUpload(Restaurant $restaurant, array $files){
        //main image
        if(isset($files['main_image']) && $files['main_image']['error'] === UPLOAD_ERR_OK){
            $isUpdate = $restaurant->main_image && $restaurant->main_image->media_id;

            if ($isUpdate) {
                $result = $this->mediaService->replaceMedia(
                    $restaurant->main_image->media_id,
                    $files['main_image'],
                    'Restaurants',
                    $restaurant->name . 'main image'
                );
            }else{
                $result = $this->mediaService->uploadAndCreate(
                    $files['main_image'],
                    'Restaurants',
                    $restaurant->name . ' main image'
                );
            }

            if ($result['success']) {
                $restaurant->main_image = $result['media'];
            } else {
                throw new \Exception('Failed to upload main image:' . $result['error']);
            } 
        }

        //chef image
        if(isset($files['chef_img']) && $files['chef_img']['error'] === UPLOAD_ERR_OK){
            $isUpdate = $restaurant->chef_img && $restaurant->chef_img->media_id;

            if ($isUpdate) {
                $result = $this->mediaService->replaceMedia(
                    $restaurant->chef_img->media_id,
                    $files['chef_img'],
                    'Restaurants',
                    $restaurant->name . 'chef image'
                );
            }else{
                $result = $this->mediaService->uploadAndCreate(
                    $files['chef_img'],
                    'Restaurants',
                    $restaurant->name . ' chef image'
                );
            }

            if ($result['success']) {
                $restaurant->chef_img = $result['media'];
            } else {
                throw new \Exception('Failed to upload chef image:' . $result['error']);
            } 
        }

        return $restaurant;
    }
}