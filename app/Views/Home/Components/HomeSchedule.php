<?php
namespace App\Views\Home\Components;
$bool = false; // This should be replaced with the actual condition to determine if the schedule button is active or not
$scheduleButtonStyle = "home_calendar_button_inactive";
$scheduleEventSelectButtonStyle = "home_event_select_button_inactive";
if ($bool) {
    $scheduleButtonStyle = "home_calendar_button_active";
    $scheduleEventSelectButtonStyle = "home_event_select_button_active";
}
?>


<div class="flex flex-col gap-4 items-center justify-center p-5 w-[90%] mx-auto mb-10">
    <div>
        <header class="headers_home pb-5">
            <h1 class="text-4xl font-bold">Festival at a Glance</h1>
        </header>
        <header class="pb-5 text-center">
            <h1 class="text-4xl font-bold">July 2026</h1>
        </header>
    </div>
    <div class="flex flex-col gap-4">
        <nav>
            <ul class="flex flex-row flex-wrap gap-4 items-center justify-center">
                <li class="mb-2">
                    <a href="#family-fun-day" class="<?php echo $scheduleEventSelectButtonStyle ?>">All Events</a>
                </li>
                <li class="mb-2">
                    <a href="#family-fun-day" class="<?php echo $scheduleEventSelectButtonStyle ?>">Food & Drinks</a>
                </li>
                <li class="mb-2">
                    <a href="#family-fun-day" class="<?php echo $scheduleEventSelectButtonStyle ?>">Jazz</a>
                </li>
                <li class="mb-2">
                    <a href="#family-fun-day" class="<?php echo $scheduleEventSelectButtonStyle ?>">A Stroll Through
                        History</a>
                </li>
                <li class="mb-2">
                    <a href="#family-fun-day" class="<?php echo $scheduleEventSelectButtonStyle ?>">Dance</a>
                </li>
                <li class="mb-2">
                    <a href="#family-fun-day" class="<?php echo $scheduleEventSelectButtonStyle ?>">Magic@Teylers</a>
                </li>
            </ul>
        </nav>
        <nav>
            <ul class="grid gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4
               max-w-5xl mx-auto items-stretch">
                <li>
                    <a href="" class="<?php echo $scheduleButtonStyle ?> w-full h-full">
                        <span>Thursday</span><span>24</span>
                    </a>
                </li>
                <li>
                    <a href="" class="<?php echo $scheduleButtonStyle ?> w-full h-full">
                        <span>Friday</span><span>25</span>
                    </a>
                </li>
                <li>
                    <a href="" class="<?php echo $scheduleButtonStyle ?> w-full h-full">
                        <span>Saturday</span><span>26</span>
                    </a>
                </li>
                <li>
                    <a href="" class="<?php echo $scheduleButtonStyle ?> w-full h-full">
                        <span>Sunday</span><span>27</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="w-full flex flex-col items-center justify-center gap-3">
        <header class="flex flex-row gap-3 items-center justify-center mb-4 w-full">
            <div class="flex flex-col items-center justify-center p-5 bg-[var(--text-home-primary)] 
                dark:bg-[var(--text-home-primary-high-contrast)] rounded-lg flex-shrink-0">
                <h2 class="text-white text-2xl font-bold mb-1">Evening</h2>
                <p class="text-white">17:00 - 21:00</p>
            </div>
            <div class="flex-grow border-b-2 border-[var(--home-gold-accent)]"></div>
        </header>
        <?php
        // Example of including ScheduleRowCard component multiple times
        include 'ScheduleRowCard.php';
        include 'ScheduleRowCard.php';
        include 'ScheduleRowCard.php';
        include 'ScheduleRowCard.php';
        ?>
    </div>

</div>