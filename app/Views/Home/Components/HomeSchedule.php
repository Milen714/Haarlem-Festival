<?php
namespace App\Views\Home\Components;
$bool = false; // This should be replaced with the actual condition to determine if the schedule button is active or not
$scheduleButtonStyle = "home_calendar_button_inactive";
if ($bool) {
    $scheduleButtonStyle = "home_calendar_button_active";
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
    <nav>
        <ul class="flex flex-row flex-wrap gap-4 items-center justify-center">
            <li class="mb-2">
                <a href="#family-fun-day" class="home_dance_button">All Events</a>
            </li>
            <li class="mb-2">
                <a href="#family-fun-day" class="home_dance_button">Food & Drinks</a>
            </li>
            <li class="mb-2">
                <a href="#family-fun-day" class="home_dance_button">Jazz</a>
            </li>
            <li class="mb-2">
                <a href="#family-fun-day" class="home_dance_button">A Stroll Through History</a>
            </li>
            <li class="mb-2">
                <a href="#family-fun-day" class="home_dance_button">Dance</a>
            </li>
            <li class="mb-2">
                <a href="#family-fun-day" class="home_dance_button">Magic@Teylers</a>
            </li>
        </ul>
    </nav>
    <nav>
        <ul class="flex flex-row flex-wrap gap-4 items-center justify-center">
            <li>
                <a href="" class="<?php echo $scheduleButtonStyle ?>"><span>Thursday</span><span>24</span>
                </a>
            </li>
            <li>
                <a href="" class="<?php echo $scheduleButtonStyle ?>"><span>Friday</span><span>25</span>
                </a>
            </li>
        </ul>
    </nav>

</div>