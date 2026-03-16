<?php

namespace App\Services;

use App\Repositories\HistoryRepository;
use App\Services\Interfaces\IHistoryService;
use App\ViewModels\History\TicketHistoryViewModel;

class HistoryService implements IHistoryService
{

    private HistoryRepository $historyRepository;

    public function __construct()
    {
        $this->historyRepository = new HistoryRepository();
    }

    public function getAvailableTourOptions(): TicketHistoryViewModel{

        $ticketOptions = $this->historyRepository->getAvailableTourOptions();
        $ticketPrices = $this->historyRepository->getTourTicketPrices();
        
        $ticketHistoryViewModel = new TicketHistoryViewModel();

        $ticketHistoryViewModel->availableDates = $ticketOptions['dates'] ?? [];
        $ticketHistoryViewModel->availableTimes = $ticketOptions['times'] ?? [];
        $ticketHistoryViewModel->availableLanguages = $ticketOptions['languages'] ?? [];
        $ticketHistoryViewModel->normalPrice = $ticketPrices['normal'] ?? 0.00;
        $ticketHistoryViewModel->familyPrice = $ticketPrices['family'] ?? 0.00;

        return $ticketHistoryViewModel;
    }


}