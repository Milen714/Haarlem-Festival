<?php

namespace App\Services\Interfaces;

use App\Models\TicketType;
use App\Models\TicketScheme;
use App\Models\History\TicketSelectionDTO;

interface ITicketService
{
    public function getTicketTypeById(int $ticketTypeId): ?TicketType;
    public function getTicketTypesByScheduleId(int $scheduleId): array;
    public function getTicketTypeFromSelection(TicketSelectionDTO $ticketDTO): ?TicketType;
    public function create(TicketType $ticketType): bool;
    public function update(TicketType $ticketType): bool;
    public function delete(int $ticketTypeId): bool;
    public function createFromRequest(int $scheduleId, array $postData): TicketType;
    public function updateFromRequest(int $ticketTypeId, int $scheduleId, array $postData): TicketType;

    public function getTicketSchemeById(int $ticketSchemeId): ?TicketScheme;
    public function getAllTicketSchemes(): array;
    public function getTicketSchemeUsageCounts(): array;
    public function getTicketSchemeUsageCount(int $ticketSchemeId): int;
    public function createTicketScheme(TicketScheme $ticketScheme): bool;
    public function updateTicketScheme(TicketScheme $ticketScheme): bool;
    public function deleteTicketScheme(int $ticketSchemeId): bool;
    public function createTicketSchemeFromRequest(array $postData): TicketScheme;
    public function updateTicketSchemeFromRequest(int $ticketSchemeId, array $postData): TicketScheme;
    public function deleteTicketSchemeSafely(int $ticketSchemeId): void;
}
