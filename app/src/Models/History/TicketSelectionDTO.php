<?php

namespace App\Models\History;

use DateTime;
use App\Models\Enums\TicketLanguageEnum;
use App\Models\Enums\TicketSchemeEnum;

class TicketSelectionDTO  
{
    public ?DateTime $date; 
    public ?DateTime $time;
    public ?TicketLanguageEnum $language;
    public ?int $qtyNormal;
    public ?int $qtyFamily;
    public ?TicketSchemeEnum $ticketSchemeEnum;

    public function __construct(array $data) 
    {
        // Cumplimos con la rúbrica de seguridad (htmlspecialchars)
        $this->date = htmlspecialchars($data['date'] ?? '');
        $this->time = htmlspecialchars($data['time'] ?? '');
        $this->language = htmlspecialchars($data['language'] ?? '');
        
        // Filtramos que sean enteros válidos
        $this->qtyNormal = filter_var($data['qtyNormal'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        $this->qtyFamily = filter_var($data['qtyFamily'] ?? 0, FILTER_VALIDATE_INT) ?: 0;  
        $this->ticketSchemeEnum = isset($data['ticketSchemeEnum']) ? TicketSchemeEnum::from($data['ticketSchemeEnum']) : null;  
    }

    public function hasTickets(): bool 
    {
        return $this->qtyNormal > 0 || $this->qtyFamily > 0;
    }
}