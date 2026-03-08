<?php

namespace App\Services;

use App\Models\Schedule;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IVenueService;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\ILandmarkService;
use App\Repositories\Interfaces\IScheduleRepository;

class ScheduleService implements IScheduleService
{
    private IScheduleRepository $scheduleRepository;
    private IVenueService $venueService;
    private IArtistService $artistService;
    private IRestaurantService $restaurantService;
    private ILandmarkService $landmarkService;

    public function __construct(
        IScheduleRepository $scheduleRepository,
        IVenueService $venueService,
        IArtistService $artistService,
        IRestaurantService $restaurantService,
        ILandmarkService $landmarkService
    ) {
        $this->scheduleRepository  = $scheduleRepository;
        $this->venueService        = $venueService;
        $this->artistService       = $artistService;
        $this->restaurantService   = $restaurantService;
        $this->landmarkService     = $landmarkService;
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

    public function createFromRequest(array $postData): Schedule
    {
        $this->validateScheduleData($postData);
        $schedule = $this->buildScheduleFromPostData(new Schedule(), $postData);
        $success = $this->scheduleRepository->create($schedule);
        if (!$success) {
            throw new \Exception('Failed to create schedule in database');
        }
        return $schedule;
    }

    public function updateFromRequest(int $scheduleId, array $postData): Schedule
    {
        $schedule = $this->scheduleRepository->getScheduleById($scheduleId);
        if (!$schedule) {
            throw new \Exception('Schedule not found');
        }
        $this->validateScheduleData($postData);
        $schedule = $this->buildScheduleFromPostData($schedule, $postData);
        $success = $this->scheduleRepository->update($schedule);
        if (!$success) {
            throw new \Exception('Failed to update schedule in database');
        }
        return $schedule;
    }

    public function deleteSchedule(int $scheduleId): bool
    {
        $schedule = $this->scheduleRepository->getScheduleById($scheduleId);
        if (!$schedule) {
            throw new \Exception('Schedule not found');
        }
        return $this->scheduleRepository->delete($scheduleId);
    }

    public function getAllEventCategories(): array
    {
        return $this->scheduleRepository->getAllEventCategories();
    }

    public function getAllVenues(): array
    {
        return $this->venueService->getAllVenues();
    }

    public function getAllArtists(): array
    {
        return $this->artistService->getAllArtists();
    }

    public function getAllRestaurants(): array
    {
        try {
            return $this->restaurantService->getAllRestaurants();
        } catch (\Exception $e) {
            error_log("ScheduleService: could not load restaurants - " . $e->getMessage());
            return [];
        }
    }

    public function getAllLandmarks(): array
    {
        return $this->landmarkService->getAllLandmarks();
    }

    private function validateScheduleData(array $data): void
    {
        if (empty($data['event_id'])) {
            throw new \Exception('Event category is required');
        }
        if (empty($data['date'])) {
            throw new \Exception('Date is required');
        }
        if (empty($data['start_time'])) {
            throw new \Exception('Start time is required');
        }
        if (empty($data['end_time'])) {
            throw new \Exception('End time is required');
        }
        if (empty($data['total_capacity']) || (int)$data['total_capacity'] < 1) {
            throw new \Exception('Total capacity must be at least 1');
        }
    }

    private function buildScheduleFromPostData(Schedule $schedule, array $data): Schedule
    {
        $schedule->event_id       = (int)$data['event_id'];
        $schedule->venue_id       = !empty($data['venue_id'])      ? (int)$data['venue_id']      : null;
        $schedule->artist_id      = !empty($data['artist_id'])     ? (int)$data['artist_id']     : null;
        $schedule->restaurant_id  = !empty($data['restaurant_id']) ? (int)$data['restaurant_id'] : null;
        $schedule->landmark_id    = !empty($data['landmark_id'])   ? (int)$data['landmark_id']   : null;
        $schedule->date           = !empty($data['date'])       ? new \DateTime($data['date'])       : null;
        $schedule->start_time     = !empty($data['start_time']) ? new \DateTime($data['start_time']) : null;
        $schedule->end_time       = !empty($data['end_time'])   ? new \DateTime($data['end_time'])   : null;
        $schedule->total_capacity = (int)($data['total_capacity'] ?? 0);
        $schedule->tickets_sold   = (int)($data['tickets_sold'] ?? 0);
        $schedule->is_sold_out    = isset($data['is_sold_out']) && $data['is_sold_out'] == '1';
        return $schedule;
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
