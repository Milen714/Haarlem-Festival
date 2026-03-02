<?php
namespace App\ViewModels\Home;
use App\Models\History\Landmark;
use App\Models\Venue;

class StartingPoints
{
    /** @var Landmark[] $landmarks */
    public array $landmarks = [];

    /** @var Venue[] $venues */
    public array $venues = [];

    public array $startingPoints = [];

    public function __construct(array $landmarks, array $venues)
    {
        $this->landmarks = $landmarks;
        $this->venues = $venues;
        $this->startingPoints = array_merge($landmarks, $venues);
    }

    
}