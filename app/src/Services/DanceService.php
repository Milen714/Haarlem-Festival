<?php
namespace App\Services;

use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IPageService;
use App\Services\Interfaces\IDanceService;
use App\Services\Interfaces\ITicketService;
use App\Exceptions\DanceEventNotFoundException;
use App\Exceptions\ArtistNotFoundException;
use App\Exceptions\ScheduleNotFoundException;
use App\Exceptions\ApplicationException;

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
        try {
            $pageData = $this->pageService->getPageBySlug($slug);
            
            if (!$pageData) {
                throw new DanceEventNotFoundException('Dance event page not found');
            }
            
            $backtoback = $this->scheduleService->getBackToBackSpecialsByEventId($eventId);
            
            if (empty($backtoback)) {
                throw new ScheduleNotFoundException('No schedule data available for dance event');
            }

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

            $artists = $this->artistService->getArtistsByEventId($eventId);
            
            if (empty($artists)) {
                throw new ArtistNotFoundException('No artists found for dance event');
            }

            return [
                'pageData' => $pageData,
                'artists' => $artists,
                'backtoback' => $backtoback,
                'ticketLookup' => $ticketLookup,
                'passTicketTypes' => $passTicketTypes,
                'sections' => $this->organizeSections($pageData->content_sections ?? [])
            ];
            
        } catch (DanceEventNotFoundException | ArtistNotFoundException | ScheduleNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ApplicationException('Failed to retrieve dance event data', 0, $e);
        }
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