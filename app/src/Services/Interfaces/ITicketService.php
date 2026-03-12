<?php
namespace App\Services\Interfaces;

use App\Models\TicketType;
use App\Models\TicketScheme;

interface ITicketService
{
    public function getTicketTypeById(int $ticketTypeId): ?TicketType;
    public function getTicketTypesByScheduleId(int $scheduleId): array;
    public function create(TicketType $ticketType): bool;
    public function update(TicketType $ticketType): bool;
    public function delete(int $ticketTypeId): bool;

    public function getTicketSchemeById(int $ticketSchemeId): ?TicketScheme;
    public function getAllTicketSchemes(): array;
    public function createTicketScheme(TicketScheme $ticketScheme): bool;
    public function updateTicketScheme(TicketScheme $ticketScheme): bool;
    public function deleteTicketScheme(int $ticketSchemeId): bool;
}