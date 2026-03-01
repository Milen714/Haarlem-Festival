<?php
/** @var \App\Models\MusicEvent\Artist $artist */
/** @var \App\Models\MusicEvent\JazzArtistDetailViewModel $vm */
?>

<header class="relative w-full bg-gray-900 overflow-hidden" style="min-height: 520px;">

    <?php if ($artist->hasProfileImage()): ?>
        <img src="<?= htmlspecialchars($artist->getProfileImagePath()) ?>"
             alt="<?= htmlspecialchars($artist->getProfileImageAlt()) ?>"
             class="absolute inset-0 w-full h-full object-cover opacity-60" />
    <?php endif; ?>

    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

    <div class="relative z-10 container mx-auto px-6 py-10 flex flex-col justify-between" style="min-height: 520px;">

        <!-- Breadcrumb -->
        <nav class="text-sm text-white/70 mb-6" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white transition-colors">Festival</a>
            <span class="mx-2">/</span>
            <a href="/events-jazz" class="hover:text-white transition-colors">Jazz</a>
            <span class="mx-2">/</span>
            <span class="text-white font-semibold"><?= htmlspecialchars($artist->name ?? '') ?></span>
        </nav>

        <!-- Center: Artist name + genres -->
        <div class="flex-1 flex flex-col items-center justify-center text-center">
            <h1 class="text-6xl font-bold text-white drop-shadow-lg mb-3"
                style="font-family: 'Cormorant Garamond', serif; letter-spacing: 0.05em;">
                <?= htmlspecialchars(strtoupper($artist->name ?? '')) ?>
            </h1>

            <?php if (!empty($artist->genres)): ?>
                <p class="text-sm font-semibold tracking-widest" style="color: rgba(255,255,255,0.8);">
                    <?= htmlspecialchars(implode(' • ', array_map('trim', explode(',', $artist->genres)))) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Bottom: Quick Info Panel -->
        <div class="mt-auto">
            <aside class="bg-white/95 rounded-xl p-5 shadow-xl text-sm inline-block min-w-[220px]">
                <h2 class="font-bold text-gray-800 mb-3 text-base">Quick Info</h2>
                <ul class="space-y-2 text-gray-700">

                    <?php if (!empty($vm->scheduleByDate)): ?>
                    <li>
                        <strong>Performances:</strong>
                        <?= array_sum(array_map('count', $vm->scheduleByDate)) ?>x
                    </li>
                    <?php endif; ?>

                    <?php
                        $venues = [];
                        foreach ($vm->scheduleByDate as $dateSlots) {
                            foreach ($dateSlots as $slot) {
                                if (!empty($slot->venue_name)) {
                                    $venues[$slot->venue_name] = true;
                                }
                            }
                        }
                        $venues = array_keys($venues);
                    ?>
                    <?php if (!empty($venues)): ?>
                    <li>
                        <strong>Venues:</strong>
                        <?= htmlspecialchars(implode(' & ', $venues)) ?>
                    </li>
                    <?php endif; ?>

                    <?php if (!empty($artist->website)): ?>
                    <li>
                        <strong>Website:</strong><br>
                        <a href="<?= htmlspecialchars($artist->website) ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="text-blue-600 hover:underline break-all">
                            <?= htmlspecialchars(parse_url($artist->website, PHP_URL_HOST) ?? $artist->website) ?>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (!empty($artist->ticket_price)): ?>
                    <li>
                        <strong>Tickets:</strong>
                        <?= htmlspecialchars($artist->ticket_price) ?>
                    </li>
                    <?php endif; ?>

                </ul>
            </aside>
        </div>

    </div>
</header>