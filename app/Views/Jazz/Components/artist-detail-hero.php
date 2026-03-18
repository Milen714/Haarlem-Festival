<?php
/** @var \App\Models\MusicEvent\Artist $artist */
/** @var \App\Models\MusicEvent\JazzArtistDetailViewModel $vm */
?>

<header class="relative w-full bg-gray-900 overflow-hidden" style="min-height: 520px;">

    <?php if ($artist->hasProfileImage()): ?>
        <img src="<?= htmlspecialchars($artist->getProfileImagePath()) ?>"
             alt=""
             aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover opacity-60" />
    <?php endif; ?>

    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent" aria-hidden="true"></div>

    <div class="relative z-10 container mx-auto px-6 py-10 flex flex-col justify-between" style="min-height: 520px;">

        <!-- Breadcrumb -->
        <nav class="text-sm text-white/70 mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center list-none p-0 m-0">
                <li><a href="/" class="hover:text-white transition-colors">Festival</a></li>
                <li class="mx-2" aria-hidden="true">/</li>
                <li><a href="/events-jazz" class="hover:text-white transition-colors">Jazz</a></li>
                <li class="mx-2" aria-hidden="true">/</li>
                <li class="text-white font-semibold" aria-current="page"><?= htmlspecialchars($artist->name ?? '') ?></li>
            </ol>
        </nav>

        <!-- Center: Artist name + genres -->
        <hgroup class="flex-1 flex flex-col items-center justify-center text-center">
            <h1 class="text-6xl font-bold text-white drop-shadow-lg mb-3"
                style="font-family: 'Cormorant Garamond', serif; letter-spacing: 0.05em;">
                <?= htmlspecialchars(strtoupper($artist->name ?? '')) ?>
            </h1>

            <?php if (!empty($artist->genres)): ?>
                <p class="text-sm font-semibold tracking-widest" style="color: rgba(255,255,255,0.8);">
                    <?= htmlspecialchars(implode(' • ', array_map('trim', explode(',', $artist->genres)))) ?>
                </p>
            <?php endif; ?>
        </hgroup>

        <!-- Bottom: Quick Info Panel -->
        <div class="mt-auto self-start">
        <aside class="bg-white/95 rounded-xl p-5 shadow-xl text-sm inline-block min-w-[220px] max-w-[280px]">
                <h2 class="font-bold text-gray-800 mb-3 text-base">Quick Info</h2>
                <?php
                    $dates  = [];
                    $venues = [];
                    foreach ($vm->scheduleByDate as $dateKey => $dateSlots) {
                        $dates[] = (new \DateTime($dateKey))->format('j M');
                        foreach ($dateSlots as $slot) {
                            if (!empty($slot['venue_name'])) {
                                $venues[$slot['venue_name']] = true;
                            }
                        }
                    }
                    $venues = array_keys($venues);
                ?>
                <ul class="list-disc list-inside space-y-1 text-gray-700">

                    <?php if (!empty($vm->scheduleByDate)): ?>
                    <li>Performances: <?= array_sum(array_map('count', $vm->scheduleByDate)) ?>x</li>
                    <?php endif; ?>

                    <?php if (!empty($venues)): ?>
                    <li>Venues: <?= htmlspecialchars(implode(' & ', $venues)) ?></li>
                    <?php endif; ?>

                    <?php if (!empty($dates)): ?>
                    <li>Dates: <?= htmlspecialchars(implode(', ', $dates)) ?></li>
                    <?php endif; ?>

                    <?php if (!empty($artist->ticket_price)): ?>
                    <li>Tickets: <?= htmlspecialchars($artist->ticket_price) ?></li>
                    <?php endif; ?>

                </ul>
            </aside>
        </div>

    </div>
</header>