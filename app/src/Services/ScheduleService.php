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

    /**
     * Get schedule grouped by date for a specific event
     * Returns an associative array grouped by date with performance details
     */
    public function getScheduleByEventIdGrouped(int $eventId): array
    {
        $schedules = $this->scheduleRepository->getScheduleByEventId($eventId);
        
        // Group by date
        $grouped = [];
        foreach ($schedules as $schedule) {
            /** @var Schedule $schedule */
            $dateKey = $schedule->date ? $schedule->date->format('Y-m-d') : 'unknown';
            
            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = [
                    'date' => $dateKey,
                    'day_name' => $schedule->date ? $schedule->date->format('l') : '',
                    'day_number' => $schedule->date ? $schedule->date->format('d') : '',
                    'venue' => $schedule->venue?->name ?? '',
                    'performances' => [],
                    'total_performances' => 0,
                    'is_free' => false,
                    'label' => null
                ];
            }
            
            $grouped[$dateKey]['performances'][] = [
                'schedule_id' => $schedule->schedule_id,
                'start_time' => $schedule->start_time ? $schedule->start_time->format('H:i') : '',
                'end_time' => $schedule->end_time ? $schedule->end_time->format('H:i') : '',
                'artist_name' => $schedule->artist_name,
                'artist_slug' => $schedule->artist_slug,
                'venue' => $schedule->venue?->name ?? '',
                'is_sold_out' => $schedule->is_sold_out,
                'tickets_available' => $schedule->hasAvailableTickets()
            ];
            
            $grouped[$dateKey]['total_performances']++;
            
            // Check if Sunday (free event)
            if ($schedule->date && $schedule->date->format('N') == 7) {
                $grouped[$dateKey]['is_free'] = true;
                $grouped[$dateKey]['label'] = 'Closing Day';
            }
        }
        
        return $grouped;
    }

    /**
     * Legacy method - kept for backwards compatibility
     * @deprecated Use getScheduleByEventIdGrouped() instead
     */
    public function getScheduleByEventId(int $eventId): array
    {
        return $this->getScheduleByEventIdGrouped($eventId);
    }
}