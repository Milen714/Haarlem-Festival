<?php
namespace App\Services\Interfaces;

use App\Models\TicketType;

interface ITicketService
{
    public function getTicketTypeById(int $ticketTypeId): ?TicketType;
    public function getTicketTypesByScheduleId(int $scheduleId): array;
    public function create(TicketType $ticketType): bool;
    public function update(TicketType $ticketType): bool;
    public function delete(int $ticketTypeId): bool;
}