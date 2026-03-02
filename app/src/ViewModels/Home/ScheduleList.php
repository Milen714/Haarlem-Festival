<?php 
namespace App\ViewModels\Home;
use App\Models\Schedule;


class ScheduleList{

    /** @var Schedule[] $schedules */
    public array $schedules = [];

    /** @var Schedule[] $allDaySchedules */
    public array $allDaySchedules = [];
    
    /** @var Schedule[] $morningSchedules */
    public array $morningSchedules = [];

    /** @var Schedule[] $afternoonSchedules */
    public array $afternoonSchedules = [];

    /** @var Schedule[] $eveningSchedules */
    public array $eveningSchedules = [];

    /** @var Schedule[] $nightSchedules */
    public array $nightSchedules = [];

    public function __construct(array $schedules)
    {
        $this->schedules = $schedules;
        $this->categorizeSchedules();
    }

    private function categorizeSchedules(): void
    {
        foreach ($this->schedules as $schedule) {
            $startHour = (int)explode(':', $schedule->start_time->format('H:i:s'))[0];
            $endHour = (int)explode(':', $schedule->end_time->format('H:i:s'))[0];

            if ($startHour == 10 && $endHour == 17) {
                $this->allDaySchedules[] = $schedule;
            } elseif ($startHour >= 10 && $startHour < 13) {
                $this->morningSchedules[] = $schedule;
            } elseif ($startHour >= 13 && $startHour < 17) {
                $this->afternoonSchedules[] = $schedule;
            } elseif ($startHour >= 17 && $endHour <= 21 && $startHour < 21) {
                 $this->eveningSchedules[] = $schedule;
            } elseif ($startHour >= 21) {
                 $this->nightSchedules[] = $schedule;
            }
        }
    }


}