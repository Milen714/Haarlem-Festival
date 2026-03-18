<?php
namespace App\Models\Yummy;

use App\Models\Restaurant;
use DateTime;
use Dom\Text;

class Session
{
    public ?int $session_id = null;
    public ?int $restaurantId = null;
    public ?string $session_type_name = null;
    public ?string $name = null;
    public ?string $icon_url = null;
    public DateTime $start_time;
    public DateTime $end_time; 
    public ?int $session_number = null;

    public function __construct() {}

    public function fromPDOData(array $data): void
    {
        $this->session_id = isset($data['session_id']) ? (int)$data['session_id'] : null;
        $this->restaurantId = isset($data['restaurant_id']) ? (int)$data['restaurant_id'] : null;
        $this->session_type_name = $data['session_type_name'] ?? null;
        $this->name = $data['session_type_name'] ?? null;
        $this->icon_url = $data['session_type_icon'] ?? null;
        $this->start_time = isset($data['start_time']) || isset($data['session_start_time']) 
            ? new \DateTime($data['start_time'] ?? $data['session_start_time']) 
            : null;
        $this->end_time = isset($data['end_time']) || isset($data['session_end_time']) 
            ? new \DateTime($data['end_time'] ?? $data['session_end_time']) 
            : null;
        $this->session_number = isset($data['session_number']) || isset($data['session_session_number']) 
            ? (int)($data['session_number'] ?? $data['session_session_number']) 
            : null;
    }

}