<?php
?>

<section aria-labelledby="collab-heading">
    <h2 id="collab-heading"
        class="text-3xl font-bold mb-6"
        style="font-family: 'Cormorant Garamond', serif;">
        Career Highlights &amp; Collaborations  
    </h2>

    <div class="jazz_event_border_<?= $accentColor ?> border-l-4 pl-6">
        <p class="text-gray-700 leading-relaxed text-lg">
            <?= nl2br(htmlspecialchars($artist->collaborations)) ?>
        </p>
    </div>
</section>