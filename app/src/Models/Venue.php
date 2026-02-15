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
        $this->name = $data['name'] ?? '';
        $this->street_address = $data['street_address'] ?? '';
        $this->postal_code = $data['postal_code'] ?? null;
        $this->city = $data['city'] ?? '';
        $this->country = $data['country'] ?? null;
        $this->description_html = $data['description_html'] ?? null;
        $this->capacity = isset($data['capacity']) ? (int)$data['capacity'] : null;
        $this->phone = $data['phone'] ?? null;
        $this->email = $data['email'] ?? null;
    }
}