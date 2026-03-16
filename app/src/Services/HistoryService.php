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

        $options = $this->historyRepository->getAvailableTourOptions();


        $ticketPrices = $this->historyRepository->getTourTicketPrices();
        
        $ticketHistoryViewModel = new TicketHistoryViewModel();

        $dates = [];
        $times = [];
        $languages = [];

        foreach ($options as $row) {
            if (isset($row['date']) && !in_array($row['date'], $dates)) {
                $dates[] = $row['date'];
            }
            if (isset($row['time']) && !in_array($row['time'], $times)) {
                $times[] = $row['time'];
            }
            if (isset($row['language']) && !in_array($row['language'], $languages)) {
                $languages[] = $row['language'];
            }
        }

        $ticketHistoryViewModel->availableDates = $dates;
        $ticketHistoryViewModel->availableTimes = $times;
        $ticketHistoryViewModel->availableLanguages = $languages;
        
        $ticketHistoryViewModel->normalPrice = $ticketPrices['normal'] ?? 0.00;
        $ticketHistoryViewModel->familyPrice = $ticketPrices['family'] ?? 0.00;

        return $ticketHistoryViewModel;
    }


}