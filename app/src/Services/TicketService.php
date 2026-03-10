<?php
namespace App\Services;

use App\Models\TicketType;
use App\Services\Interfaces\ITicketService;
use App\Repositories\Interfaces\ITicketRepository;
use App\Repositories\TicketRepository;

class TicketService implements ITicketService
{
    private ITicketRepository $ticketRepository;

    public function __construct()
    {
        $this->ticketRepository = new TicketRepository();
    }

    public function getTicketTypeById(int $ticketTypeId): ?TicketType
    {
        return $this->ticketRepository->getTicketTypeById($ticketTypeId);
    }

    public function getTicketTypesByScheduleId(int $scheduleId): array
    {
        return $this->ticketRepository->getTicketTypesByScheduleId($scheduleId);
    }

    public function create(TicketType $ticketType): bool
    {
        return $this->ticketRepository->create($ticketType);
    }

    public function update(TicketType $ticketType): bool
    {
        return $this->ticketRepository->update($ticketType);
    }

    public function delete(int $ticketTypeId): bool
    {
        return $this->ticketRepository->delete($ticketTypeId);
    }
}