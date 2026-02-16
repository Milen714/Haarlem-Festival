<?php
namespace App\Models;

class Venue
{
    public ?int $venue_id = null;
    public string $name;
    public string $street_address;
    public ?string $postal_code = null;
    public string $city;
    public ?string $country = null;
    public ?string $description_html = null;
    public ?int $capacity = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?Media $venue_image = null;

    


    public function __construct(){}

    public function fromPDOData(array $data): void {
        $this->venue_id = isset($data['venue_id']) ? (int)$data['venue_id'] : null;
        $this->name = $data['venue_name'] ?? '';
        $this->street_address = $data['venue_address'] ?? '';
        $this->postal_code = $data['venue_postal_code'] ?? null;
        $this->city = $data['venue_city'] ?? '';
        $this->country = $data['venue_country'] ?? null;
        $this->description_html = $data['venue_description_html'] ?? null;
        $this->capacity = isset($data['venue_capacity']) ? (int)$data['venue_capacity'] : null;
        $this->phone = $data['venue_phone'] ?? null;
        $this->email = $data['venue_email'] ?? null;
    }
}