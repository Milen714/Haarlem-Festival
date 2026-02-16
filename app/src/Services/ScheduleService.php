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
     * Get all schedules as an array of Schedule objects
     * @return Schedule[]
     */
    public function getAllSchedules(): array
    {
        return $this->scheduleRepository->getAllSchedules();
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