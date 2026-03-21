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

    public function getAvailableTourOptions(): TicketHistoryViewModel
    {
        $rawOptions = $this->historyRepository->getAvailableTourOptions();
        $ticketPrices = $this->historyRepository->getTourTicketPrices();
        
        $viewModel = new TicketHistoryViewModel();

        // Creamos el Árbol de Decisiones
        $ticketOptions = [];

        foreach ($rawOptions as $row) {
            $language = $row['language'];
            $date = $row['date'];
            $time = $row['time'];

            // Si el idioma no existe en el árbol, lo creamos
            if (!isset($ticketOptions[$language])) {
                $ticketOptions[$language] = [];
            }
            // Si la fecha no existe dentro de ese idioma, la creamos
            if (!isset($ticketOptions[$language][$date])) {
                $ticketOptions[$language][$date] = [];
            }
            // Agregamos la hora a esa fecha exacta (evitando duplicados)
            if (!in_array($time, $ticketOptions[$language][$date])) {
                $ticketOptions[$language][$date][] = $time;
            }
        }

        $viewModel->options = $ticketOptions;
        $viewModel->normalPrice = $ticketPrices['normal'] ?? 0.00;
        $viewModel->familyPrice = $ticketPrices['family'] ?? 0.00;

        return $viewModel;
    }


}