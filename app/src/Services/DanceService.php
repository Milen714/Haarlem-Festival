<?php
namespace App\Services;

use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IPageService;
use App\Services\Interfaces\IDanceService;
use App\Services\Interfaces\ITicketService;

class DanceService implements IDanceService
{
    private $ticketService;
    private $scheduleService;
    private $artistService;
    private $pageService;

    public function __construct(
        ITicketService $ticketService,
        IScheduleService $scheduleService,
        IArtistService $artistService,
        IPageService $pageService
    ) {
        $this->ticketService = $ticketService;
        $this->scheduleService = $scheduleService;
        $this->artistService = $artistService;
        $this->pageService = $pageService;
    }

    public function getDanceOverviewData(string $slug, int $eventId): array
    {
        $pageData = $this->pageService->getPageBySlug($slug);
        $backtoback = $this->scheduleService->getBackToBackSpecialsByEventId($eventId);

        $ticketLookup = [];
        foreach ($backtoback as $session) {
            $types = $this->ticketService->getTicketTypesByScheduleId($session->schedule_id);
            if (!empty($types)) {
                $ticketLookup[$session->schedule_id] = [
                    'id' => $types[0]->ticket_type_id,
                    'price' => $types[0]->ticket_scheme->price ?? 0
                ];
            }
        }

        $passTicketTypes = $this->ticketService->getTicketTypesBySchemeEnums([
            'DANCE_ALL_DAY', 
            'DANCE_WEEK_PASS'
        ]);

        return [
            'pageData' => $pageData,
            'artists' => $this->artistService->getArtistsByEventId($eventId),
            'backtoback' => $backtoback,
            'ticketLookup' => $ticketLookup,
            'passTicketTypes' => $passTicketTypes,
            'sections' => $this->organizeSections($pageData->content_sections ?? [])
        ];
    }

    private function organizeSections(array $sections): array
    {
        $organized = [];
        foreach ($sections as $index => $section) {
            $rawTitle = $section->title ?? $section->section_title ?? null;

            if (!$rawTitle) {
                $key = 'section_' . $index;
            } else {
                $key = strtolower(str_replace(' ', '', (string)$rawTitle));
            }

            $organized[$key] = $section;
        }
        return $organized;
    }
}