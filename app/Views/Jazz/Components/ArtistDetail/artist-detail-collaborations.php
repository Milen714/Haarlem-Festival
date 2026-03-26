<?php

namespace App\Views\Jazz\Components\ArtistDetail;

/* Split multi-line text into individual bullet points */

$collaborationLines = array_filter(
    array_map('trim', explode("\n", (string) ($artist->collaborations ?? '')))
);
$hasMultipleLines = count($collaborationLines) > 1;
?>

<section aria-labelledby="collaborations-heading">
    <h2 id="collaborations-heading" class="text-3xl font-bold mb-6" style="font-family: 'Cormorant Garamond', serif;">
        Career Highlights &amp; Collaborations
    </h2>

    <div class="jazz_event_bg_<?= $accentColor ?> rounded-2xl p-6 shadow-sm">
        <?php if ($hasMultipleLines): ?>
            <ul class="space-y-3">
                <?php foreach ($collaborationLines as $line): ?>
                    <li class="flex items-start gap-3 text-gray-800">
                        <span class="mt-1.5 w-2.5 h-2.5 rounded-full bg-gray-800 shrink-0" aria-hidden="true"></span>
                        <span class="leading-relaxed"><?= htmlspecialchars($line) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-800 leading-relaxed text-lg">
                <?= nl2br(htmlspecialchars((string) ($artist->collaborations ?? ''))) ?>
            </p>
        <?php endif; ?>
    </div>
</section>
