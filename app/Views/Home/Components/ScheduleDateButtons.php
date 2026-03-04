<?php
namespace App\Views\Home\Components;
use App\Models\Enums\EventType;
use App\ViewModels\Home\ScheduleList;
?>
<input id="event-filter" type="hidden" value="<?php echo htmlspecialchars($eventFilter ?? ''); ?>">
<nav>
    <ul id="dates-ul" class="grid gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4
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