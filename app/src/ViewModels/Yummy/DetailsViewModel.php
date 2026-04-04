<?php
namespace App\ViewModels\Yummy;

use App\Models\Restaurant;

class DetailsViewModel
{
   public Restaurant $restaurant;
   public array $schedules; // Array of schedule items (date, time, etc.)
   public array $groupedSchedules; // Schedules grouped by date for easier display
    public function __construct(Restaurant $restaurant, array $schedules)
    {
         $this->restaurant = $restaurant;
         $this->schedules = $schedules;
        
    }

    public function uniqueStartEndTimePairs() {
        $timePairs = [];
        foreach ($this->schedules as $schedule) {
            $timeKey = $schedule->start_time->format('H:i') . '_' . $schedule->end_time->format('H:i');
            if (!isset($timePairs[$timeKey])) {
                $timePairs[$timeKey] = [
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time
                ];
            }
        }
        return array_values($timePairs);
    }

    public function groupedSchedulesByDate() {
        $groupedSchedules = [];
        foreach ($this->schedules as $schedule) {
            $date = $schedule->date->format('Y-m-d');
            $groupedSchedules[$date][] = $schedule;
        }
        return $groupedSchedules;
    }
}