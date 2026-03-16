<?php
namespace App\Models;

use App\Models\Enums\TicketLanguageEnum;
use App\Models\Enums\TicketSchemeEnum;

class TicketScheme
{
    public ?int $ticket_scheme_id;
    public ?string $name;
    public ?TicketSchemeEnum $scheme_enum;
    public ?float  $price;
    public ?float  $fee;
    public ?TicketLanguageEnum $ticket_language;
    public function __construct(){}

    public function fromPDOData($data): self
    {
        $this->ticket_scheme_id = $data['ticket_scheme_id'] ?? null;
        $this->name = $data['ts_name'] ?? $data['name'] ?? null;
        $this->scheme_enum = isset($data['scheme_enum']) ? TicketSchemeEnum::from($data['scheme_enum']) : null;
        $this->price = $data['price'] ?? null;
        $this->fee = $data['fee'] ?? null;
        $this->ticket_language = isset($data['ticket_language']) ? TicketLanguageEnum::from($data['ticket_language']) : null;
        return $this;
    }
}