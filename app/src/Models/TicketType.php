<?php
namespace App\Models;
use App\Models\Enums\EventType;
use App\Models\EventCategory;
use App\Models\Schedule;

class TicketType
{
	public ?int $ticket_type_id = null;
	public ?Schedule $schedule = null;
	public ?string $name = null;
	public ?float $price = null;
	public ?string $description = null;
	public ?float $reservation_fee = null;
	public ?int $min_age = null;
	public ?int $max_age = null;
	public ?int $min_quantity = null;
	public ?int $max_quantity = null;
	public ?int $capacity = null;
	public ?string $language = null;
	public ?string $special_requirements = null;

	public function __construct() {}

	public function fromPDOData(array $data): void
	{
		$this->ticket_type_id = isset($data['ticket_type_id']) ? (int)$data['ticket_type_id'] : null;
		$this->name = $data['name'] ?? null;
		$this->price = isset($data['price']) ? (float)$data['price'] : null;
		$this->description = $data['description'] ?? null;
		$this->reservation_fee = isset($data['reservation_fee']) ? (float)$data['reservation_fee'] : null;
		$this->min_age = isset($data['min_age']) ? (int)$data['min_age'] : null;
		$this->max_age = isset($data['max_age']) ? (int)$data['max_age'] : null;
		$this->min_quantity = isset($data['min_quantity']) ? (int)$data['min_quantity'] : null;
		$this->max_quantity = isset($data['max_quantity']) ? (int)$data['max_quantity'] : null;
		$this->capacity = isset($data['capacity']) ? (int)$data['capacity'] : null;
		$this->language = $data['language'] ?? null;
		$this->special_requirements = $data['special_requirements'] ?? null;
	}
}