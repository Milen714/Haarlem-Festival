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

    public function getScheduleById(int $scheduleId): ?Schedule
    {
        return $this->scheduleRepository->getScheduleById($scheduleId);
    }

    public function getAllSchedules(?string $eventType = null, ?string $date = null): array
    {
        return $this->scheduleRepository->getAllSchedules($eventType, $date);
    }

    public function getSchedulesByEventId(int $eventId): array
    {
        return $this->scheduleRepository->getScheduleByEventId($eventId);
    }

    public function getSchedulesForArtistInEvent(int $artistId, int $eventId): array
    {
        $all = $this->scheduleRepository->getScheduleByEventId($eventId);

        $artistSchedules = array_values(array_filter(
            $all,
            fn($s) => $s->artist !== null && (int) $s->artist->artist_id === $artistId
        ));

        $grouped = [];

        foreach ($artistSchedules as $s) {
            if (!$s->date instanceof \DateTime) continue;

            $dateKey = $s->date->format('Y-m-d');

            $grouped[$dateKey][] = [
                'schedule_id'    => $s->schedule_id,
                'date'           => $s->date,
                'start_time'     => $s->start_time,
                'end_time'       => $s->end_time,
                'venue_name'     => $s->venue?->name ?? 'Venue TBA',
                'venue_address'  => $s->venue
                    ? trim(($s->venue->street_address ?? '') . ', ' . ($s->venue->city ?? ''))
                    : '',
                'venue_capacity' => $s->venue?->capacity,
                'total_capacity' => $s->total_capacity,
                'is_sold_out'    => $s->is_sold_out ?? false,
            ];
        }

        ksort($grouped);

        foreach ($grouped as &$slots) {
            usort($slots, function ($a, $b) {
                $ta = $a['start_time'] instanceof \DateTime ? $a['start_time']->getTimestamp() : 0;
                $tb = $b['start_time'] instanceof \DateTime ? $b['start_time']->getTimestamp() : 0;
                return $ta - $tb;
            });
        }
        unset($slots);

        return $grouped;
    }
   
}