<?php
namespace App\Views\Home\Components;
use App\Models\Enums\EventType;
use App\ViewModels\Home\ScheduleList;

/** @var ScheduleList $scheduleList */

$eventFilter = $_GET['event'] ?? null;
$dateFilter = $_GET['date'] ?? null;

// Helper function to determine if a button is active
$isEventActive = fn($eventType) => $eventFilter === $eventType || ($eventFilter === '' && $eventType === '');
$isDateActive = fn($date) => $dateFilter === $date;
?>


<div class="flex flex-col gap-4 items-center justify-center p-5 w-[90%] mx-auto mb-10">
    <section>
        <?php if (isset($scheduleSection->content_html)) {
            echo $scheduleSection->content_html;
        }?>

    </section>
    <!-- <div id="schedule-filter-container" class="flex flex-col gap-4">
        <nav>
            <ul class="flex flex-row flex-wrap gap-4 items-center justify-center schedule-filter-event">
                <li class="mb-2">
                    <a href="#" data-event="" data-date="<?php echo $dateFilter; ?>"
                        class="schedule-filter-link <?php echo $isEventActive('') ? 'home_event_select_button_active' : 'home_event_select_button_inactive'; ?>">All
                        Events</a>
                </li>
                <li class="mb-2">
                    <a href="#" data-event="<?= EventType::Yummy->value ?>" data-date="<?php echo $dateFilter; ?>"
                        class="schedule-filter-link <?php echo $isEventActive(EventType::Yummy->value) ? 'home_event_select_button_active' : 'home_event_select_button_inactive'; ?>">Food
                        & Drinks</a>
                </li>
                <li class="mb-2">
                    <a href="#" data-event="<?= EventType::Jazz->value ?>" data-date="<?php echo $dateFilter; ?>"
                        class="schedule-filter-link <?php echo $isEventActive(EventType::Jazz->value) ? 'home_event_select_button_active' : 'home_event_select_button_inactive'; ?>">Jazz</a>
                </li>
                <li class="mb-2">
                    <a href="#" data-event="<?= EventType::History->value ?>" data-date="<?php echo $dateFilter; ?>"
                        class="schedule-filter-link <?php echo $isEventActive(EventType::History->value) ? 'home_event_select_button_active' : 'home_event_select_button_inactive'; ?>">A
                        Stroll Through
                        History</a>
                </li>
                <li class="mb-2">
                    <a href="#" data-event="<?= EventType::Dance->value ?>" data-date="<?php echo $dateFilter; ?>"
                        class="schedule-filter-link <?php echo $isEventActive(EventType::Dance->value) ? 'home_event_select_button_active' : 'home_event_select_button_inactive'; ?>">Dance</a>
                </li>
                <li class="mb-2">
                    <a href="#" data-event="<?= EventType::Magic->value ?>" data-date="<?php echo $dateFilter; ?>"
                        class="schedule-filter-link <?php echo $isEventActive(EventType::Magic->value) ? 'home_event_select_button_active' : 'home_event_select_button_inactive'; ?>">Magic@Teylers</a>
                </li>
            </ul>
        </nav>
        <nav>
            <ul class="grid gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4
               max-w-5xl mx-auto items-stretch schedule-filter-date">
                <li>
                    <a href="#" data-event="<?php echo $eventFilter; ?>" data-date="2026-07-24"
                        class="schedule-filter-link <?php echo $isDateActive('2026-07-24') ? 'home_calendar_button_active' : 'home_calendar_button_inactive'; ?> w-full h-full">
                        <span>Thursday</span><span>24</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-event="<?php echo $eventFilter; ?>" data-date="2026-07-25"
                        class="schedule-filter-link <?php echo $isDateActive('2026-07-25') ? 'home_calendar_button_active' : 'home_calendar_button_inactive'; ?> w-full h-full">
                        <span>Friday</span><span>25</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-event="<?php echo $eventFilter; ?>" data-date="2026-07-26"
                        class="schedule-filter-link <?php echo $isDateActive('2026-07-26') ? 'home_calendar_button_active' : 'home_calendar_button_inactive'; ?> w-full h-full">
                        <span>Saturday</span><span>26</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-event="<?php echo $eventFilter; ?>" data-date="2026-07-27"
                        class="schedule-filter-link <?php echo $isDateActive('2026-07-27') ? 'home_calendar_button_active' : 'home_calendar_button_inactive'; ?> w-full h-full">
                        <span>Sunday</span><span>27</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div> -->

    <div id="schedule_container" class="w-full">
        <?php include 'ScheduleList.php'; ?>
    </div>
</div>