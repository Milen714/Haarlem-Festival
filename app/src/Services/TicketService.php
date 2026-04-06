<?php

namespace App\Services;

use App\Models\TicketType;
use App\Models\TicketScheme;
use App\Models\Schedule;
use App\Models\Enums\TicketLanguageEnum;
use App\Models\Enums\TicketSchemeEnum;
use App\Services\Interfaces\ITicketService;
use App\Repositories\Interfaces\ITicketRepository;
use App\Repositories\TicketRepository;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ApplicationException;

class TicketService implements ITicketService
{
    private ITicketRepository $ticketRepository;

    public function __construct(?ITicketRepository $ticketRepository = null)
    {
        $this->ticketRepository = $ticketRepository ?? new TicketRepository();
    }

    public function getTicketTypeById(int $ticketTypeId): ?TicketType
    {
        return $this->ticketRepository->getTicketTypeById($ticketTypeId);
    }

    public function getTicketTypesByScheduleId(int $scheduleId): array
    {
        return $this->ticketRepository->getTicketTypesByScheduleId($scheduleId);
    }

    public function getTicketTypesByScheduleIds(array $scheduleIds): array
    {
        return $this->ticketRepository->getTicketTypesByScheduleIds($scheduleIds);
    }

    public function getTicketTypesBySchemeEnums(array $schemeEnums): array
    {
        return $this->ticketRepository->getTicketTypesBySchemeEnums($schemeEnums);
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

    public function getTicketSchemeById(int $ticketSchemeId): ?TicketScheme
    {
        return $this->ticketRepository->getTicketSchemeById($ticketSchemeId);
    }

    public function getAllTicketSchemes(): array
    {
        return $this->ticketRepository->getAllTicketSchemes();
    }

    public function getTicketSchemeUsageCounts(): array
    {
        return $this->ticketRepository->getTicketSchemeUsageCounts();
    }

    public function getTicketSchemeUsageCount(int $ticketSchemeId): int
    {
        return $this->ticketRepository->countTicketTypesBySchemeId($ticketSchemeId);
    }

    public function createTicketScheme(TicketScheme $ticketScheme): bool
    {
        return $this->ticketRepository->createTicketScheme($ticketScheme);
    }

    public function updateTicketScheme(TicketScheme $ticketScheme): bool
    {
        return $this->ticketRepository->updateTicketScheme($ticketScheme);
    }

    public function deleteTicketScheme(int $ticketSchemeId): bool
    {
        return $this->ticketRepository->deleteTicketScheme($ticketSchemeId);
    }

    public function getTicketTypeIdsBySchemeId(int $schemeId): array
    {
        return $this->ticketRepository->getTicketTypeIdsBySchemeId($schemeId);
    }

    public function getAvailableCapacity(int $ticketTypeId): int
    {
        return $this->ticketRepository->getAvailableCapacity($ticketTypeId);
    }

    public function reserveSeats(int $ticketTypeId, int $quantity): bool
    {
        return $this->ticketRepository->atomicIncrementTicketsSold($ticketTypeId, $quantity);
    }

    public function reserveMultiple(array $items): bool
    {
        return $this->ticketRepository->reserveMultiple($items);
    }

    /*public function syncHistoryScheduleSoldOut(int $ticketTypeId): void
    {
        $this->ticketRepository->syncHistoryScheduleSoldOut($ticketTypeId);
    }*/


    public function releaseSeats(int $ticketTypeId, int $quantity): bool
    {
        return $this->ticketRepository->atomicDecrementTicketsSold($ticketTypeId, $quantity);
    }

    // Releases all tickets from an order's items in one transaction. Used by the webhook on session expiry.
    // For pass-type tickets, releases from all sibling ticket types sharing the same scheme.
    public function releaseOrderItems(array $orderItems): void
    {
        $items = [];
        foreach ($orderItems as $item) {
            $ticketTypeId = $item->ticket_type?->ticket_type_id ?? null;
            $schemeEnum   = $item->ticket_type?->ticket_scheme?->scheme_enum ?? null;
            $schemeId     = $item->ticket_type?->ticket_scheme?->ticket_scheme_id ?? null;
            $quantity     = (int)($item->quantity ?? 0);

            if ($ticketTypeId === null || $quantity <= 0) {
                continue;
            }

            if ($schemeEnum !== null && TicketSchemeEnum::isPassType($schemeEnum) && $schemeId !== null) {
                $siblingIds = $this->ticketRepository->getTicketTypeIdsBySchemeId($schemeId);
                foreach ($siblingIds as $siblingId) {
                    $items[] = ['ticket_type_id' => (int)$siblingId, 'quantity' => $quantity];
                }
            } else {
                $items[] = ['ticket_type_id' => $ticketTypeId, 'quantity' => $quantity];
            }
        }
        if (!empty($items)) {
            $this->ticketRepository->releaseMultiple($items);
        }
    }

    public function validateCapacityAgainstVenue(int $scheduleId, int $newCapacity, ?int $excludeTicketTypeId = null): void
    {
        $existing     = $this->ticketRepository->getTotalAllocatedCapacityForSchedule($scheduleId, $excludeTicketTypeId);
        $venueCapacity = $this->ticketRepository->getVenueCapacityForSchedule($scheduleId);

        if ($venueCapacity === null) {

            return;
        }

        $total = $existing + $newCapacity;
        if ($total > $venueCapacity) {
            throw new ValidationException(
                "Total ticket capacity ({$total}) would exceed the venue capacity ({$venueCapacity}). " .
                    "You can allocate at most " . ($venueCapacity - $existing) . " more seat(s) for this schedule."
            );
        }
    }

    public function deleteTicketSchemeSafely(int $ticketSchemeId): void
    {
        $usageCount = $this->ticketRepository->countTicketTypesBySchemeId($ticketSchemeId);

        if ($usageCount > 0) {
            throw new ValidationException("This ticket scheme is currently used by {$usageCount} ticket type(s) and cannot be deleted.");
        }

        $success = $this->ticketRepository->deleteTicketScheme($ticketSchemeId);

        if (!$success) {
            throw new ApplicationException('Failed to delete ticket scheme in database');
        }
    }

    public function createTicketSchemeFromRequest(array $postData): TicketScheme
    {
        $this->validateTicketSchemeData($postData);

        $ticketScheme = $this->buildTicketSchemeFromPostData(new TicketScheme(), $postData);
        $success = $this->ticketRepository->createTicketScheme($ticketScheme);

        if (!$success) {
            throw new ApplicationException('Failed to create ticket scheme in database');
        }

        return $ticketScheme;
    }

    public function updateTicketSchemeFromRequest(int $ticketSchemeId, array $postData): TicketScheme
    {
        $ticketScheme = $this->ticketRepository->getTicketSchemeById($ticketSchemeId);

        if (!$ticketScheme) {
            throw new ResourceNotFoundException('Ticket scheme not found.');
        }

        $this->validateTicketSchemeData($postData);

        $ticketScheme = $this->buildTicketSchemeFromPostData($ticketScheme, $postData);
        $success = $this->ticketRepository->updateTicketScheme($ticketScheme);

        if (!$success) {
            throw new ApplicationException('Failed to update ticket scheme in database');
        }

        return $ticketScheme;
    }

    public function createFromRequest(int $scheduleId, array $postData): TicketType
    {
        $this->validateTicketTypeData($postData);
        $this->validateCapacityAgainstVenue($scheduleId, (int)($postData['capacity'] ?? 0));

        $ticketType = $this->buildTicketTypeFromPostData(new TicketType(), $scheduleId, $postData);
        $success = $this->ticketRepository->create($ticketType);

        if (!$success) {
            throw new ApplicationException('Failed to create ticket type in database');
        }

        return $ticketType;
    }

    public function updateFromRequest(int $ticketTypeId, int $scheduleId, array $postData): TicketType
    {
        $ticketType = $this->ticketRepository->getTicketTypeById($ticketTypeId);

        if (!$ticketType) {
            throw new ResourceNotFoundException('Ticket type not found.');
        }

        if (($ticketType->schedule?->schedule_id ?? null) !== $scheduleId) {
            throw new ValidationException('Ticket type does not belong to this schedule.');
        }

        $this->validateTicketTypeData($postData);
        $this->validateCapacityAgainstVenue($scheduleId, (int)($postData['capacity'] ?? 0), $ticketType->ticket_type_id);

        $ticketType = $this->buildTicketTypeFromPostData($ticketType, $scheduleId, $postData);
        $success = $this->ticketRepository->update($ticketType);

        if (!$success) {
            throw new ApplicationException('Failed to update ticket type in database');
        }

        return $ticketType;
    }

    private function validateTicketTypeData(array $postData): void
    {
        $schemeId = (int)($postData['scheme_id'] ?? 0);
        $capacity = (int)($postData['capacity'] ?? 0);
        $minQuantity = (int)($postData['min_quantity'] ?? 0);
        $maxQuantity = (int)($postData['max_quantity'] ?? 0);

        if ($schemeId <= 0) {
            throw new ValidationException('Please select a ticket scheme.');
        }

        if (!$this->ticketRepository->getTicketSchemeById($schemeId)) {
            throw new ValidationException('Selected ticket scheme was not found.');
        }

        if ($capacity <= 0) {
            throw new ValidationException('Capacity must be greater than 0.');
        }

        if ($minQuantity <= 0) {
            throw new ValidationException('Minimum quantity must be at least 1.');
        }

        if ($maxQuantity <= 0) {
            throw new ValidationException('Maximum quantity must be at least 1.');
        }

        if ($minQuantity > $maxQuantity) {
            throw new ValidationException('Minimum quantity cannot be greater than maximum quantity.');
        }

        $minAge = trim((string)($postData['min_age'] ?? ''));
        $maxAge = trim((string)($postData['max_age'] ?? ''));

        if ($minAge !== '' && $maxAge !== '' && (int)$minAge > (int)$maxAge) {
            throw new ValidationException('Minimum age cannot be greater than maximum age.');
        }
    }

    private function buildTicketTypeFromPostData(TicketType $ticketType, int $scheduleId, array $postData): TicketType
    {
        $schedule = new Schedule();
        $schedule->schedule_id = $scheduleId;

        $ticketType->schedule = $schedule;
        $ticketType->ticket_scheme = $this->ticketRepository->getTicketSchemeById((int)$postData['scheme_id']);
        $ticketType->description = $this->normalizeNullableString($postData['description'] ?? null);
        $ticketType->min_age = $this->normalizeNullableInt($postData['min_age'] ?? null);
        $ticketType->max_age = $this->normalizeNullableInt($postData['max_age'] ?? null);
        $ticketType->min_quantity = (int)$postData['min_quantity'];
        $ticketType->max_quantity = (int)$postData['max_quantity'];
        $ticketType->capacity = (int)$postData['capacity'];
        $ticketType->special_requirements = $this->normalizeNullableString($postData['special_requirements'] ?? null);
        $ticketType->tickets_sold = $ticketType->tickets_sold ?? 0;
        $ticketType->is_sold_out  = $ticketType->is_sold_out  ?? false;

        return $ticketType;
    }

    private function validateTicketSchemeData(array $postData): void
    {
        $name = $this->normalizeNullableString($postData['name'] ?? null);
        $schemeEnum = $postData['scheme_enum'] ?? null;
        $priceRaw = $postData['price'] ?? null;
        $ticketLanguage = $postData['ticket_language'] ?? null;

        if ($name === null) {
            throw new ValidationException('Ticket scheme name is required.');
        }

        if (!is_string($schemeEnum) || trim($schemeEnum) === '') {
            throw new ValidationException('Ticket scheme type is required.');
        }

        try {
            TicketSchemeEnum::from(trim($schemeEnum));
        } catch (\ValueError $e) {
            throw new ValidationException('Selected ticket scheme type is invalid.');
        }

        if (!is_numeric($priceRaw) || (float)$priceRaw < 0) {
            throw new ValidationException('Price must be a valid non-negative number.');
        }

        if (is_string($ticketLanguage) && trim($ticketLanguage) !== '') {
            try {
                TicketLanguageEnum::from(trim($ticketLanguage));
            } catch (\ValueError $e) {
                throw new ValidationException('Selected ticket language is invalid.');
            }
        }
    }

    private function buildTicketSchemeFromPostData(TicketScheme $ticketScheme, array $postData): TicketScheme
    {
        $ticketScheme->name = $this->normalizeNullableString($postData['name'] ?? null);
        $ticketScheme->scheme_enum = TicketSchemeEnum::from(trim((string)$postData['scheme_enum']));
        $ticketScheme->price = round((float)$postData['price'], 2);
        $ticketScheme->fee = null;

        $ticketLanguage = $this->normalizeNullableString($postData['ticket_language'] ?? null);
        $ticketScheme->ticket_language = $ticketLanguage !== null ? TicketLanguageEnum::from($ticketLanguage) : null;

        return $ticketScheme;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);
        return $value === '' ? null : $value;
    }

    private function normalizeNullableInt(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }
        }

        return (int)$value;
    }

    /*public function getTicketTypeFromSelection(TicketSelectionDTO $ticketDTO): ?TicketType
    {        
        return $this->ticketRepository->getTicketTypeFromSelection($ticketDTO);
    }*/
}
