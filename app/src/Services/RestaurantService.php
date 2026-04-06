<?php 

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Restaurant;
use App\Models\Yummy\Session;
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
    public function getAllSessionsTypes(): array{
        return $this->restaurantRepository->getAllSessionsTypes();
    }
    public function getSessionsByRestaurant(int $restaurantId): array{
        return $this->restaurantRepository->getSessionsByRestaurant($restaurantId);
    }
    public function createSession(Session $session): Session
    {
        return $this->restaurantRepository->createSession($session);
    }
    public function deleteSessionsByRestaurant(int $restaurantId): bool{
        return $this->restaurantRepository->deleteSessionsByRestaurant($restaurantId);
    }

    public function getRestaurantDetail(int $id){
        $restaurant = $this->restaurantRepository->getRestaurantById($id);

        if (!$restaurant) {
            return null;
        }
        $restaurant->sessions = $this->restaurantRepository->getSessionsByRestaurant($id);
       
        return $restaurant;
    }

    public function processRestaurantRequest(Restaurant $restaurant, array $postData, array $files): Restaurant{

        $restaurant = Restaurant::createFromPostData($postData);

        $restaurant = $this->handleImageUpload($restaurant, $files);

        return $restaurant;
    }

    public function createFromRequest(array $postData, array $files): Restaurant{
        //get the restaurant data from the post data and create a restaurant instance
        $restaurant = Restaurant::createFromPostData($postData);

        $restaurant = $this->handleImageUpload($restaurant, $files);
        //create the restaurant to get the id for the gallery and the cuisine relation and sessions
        $restaurantId = $this->restaurantRepository->createRestaurant($restaurant);
        //upload the gallery images if there are any
        $this->uploadRestauratGallery($restaurantId, $restaurant, $files['gallery_images'] ?? []);
        //get the cuisines by id and slice it so only up to 3 are displayed
        $cuisineIds = $postData['cuisines'] ?? [];
        $cuisineIds = array_slice($cuisineIds, 0, 3);
        //get the sessions
        $this->handleSessions($restaurantId, $postData);
        //sync the cuisines with the restaurant
        $this->restaurantRepository->syncRestaurantCuisines($restaurantId, $cuisineIds);

        return $restaurant;
    }
    /**
     * Updates a restaurant based on the provided ID, post data, and files.
     *
     * @param int $restaurantId The ID of the restaurant to update.
     * @param array $postData The data from the form submission.
     * @param array $files The uploaded files from the form submission.
     * @return Restaurant The updated restaurant instance.
     * @throws \Exception If the restaurant is not found or if image upload fails.
     */
    public function updateFromRequest(int $restaurantId, array $postData, array $files): Restaurant {
        $restaurant = $this->restaurantRepository->getRestaurantById($restaurantId);
        if(!$restaurant){
            throw new ResourceNotFoundException('Restaurant not found');
        }
        
        // Update restaurant data using the model's method which handles empty fields properly
        $restaurant->fillRestaurantFromPostData($postData);
        $restaurant = $this->handleImageUpload($restaurant, $files);
        $this->restaurantRepository->updateRestaurant($restaurant);
        
        //get the cuisines by id and slice it so only up to 3 are displayed
        $cuisineIds = $postData['cuisines'] ?? [];
        if (!$cuisineIds) {
            throw new ResourceNotFoundException('Could not find cuisines');
        }
        $cuisineIds = array_slice($cuisineIds, 0, 3);
        
        //replace the gallery images if there are any
        $this->replaceRestaurantGalleryImages($restaurant, $files);
        $this->uploadRestauratGallery($restaurantId, $restaurant, $files['gallery_images'] ?? []);
        
        //sync the cuisines with the restaurant
        $this->restaurantRepository->syncRestaurantCuisines($restaurantId, $cuisineIds);
        
        //handle the sessions by deleting the old ones and creating new ones based on the form data
        $this->handleSessions($restaurantId, $postData);
        
        return $restaurant;
    }

    /**
     * Handles the upload of images for a restaurant.
     * This method processes both the main image and the chef image, 
     * checking if they are being updated or created for the first time. 
     * It uses the MediaService to upload or replace images and updates the restaurant 
     * instance with the new media information.
     * @param Restaurant $restaurant The restaurant instance.
     * @param array $files The uploaded files.
     * @return Restaurant The updated restaurant instance.
     * @throws \Exception If image upload fails.
     */
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
                throw new ResourceNotFoundException('Failed to upload main image:' . $result['error']);
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
                throw new ResourceNotFoundException('Failed to upload chef image:' . $result['error']);
            } 
        }
        return $restaurant;
    }

    /**
     * Uploads gallery images for a restaurant.
     * This method checks if there are any gallery images to upload and processes each one.
     * If the restaurant does not have an existing gallery, it creates one. 
     * It then uploads each image and adds it to the restaurant's gallery with the correct order.
     * @param int $restaurantId The ID of the restaurant.
     * @param ?Restaurant $restaurant The restaurant instance.
     * @param array $files The uploaded files.
     * @return void
     */
    public function uploadRestauratGallery(int $restaurantId, ?Restaurant $restaurant, array $files): void{
        if (empty($files['name'])) {
            return;
        }

        $pdoGalleryId = $restaurant?->gallery?->gallery_id;
        if (!$pdoGalleryId) {
            $pdoGalleryId = $this->restaurantRepository->createGalleryForRestaurant($restaurantId, ($restaurant->name ?? 'Restaurant') . ' Gallery' );
        }

        //looping the files to upload multiple at one and add them to the gallery with the correct order
        foreach($files['name'] as $i => $name){
            if (empty($name)) {
                continue;
            }
            
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                // Log the error but don't throw - allow other images to upload even if one fails
                error_log("Gallery image upload error for restaurant {$restaurantId}: " . $files['error'][$i]);
                continue;
            }
            
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];

            $altText = ($restaurant->name ?? 'Dish') . ' Image';
            $result = $this->mediaService->uploadAndCreate($file, 'Yummy/Restaurant', $altText);

            if (!($result['success'] ?? false)) {
                error_log("Failed to upload gallery image for restaurant {$restaurantId}: " . ($result['error'] ?? 'Unknown error'));
                continue;
            }

            $order = $this->restaurantRepository->getNextGalleryOrder($pdoGalleryId);

            $this->restaurantRepository->addMediaToGallery(
                $pdoGalleryId,
                $result['media']->media_id,
                $order
            );
        }

    }

    /**
     * This method replaces existing gallery images for a restaurant based on the provided files.
     * It checks for files that have keys starting with 'gallery_replace_' to identify which images to replace.
     * For each identified file, it uses the MediaService to replace the existing media with the new file.
     * @param ?Restaurant $restaurant The restaurant instance.
     * @param array $files The uploaded files.
     * @return void
     * @throws \Exception If image replacement fails.
     */
    public function replaceRestaurantGalleryImages(?Restaurant $restaurant, array $files){
        if (!$restaurant?->gallery?->gallery_id) {
            return;
        }

        foreach ($files as $key => $file) {
            if (!str_starts_with($key, 'gallery_replace_')) {
                continue;
            }
            
            // Skip if not a valid single file upload (array-style uploads like gallery_images have different structure)
            if (!is_array($file) || !isset($file['error']) || is_array($file['error'])) {
                continue;
            }

            $mediaId = (int)str_replace('gallery_replace_', '', $key);

            if ($file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $altText = ($restaurant->name ?? 'Dish') . ' Image';

            $result = $this->mediaService->replaceMedia(
                $mediaId,
                $file,
                'Yummy/Restaurant',
                $altText
            );
            if (!($result['success'] ?? false)) {
                throw new ApplicationException(('Failed to replace image'));
            }
        }
    }

    /**
     * Removes a gallery image from a restaurant's gallery based on the provided restaurant ID and media ID.
     * This method first checks if the restaurant has an associated gallery. If it does, 
     * it calls the RestaurantRepository to remove the specified media from the gallery.
     * @param int $restaurantId The ID of the restaurant.
     * @param int $mediaId The ID of the media to be removed from the gallery.
     * @return bool Returns true if the media was successfully removed, or false if the restaurant does not have a gallery.
     * @throws \Exception If the restaurant is not found or if the gallery removal fails.
     * 
     */
    public function removeGalleryImage(int $restaurantId, int $mediaId): bool{
        $restaurant = $this->restaurantRepository->getRestaurantById($restaurantId);
        if (!$restaurant?->gallery?->gallery_id) {
            return false;
        }   

        return $this->restaurantRepository->removeMediaFromGallery(
            $restaurant->gallery->gallery_id,
            $mediaId
        );
    }

    /**
     * Handles the creation and updating of restaurant sessions.
     * This method first deletes all existing sessions for the 
     * specified restaurant ID to ensure that the session data is fully replaced.
     * It then iterates through the session data provided in the post data, 
     * creating new Session instances for each valid session entry and saving 
     * them to the database using the RestaurantRepository.
     * @param int $restaurantId The ID of the restaurant.
     * @param array $postData The post data containing session information.
     * @return void
     */
    private function handleSessions(int $restaurantId, array $postData){
        //delete old pairing
        $this->restaurantRepository->deleteSessionsByRestaurant($restaurantId);
        foreach ($postData['sessions'] ?? [] as $index => $data) {
            if(empty($data['session_type_id']) && empty($data['start_time']) && empty($data['end_time'])){
                continue;
            }       
            //create new sessions pairing
            $session = new Session();
            $session->session_id = (int)$data['session_type_id'];
            $session->restaurantId = $restaurantId;
            $session->start_time = new \DateTime($data['start_time']);
            $session->end_time = new \DateTime($data['end_time']);
            $session->session_number = $index + 1;

            $this->restaurantRepository->createSession($session);
        }
    }
        public function getEvents(): array{
            return $this->restaurantRepository->getEvents();
        }
}