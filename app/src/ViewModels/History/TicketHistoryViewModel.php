<?php

namespace App\ViewModels\History;


class TicketHistoryViewModel {  
    public array $availableDates = []; 
    public array $availableTimes = [];
    public array $availableLanguages = [];
    public float $normalPrice;
    public float $familyPrice;
}