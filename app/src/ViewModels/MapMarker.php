<?php

namespace App\ViewModels;

use App\Models\Venue;
use App\Models\Landmark;
use App\Models\Enums\EventType;

class MapMarker
{
    public ?int $id;
    public string $name;
    public ?float $latitude;
    public ?float $longitude;
    public ?string $description;
    public ?string $imageUrl;
    public string $type; // 'venue' or 'landmark'
    public ?string $address;
    public ?string $iconPath; // Event-specific icon path

    /**
     * Constructor accepts either a Venue or Landmark object
     * and maps the relevant fields for map display.
     */
    public function __construct(Venue|Landmark $location)
    {
        if ($location instanceof Venue) {
            $this->mapFromVenue($location);
        } elseif ($location instanceof Landmark) {
            $this->mapFromLandmark($location);
        }
    }

    /**
     * Maps properties from a Venue object.
     */
    private function mapFromVenue(Venue $venue): void
    {
        $this->id = $venue->venue_id;
        $this->name = $venue->name;
        $this->latitude = $venue->latitude;
        $this->longitude = $venue->longitude;
        $this->description = $venue->description_html;
        $this->imageUrl = $venue->getImagePath();
        $this->type = 'venue';
        $this->address = $venue->getFullAddress();
        $this->iconPath = $this->getIconPathForEventType($venue->event_category?->type);
    }

    /**
     * Maps properties from a Landmark object.
     */
    private function mapFromLandmark(Landmark $landmark): void
    {
        $this->id = $landmark->landmark_id;
        $this->name = $landmark->name;
        $this->latitude = $landmark->latitude;
        $this->longitude = $landmark->longitude;
        $this->description = $landmark->short_description;
        $this->imageUrl = $landmark->main_image_id?->file_path ? '/' . ltrim($landmark->main_image_id->file_path, '/') : '/Assets/Home/ImagePlaceholder.png';
        $this->type = 'landmark';
        $this->address = $landmark->name; // Landmarks don't have full address, use name
        $this->iconPath = $this->getIconPathForEventType($landmark->event_category?->type);
    }

    /**
     * Gets the icon path based on EventType.
     */
    private function getIconPathForEventType(?EventType $eventType): ?string
    {
        return match ($eventType) {
            EventType::Jazz => '/Assets/Home/MapJazzIcon.svg',
            EventType::Yummy => '/Assets/Home/MapYummyIcon.svg',
            EventType::History => '/Assets/Home/MapHistoryIcon.svg',
            EventType::Dance => '/Assets/Home/MapDanceIcon.svg',
            EventType::Magic => '/Assets/Home/MapMagicIcon.svg',
            default => '/Assets/Home/MapDefaultIcon.svg',
        };
    }

    /**
     * Convert to array for JSON serialization.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'description' => $this->description,
            'imageUrl' => $this->imageUrl,
            'type' => $this->type,
            'address' => $this->address,
            'iconPath' => $this->iconPath,
        ];
    }
}