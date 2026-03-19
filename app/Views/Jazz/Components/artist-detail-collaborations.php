<?php
    $lines = array_filter(array_map('trim', explode("\n", $artist->collaborations)));
?>

<section aria-labelledby="collab-heading">
    <h2 id="collab-heading"
        class="text-3xl font-bold mb-6"
        style="font-family: 'Cormorant Garamond', serif;">
        Career Highlights &amp; Collaborations
    </h2>

    <div class="jazz_event_bg_<?= $accentColor ?> rounded-2xl p-6 shadow-sm">
        <?php if (count($lines) > 1): ?>
        <ul class="space-y-3">
            <?php foreach ($lines as $line): ?>
            <li class="flex items-start gap-3 text-gray-800">
                <span class="mt-1.5 w-2.5 h-2.5 rounded-full bg-gray-800 shrink-0"></span>
                <span class="leading-relaxed"><?= htmlspecialchars($line) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="text-gray-800 leading-relaxed text-lg">
            <?= nl2br(htmlspecialchars($artist->collaborations)) ?>
        </p>
        <?php endif; ?>
    </div>
</section>