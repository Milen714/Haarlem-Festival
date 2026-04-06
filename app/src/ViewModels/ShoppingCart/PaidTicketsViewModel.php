<?php
namespace App\ViewModels\ShoppingCart;

use App\Models\Payment\OrderItem;

class PaidTicketsViewModel
{
    /**
     * @var OrderItem[] Array of order items sorted by event start time
     */
    public array $orderItems = [];

    /**
     * Summary of selectedDate
     * @var string|null Optional date filter in 'Y-m-d' format, used to filter tickets by event date
     */
    public ?string $selectedDate = null;

    /**
     * Summary of showMyTicketsSection
     * @var bool
     */
    public bool $showMyTicketsSection = false;

    /**
     * Summary of totalTickets
     * @var int Total number of tickets across all order items, calculated for display purposes
     */
    public int $totalTickets = 0;

    /**
     * @param OrderItem[] $orderItems Array of OrderItem objects
     */
    public function __construct(array $orderItems, ?string $selectedDate = null, bool $showMyTicketsSection = false)
    {
        // Sort items by event start time
        $this->sortOrderItemsByStartTime($orderItems);
        $this->orderItems = $orderItems;
        $this->selectedDate = $selectedDate;
        $this->showMyTicketsSection = $showMyTicketsSection;
        $this->totalTickets = count($this->orderItems)  > 0 ? array_sum(array_map(fn($item) => $item->quantity, $this->orderItems)) : 0;
    }

    /**
     * Sort order items by their ticket type's schedule start time
     */
    private function sortOrderItemsByStartTime(array &$orderItems): void
    {
        usort($orderItems, function ($a, $b) {
            $timeA = $a->ticket_type?->schedule?->start_time;
            $timeB = $b->ticket_type?->schedule?->start_time;
            
            // Convert DateTime objects to string if needed
            if ($timeA instanceof \DateTime) {
                $timeA = $timeA->format('H:i:s');
            }
            if ($timeB instanceof \DateTime) {
                $timeB = $timeB->format('H:i:s');
            }
            
            // Default to midnight if null
            $timeA = $timeA ?? '00:00:00';
            $timeB = $timeB ?? '00:00:00';
            
            return strcmp($timeA, $timeB);
        });
    }
}