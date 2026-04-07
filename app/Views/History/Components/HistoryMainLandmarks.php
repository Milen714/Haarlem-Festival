<?php /** @var App\Models\Landmark[] $landmarks */ ?>
<section class="space-y-[3rem] mb-[5rem]">
    <?php foreach ($landmarks as $i => $landmark):
        $imageLeft = $i % 2 === 0;
    ?>
    <article class="grid md:grid-cols-2 gap-8 md:gap-x-16 items-stretch mb-5">
        <div class="md:col-span-1 <?= $imageLeft ? 'order-1' : 'order-2' ?>">
            <img src="<?= htmlspecialchars($landmark->imagePath ?? '/Assets/Home/ImagePlaceholder.png') ?>"
                 class="w-full h-[16rem] md:h-full object-cover rounded-[0.5rem] shadow-md" />
        </div>
        <div class="md:col-span-1 <?= $imageLeft ? 'order-2' : 'order-1' ?> p-[2rem] flex flex-col justify-center <?= !$imageLeft ? 'text-right md:text-left' : '' ?>">
            <h4 class="text-[1.5rem] font-bold text-[var(--history-dark-brown)]">
                <?= htmlspecialchars($landmark->name) ?>
            </h4>
            <div class="mt-[1rem] leading-relaxed text-ink-700">
                <?= htmlspecialchars($landmark->short_description ?? '') ?>
            </div>
            <a href="/history/detail/<?= htmlspecialchars($landmark->landmark_slug) ?>"
               class="mt-[1.25rem] inline-block font-bold italic text-[1.125rem] text-[var(--history-accent-color)] hover:text-brand-700 transition-colors">
                <?= htmlspecialchars($landmark->home_cta ?? 'Learn more') ?>
            </a>
        </div>
    </article>
    <?php endforeach; ?>
</section>
