<?php
namespace App\Models;
use App\Models\Enums\EventType;
use App\Models\EventCategory;
use App\Models\Venue;
use DateTime;

class Schedule
{
    public ?int $schedule_id = null;
    public ?EventCategory $event_category = null;
    public DateTime $date;
    public DateTime $start_time;
    public DateTime $end_time;
    public ?Venue $venue = null; 
    public ?int $total_capacity = null;
    public ?int $tickets_sold = null;
    public ?bool $is_sold_out = null;
    

    public function __construct(){}

    public function fromPDOData(array $data): void {
        $this->schedule_id = isset($data['schedule_id']) ? (int)$data['schedule_id'] : null;
        $this->start_time = $data['start_time'] ?? null;
        $this->end_time = $data['end_time'] ?? null;
        $this->is_sold_out = isset($data['is_sold_out']) ? (bool)$data['is_sold_out'] : null;
    }
}