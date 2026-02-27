<?php
namespace App\Views\Home\Components;
use App\Models\History\Landmark;
use App\Models\Venue;
use App\ViewModels\Home\StartingPoints;
/** @var StartingPoints $startingPoints */


?>
<section class="flex flex-row items-start justify-center p-5 w-[90%] mx-auto mb-10" aria-labelledby="map-heading">

    <article class="flex flex-col gap-2 justify-start">
        <header class="headers_home pb-5 text-start">
            <h1 id="map-heading" class="text-4xl font-bold ">Find Your Way Around</h1>
        </header>
        <iframe class="rounded-xl"
            src="https://www.google.com/maps/d/u/0/embed?mid=1ezQjCFX3T3Jib7ooF9p-_d3gStB5oE4&ehbc=2E312F&noprof=1"
            width="640" height="480"></iframe>
    </article>
    <article class="flex flex-col gap-2 items-center ml-5">
        <header class="headers_home pb-5">
            <h1 class="text-4xl font-bold">Starting points</h1>
        </header>
        <ul class="flex flex-col items-center gap-2">
            <?php foreach ($startingPoints->startingPoints as $point): ?>
            <?php include __DIR__ . '/StartingPoint.php'; ?>
            <?php endforeach; ?>
        </ul>
    </article>
</section>