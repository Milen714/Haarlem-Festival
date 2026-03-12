<?php
namespace App\Models;
use App\Models\Enums\EventType;
use App\Models\EventCategory;
use App\Models\Schedule;
use App\Models\TicketScheme;

class TicketType
{
	public ?int $ticket_type_id = null;
	public ?Schedule $schedule = null;
	public ?TicketScheme $ticket_scheme = null;
	public ?string $description = null;
	public ?int $min_age = null;
	public ?int $max_age = null;
	public ?int $min_quantity = null;
	public ?int $max_quantity = null;
	public ?int $capacity = null;
	public ?string $special_requirements = null;

	public function __construct() {}

	public function fromPDOData(array $data): void
	{
		$this->ticket_type_id = isset($data['ticket_type_id']) ? (int)$data['ticket_type_id'] : null;
		$this->description = $data['description'] ?? null;
		$this->min_age = isset($data['min_age']) ? (int)$data['min_age'] : null;
		$this->max_age = isset($data['max_age']) ? (int)$data['max_age'] : null;
		$this->min_quantity = isset($data['min_quantity']) ? (int)$data['min_quantity'] : null;
		$this->max_quantity = isset($data['max_quantity']) ? (int)$data['max_quantity'] : null;
		$this->capacity = isset($data['capacity']) ? (int)$data['capacity'] : null;
		$this->special_requirements = $data['special_requirements'] ?? null;
		$this->schedule = isset($data['schedule_id']) ? (new Schedule())->hydrateSchedule($data) : null;

		$this->ticket_scheme = isset($data['ticket_scheme_id']) ? (new TicketScheme())->fromPDOData($data) : null;
	}
}