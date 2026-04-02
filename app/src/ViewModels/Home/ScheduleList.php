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

    /**
     * Accepts a flat array of Schedule objects and immediately categorises them into
     * time-of-day buckets. The buckets are what templates iterate over to render a
     * structured schedule — morning, afternoon, evening, night, and all-day slots.
     *
     * @param Schedule[] $schedules  All schedule slots to categorise, each with start_time and end_time set.
     */
    public function __construct(array $schedules)
    {
        $this->schedules = $schedules;
        $this->categorizeSchedules();
    }

    /**
     * Sorts each schedule slot into the appropriate time-of-day bucket based on start and end hour.
     * Slots running 10:00–17:00 exactly go into allDaySchedules. All others are bucketed by
     * start hour: morning (10–12), afternoon (13–16), evening (17–20), night (21+).
     * Populates the allDaySchedules, morningSchedules, afternoonSchedules, eveningSchedules,
     * and nightSchedules arrays as a side effect.
     *
     * @return void
     */
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
