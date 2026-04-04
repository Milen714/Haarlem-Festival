<?php

namespace App\Services\Interfaces;

use App\ViewModels\History\TicketHistoryViewModel;


interface IHistoryService
{   
    public function getAvailableTourOptions(): TicketHistoryViewModel;

}