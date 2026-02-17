<?php

namespace App\Services;

use App\Models\Schedule;
use App\Services\Interfaces\IScheduleService;
use App\Repositories\Interfaces\IScheduleRepository;

class ScheduleService implements IScheduleService
{
    private IScheduleRepository $scheduleRepository;

    public function __construct(IScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * Get a single schedule by ID
     * @return Schedule|null
     */
    public function getScheduleById(int $scheduleId): ?Schedule
    {
        return $this->scheduleRepository->getScheduleById($scheduleId);
    }

    /**
     * Get all schedules with optional filters
     * @param string|null $eventType Filter by event type (e.g., 'Yummy', 'Jazz', 'Magic')
     * @param string|null $date Filter by date (format: 'Y-m-d')
     * @return Schedule[]
     */
    public function getAllSchedules(?string $eventType = null, ?string $date = null): array
    {
        return $this->scheduleRepository->getAllSchedules($eventType, $date);
    }

    /**
     * Get schedules for a specific event as an array of Schedule objects
     * @return Schedule[]
     */
    public function getSchedulesByEventId(int $eventId): array
    {
        return $this->scheduleRepository->getScheduleByEventId($eventId);
    }

    
   
}