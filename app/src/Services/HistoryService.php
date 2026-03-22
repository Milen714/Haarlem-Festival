<?php

namespace App\Services;

use App\Repositories\HistoryRepository;
use App\Services\Interfaces\IHistoryService;
use App\ViewModels\History\TicketHistoryViewModel;
use App\Models\Enums\TicketSchemeEnum;

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
            $id   = (int)$row['ticket_type_id']; 
            $schemeType = $row['scheme_enum']; 

            // Si el idioma no existe en el árbol, lo creamos
            if (!isset($ticketOptions[$language])) {
                $ticketOptions[$language] = [];
            }
            // Si la fecha no existe dentro de ese idioma, la creamos
            if (!isset($ticketOptions[$language][$date])) {
                $ticketOptions[$language][$date] = [];
            }
            
            // ¡EL CAMBIO IMPORTANTE ESTÁ AQUÍ!
            // Inicializamos la hora usando su llave explícita [$time]
            if (!isset($ticketOptions[$language][$date][$time])) {
                $ticketOptions[$language][$date][$time] = ['normalId' => null, 'familyId' => null];
            }

            // Asignamos el ID correcto usando validación estricta con tu Enum
            if ($schemeType === TicketSchemeEnum::HISTORY_SINGLE_TICKET->value) {
                $ticketOptions[$language][$date][$time]['normalId'] = $id;
            } elseif ($schemeType === TicketSchemeEnum::HISTORY_FAMILY_TICKET->value) {
                $ticketOptions[$language][$date][$time]['familyId'] = $id;
            }
        }

        $viewModel->options = $ticketOptions;
        $viewModel->normalPrice = $ticketPrices['normal'] ?? 0.00;
        $viewModel->familyPrice = $ticketPrices['family'] ?? 0.00;

        return $viewModel;
    }

}