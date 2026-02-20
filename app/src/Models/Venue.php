<?php
namespace App\Models;

class Venue
{
    public ?int $venue_id = null;
    public string $name = '';
    public string $street_address = '';
    public ?string $postal_code = null;
    public string $city = '';
    public ?string $country = null;
    public ?string $description_html = null;
    public ?int $capacity = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?Media $venue_image = null;

    public function __construct(){}

    /**
     * Get full address as single string
     */
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->postal_code,
            $this->city,
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get venue image path with leading slash
     */
    public function getImagePath(): string
    {
        if ($this->venue_image && $this->venue_image->file_path) {
            $path = $this->venue_image->file_path;
            return str_starts_with($path, '/') ? $path : '/' . $path;
        }
        
        return '/Assets/Home/ImagePlaceholder.png';
    }

    /**
     * Get image alt text
     */
    public function getImageAlt(): string
    {
        return $this->venue_image?->alt_text ?? $this->name ?? 'Venue';
    }

    /**
     * Check if venue has image
     */
    public function hasImage(): bool
    {
        return $this->venue_image !== null && !empty($this->venue_image->file_path);
    }

    /**
     * Format capacity for display
     */
    public function getCapacityDisplay(): string
    {
        if (!$this->capacity || $this->capacity === 0) {
            return 'Open Air';
        }
        
        return number_format($this->capacity) . ' capacity';
    }

    /**
     * Get Google Maps link
     */
    public function getMapLink(): string
    {
        $address = urlencode($this->getFullAddress());
        return "https://www.google.com/maps/search/?api=1&query={$address}";
    }

    public function fromPDOData(array $data): void 
{
    $this->venue_id = isset($data['venue_id']) ? (int)$data['venue_id'] : null;
    $this->name = $data['venue_name'] ?? ($data['name'] ?? '');
    $this->street_address = $data['venue_address'] ?? ($data['street_address'] ?? '');
    $this->postal_code = $data['venue_postal_code'] ?? ($data['postal_code'] ?? null);
    $this->city = $data['venue_city'] ?? ($data['city'] ?? '');
    $this->country = $data['venue_country'] ?? ($data['country'] ?? null);
    $this->description_html = $data['venue_description_html'] ?? ($data['description_html'] ?? null);
    $this->capacity = isset($data['venue_capacity']) ? (int)$data['venue_capacity'] : (isset($data['capacity']) ? (int)$data['capacity'] : null);
    $this->phone = $data['venue_phone'] ?? ($data['phone'] ?? null);
    $this->email = $data['venue_email'] ?? ($data['email'] ?? null);

    $mediaId = $data['venue_image_id'] ?? $data['media_id'] ?? null;
    $filePath = $data['image_path'] ?? $data['file_path'] ?? null;
    $altText = $data['image_alt'] ?? $data['alt_text'] ?? null;
    
    if ($mediaId && $filePath) {
        $this->venue_image = new Media();
        $this->venue_image->fromPDOData([
            'media_id' => $mediaId,
            'file_path' => $filePath,
            'alt_text' => $altText,
        ]);
    }
}
}