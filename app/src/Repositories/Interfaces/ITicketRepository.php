<?php

namespace App\Repositories\Interfaces;

use App\Models\TicketScheme;
use App\Models\TicketType;

interface ITicketRepository
{
    public function getTicketTypeById(int $ticketTypeId): ?TicketType;
    public function getTicketTypesByScheduleId(int $scheduleId): array;
    public function getTicketTypesByScheduleIds(array $scheduleIds): array;
    public function create(TicketType $ticketType): bool;
    public function update(TicketType $ticketType): bool;
    public function delete(int $ticketTypeId): bool;

    public function getTicketSchemeById(int $ticketSchemeId): ?TicketScheme;
    public function getAllTicketSchemes(): array;
    public function getTicketSchemeUsageCounts(): array;
    public function countTicketTypesBySchemeId(int $ticketSchemeId): int;
    public function getAvailableCapacity(int $ticketTypeId): int;
    public function atomicIncrementTicketsSold(int $ticketTypeId, int $quantity): bool;
    public function createTicketScheme(TicketScheme $ticketScheme): bool;
    public function updateTicketScheme(TicketScheme $ticketScheme): bool;
    public function deleteTicketScheme(int $ticketSchemeId): bool;
}
