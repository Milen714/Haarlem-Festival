<?php

namespace App\ViewModels\ShoppingCart;

use App\Models\Media;
use App\Models\Schedule;
use App\Models\Payment\OrderItem;
use App\Models\Enums\EventType;

class OrderItemViewModel
{
    private OrderItem $item;
    private Schedule  $schedule;

    public function __construct(OrderItem $item)
    {
        $this->item     = $item;
        $this->schedule = $item->ticket_type->schedule ?? new Schedule();
    }

    public function isPass(): bool
    {
        return $this->getVenueDisplay() === 'All Venues & Stages';
    }

    public function getDisplayName(): string
    {
        if ($this->isPass()) {
            return $this->item->ticket_type->ticket_scheme->name ?? '';
        }
        return $this->item->ticket_type->schedule->artist->name ?? '';
    }

    public function getCardImage(): Media
    {
        if ($this->isPass()) {
            $img            = new Media();
            $img->file_path = '/Assets/Home/ImagePlaceholder.png';
            $img->alt_text  = $this->getDisplayName();
            return $img;
        }

        $eventType = $this->schedule->event_category?->type ?? null;

        switch ($eventType) {
            case EventType::Magic:
                return $this->item->ticket_type->schedule->venue->venue_image ?? new Media();
            case EventType::History:
                return $this->item->ticket_type->schedule->landmark->main_image_id ?? new Media();
            case EventType::Yummy:
                return $this->item->ticket_type->schedule->restaurant->main_image ?? new Media();
            case EventType::Jazz:
            case EventType::Dance:
            default:
                return $this->item->ticket_type->schedule->artist->profile_image ?? new Media();
        }
    }

    public function getCardStyles(): array
    {
        $eventType = $this->schedule->event_category?->type ?? null;

        switch ($eventType) {
            case EventType::Magic:
                return [
                    'side'  => 'bg-[var(--home-magic-accent)] dark:bg-[var(--home-magic-accent-muted)]',
                    'muted' => 'bg-[var(--home-magic-accent-muted)] dark:bg-[var(--home-magic-accent-muted-high-contrast)]',
                ];
            case EventType::History:
                return [
                    'side'  => 'bg-[var(--home-history-accent)] dark:bg-[var(--home-history-accent-muted)]',
                    'muted' => 'bg-[var(--home-history-accent-muted)] dark:bg-[var(--home-history-accent-muted-high-contrast)]',
                ];
            case EventType::Yummy:
                return [
                    'side'  => 'bg-[var(--home-yummy-accent)] dark:bg-[var(--home-yummy-accent-muted)]',
                    'muted' => 'bg-[var(--home-yummy-accent-muted)] dark:bg-[var(--home-yummy-accent-muted-high-contrast)]',
                ];
            case EventType::Dance:
                return [
                    'side'  => 'bg-[var(--home-dance-accent)] dark:bg-[var(--home-dance-accent-muted)]',
                    'muted' => 'bg-[var(--home-dance-accent-muted)] dark:bg-[var(--home-dance-accent-muted-high-contrast)]',
                ];
            case EventType::Jazz:
            default:
                return [
                    'side'  => 'bg-[var(--home-jazz-accent)] dark:bg-[var(--home-jazz-accent-muted)]',
                    'muted' => 'bg-[var(--home-jazz-accent-muted)] dark:bg-[var(--home-jazz-accent-muted-high-contrast)]',
                ];
        }
    }

    public function getEventLabel(): string
    {
        $eventType = $this->schedule->event_category?->type ?? null;

        switch ($eventType) {
            case EventType::Magic:
                return $this->schedule->event_category?->title ?? '';
            case EventType::History:
                return 'History';
            case EventType::Yummy:
                return 'Yummy';
            case EventType::Jazz:
                return 'Jazz';
            case EventType::Dance:
                return 'Dance';
            default:
                return '';
        }
    }

    public function getVenueDisplay(): string
    {
        $schemeEnum = $this->item->ticket_type->ticket_scheme->scheme_enum ?? null;
        $label      = $schemeEnum?->getVenueLabel() ?? '';

        if ($label !== '') {
            return $label;
        }

        if ($this->schedule->venue !== null) {
            return $this->schedule->venue->name ?? '';
        }

        return $this->schedule->landmark?->name ?? '';
    }

    public function getDurationDisplay(): string
    {
        $schemeEnum  = $this->item->ticket_type->ticket_scheme->scheme_enum ?? null;
        $schemePrice = (float)($this->item->ticket_type->ticket_scheme->price ?? 0);
        $label       = $schemeEnum?->getDurationLabel($schemePrice) ?? '';

        if ($label !== '') {
            return $label;
        }

        if ($this->schedule->start_time && $this->schedule->end_time) {
            $timeRange = $this->schedule->start_time->format('H:i')
                . ' - '
                . $this->schedule->end_time->format('H:i');
            $minutes = $this->schedule->getDurationInMinutes();
            return $timeRange . ' (' . $minutes . ' min)';
        }

        $minutes = $this->schedule->getDurationInMinutes();
        return $minutes !== null ? 'Duration: ' . $minutes . ' Minutes' : '';
    }

    public function getDateBoxLabel(): string
    {
        $schemeEnum  = $this->item->ticket_type->ticket_scheme->scheme_enum ?? null;
        $schemePrice = (float)($this->item->ticket_type->ticket_scheme->price ?? 0);
        $duration    = $schemeEnum?->getDurationLabel($schemePrice) ?? '';

        if ($this->isPass() && $duration !== '') {
            return $duration;
        }

        return $this->schedule->date?->format('d M') ?? '';
    }
}