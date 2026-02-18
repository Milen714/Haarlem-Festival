<?php
namespace App\Views\Jazz\Components;

$colors = ['lavender', 'pink', 'yellow', 'coral'];
$colorIndex = 0;
?>

<section class="py-16 bg-white" aria-labelledby="schedule-heading">
    <article class="container mx-auto px-4">
        <h2 id="schedule-heading" class="text-4xl font-bold mb-12" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($scheduleSection->title ?? 'Festival at a Glance') ?>
        </h2>
        
        <?php if (!empty($scheduleByDate)): ?>
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($scheduleByDate as $dateKey => $daySchedule): 
                $color = $colors[$colorIndex % 4];
                $colorIndex++;
                $textColor = ($color === 'yellow') ? 'text-gray-800' : 'text-white';
            ?>
            <li class="jazz_event_border_<?= $color ?> rounded-lg overflow-hidden bg-white">
                <article>
                    <header class="jazz_event_bg_<?= $color ?> <?= $textColor ?> p-6 text-center">
                        <time datetime="<?= htmlspecialchars($dateKey) ?>" class="block">
                            <span class="text-5xl font-bold block mb-2"><?= htmlspecialchars($daySchedule['day_number']) ?></span>
                            <span class="text-lg block"><?= htmlspecialchars($daySchedule['day_name']) ?></span>
                            <?php if (!empty($daySchedule['label'])): ?>
                                <mark class="text-sm bg-white text-[var(--pastel-<?= $color ?>)] px-2 py-1 rounded inline-block mt-1">
                                    <?= htmlspecialchars($daySchedule['label']) ?>
                                </mark>
                            <?php endif; ?>
                        </time>
                    </header>
                    
                    <section class="p-6">
                        <h3 class="font-bold mb-3"><?= htmlspecialchars($daySchedule['venue']) ?></h3>
                        <ul class="space-y-2 text-sm mb-6" role="list">
                            <?php foreach ($daySchedule['performances'] as $perf): ?>
                            <li>
                                • <time datetime="<?= htmlspecialchars($perf['start_time']) ?>">
                                    <?= date('H:i', strtotime($perf['start_time'])) ?>
                                </time> - <?= htmlspecialchars($perf['artist_name']) ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <footer class="mb-4">
                            <p class="font-semibold mb-1">
                                <?= $daySchedule['total_performances'] ?> Performance<?= $daySchedule['total_performances'] > 1 ? 's' : '' ?>
                            </p>
                        </footer>
                        
                        <p class="text-sm text-gray-600 mb-4">
                            <?= $daySchedule['is_free'] ? 'FREE Event - Open Air' : 'From €10 • Day Pass €35' ?>
                        </p>
                        
                        <a href="/schedule?date=<?= htmlspecialchars($dateKey) ?>" 
                           class="block w-full jazz_event_bg_<?= $color ?> <?= $textColor ?> text-center py-3 rounded-lg font-semibold hover:opacity-90 transition-colors">
                            See Schedule →
                        </a>
                    </section>
                </article>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p class="text-center text-gray-500 py-12">Schedule coming soon!</p>
        <?php endif; ?>
    </article>
</section>
