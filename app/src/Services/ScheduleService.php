<?php

namespace App\Services;

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
     * Get schedule grouped by date for a specific event
     */
    public function getScheduleByEventId(int $eventId): array
    {
        $schedules = $this->scheduleRepository->getScheduleByEventId($eventId);
        
        // Group by date
        $grouped = [];
        foreach ($schedules as $schedule) {
            $date = $schedule->date;
            
            if (!isset($grouped[$date])) {
                $grouped[$date] = [
                    'date' => $date,
                    'day_name' => date('l', strtotime($date)),
                    'day_number' => date('d', strtotime($date)),
                    'venue' => $schedule->venue_name,
                    'performances' => [],
                    'total_performances' => 0,
                    'is_free' => false,
                    'label' => null
                ];
            }
            
            $grouped[$date]['performances'][] = [
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'artist_name' => $schedule->artist_name,
                'artist_slug' => $schedule->artist_slug
            ];
            
            $grouped[$date]['total_performances']++;
            
            // Check if Sunday (free event)
            if (date('N', strtotime($date)) == 7) {
                $grouped[$date]['is_free'] = true;
                $grouped[$date]['label'] = 'Closing Day';
            }
        }
        
        return $grouped;
    }
}