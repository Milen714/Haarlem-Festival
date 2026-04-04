<?php

namespace App\Repositories\Interfaces;

interface IHistoryRepository
{   
    public function getAvailableTourOptions(): array;

    public function getTourTicketPrices(): array;
}