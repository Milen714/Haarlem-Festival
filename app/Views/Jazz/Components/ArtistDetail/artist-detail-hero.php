<?php

namespace App\Views\Jazz\Components\ArtistDetail;

$performanceDates = [];
$performanceVenueNames = [];

try {
    foreach ($vm->scheduleByDate as $dateKey => $dateSlots) {
        $performanceDates[] = (new \DateTime($dateKey))->format('j M');
        foreach ($dateSlots as $slot) {
            if (!empty($slot['venue_name'])) {
                $performanceVenueNames[$slot['venue_name']] = true;
            }
        }
    }
} catch (\Throwable $e) {
    $performanceDates      = [];
    $performanceVenueNames = [];
}

$performanceVenueNames = array_keys($performanceVenueNames);

$genreDisplay = '';
if (!empty($artist->genres)) {
    $genreDisplay = implode(' • ', array_map('trim', explode(',', $artist->genres)));
}
?>

<header class="relative w-full bg-gray-900 overflow-hidden min-h-[350px] md:min-h-[520px]">

    <!-- Background artist photo (decorative, aria-hidden) -->
    <?php if ($artist->hasProfileImage()): ?>
        <img src="<?= htmlspecialchars($artist->getProfileImagePath()) ?>" alt="" aria-hidden="true"
            class="absolute inset-0 w-full h-full object-cover opacity-60" />
    <?php endif; ?>

    <!-- Gradient overlay for readability -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent" aria-hidden="true"></div>

    <div class="relative z-10 container mx-auto px-4 md:px-6 py-6 md:py-10 flex flex-col justify-between min-h-[350px] md:min-h-[520px]">

        <!-- Breadcrumb navigation -->
        <nav class="text-sm text-white/70 mb-4 md:mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center list-none p-0 m-0 flex-wrap gap-y-1">
                <li><a href="/" class="hover:text-white transition-colors">Festival</a></li>
                <li class="mx-2" aria-hidden="true">/</li>
                <li><a href="/events-jazz" class="hover:text-white transition-colors">Jazz</a></li>
                <li class="mx-2" aria-hidden="true">/</li>
                <li class="text-white font-semibold" aria-current="page">
                    <?= htmlspecialchars($artist->name ?? '') ?>
                </li>
            </ol>
        </nav>

        <!-- Artist name and genre tags -->
        <hgroup class="flex-1 flex flex-col items-center justify-center text-center py-4">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white drop-shadow-lg mb-3"
                style="font-family: 'Cormorant Garamond', serif; letter-spacing: 0.05em;">
                <?= htmlspecialchars(strtoupper($artist->name ?? '')) ?>
            </h1>
            <?php if ($genreDisplay !== ''): ?>
                <p class="text-xs md:text-sm font-semibold tracking-widest" style="color: rgba(255,255,255,0.8);">
                    <?= htmlspecialchars($genreDisplay) ?>
                </p>
            <?php endif; ?>
        </hgroup>

        <!-- Quick Info panel -->
        <div class="mt-4 md:mt-auto md:self-start w-full md:w-auto">
            <aside class="bg-white/95 rounded-xl p-4 md:p-5 shadow-xl text-sm w-full md:inline-block md:min-w-[220px] md:max-w-[280px]">
                <h2 class="font-bold text-gray-800 mb-3 text-base">Quick Info</h2>
                <ul class="list-disc list-inside space-y-1 text-gray-700">

                    <?php if (!empty($vm->scheduleByDate)): ?>
                        <li>Performances: <?= array_sum(array_map('count', $vm->scheduleByDate)) ?>×</li>
                    <?php endif; ?>

                    <?php if (!empty($performanceVenueNames)): ?>
                        <li>Venues: <?= htmlspecialchars(implode(' & ', $performanceVenueNames)) ?></li>
                    <?php endif; ?>

                    <?php if (!empty($performanceDates)): ?>
                        <li>Dates: <?= htmlspecialchars(implode(', ', $performanceDates)) ?></li>
                    <?php endif; ?>

                    <?php if (!empty($artist->ticket_price)): ?>
                        <li>Tickets: <?= htmlspecialchars($artist->ticket_price) ?></li>
                    <?php endif; ?>

                </ul>
            </aside>
        </div>

    </div>
</header>
