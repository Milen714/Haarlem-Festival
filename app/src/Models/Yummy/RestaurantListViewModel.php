<?php 

namespace App\Models\Yummy;

use App\Models\Cuisine;
use App\Models\Restaurant;

class RestaurantListViewModel{
    /**
     * @var Restaurant[]
     */
    public array $restaurants;
    //public ?Gallery $gallery = null;

    /**
     * @var Cuisine[]
     */ 
    public array $cuisines = [];
    public object $pageData;
    public ?int $selectedCuisineId = null;

    public function __construct(object $pageData, array $restaurants, array $cuisines, ?int $selectedCuisine)
    {
        $this->pageData = $pageData;
        $this->restaurants = $restaurants;
        $this->cuisines = $cuisines;
        $this->selectedCuisineId = $selectedCuisine;
    }
}