<?php
/** @var App\CmsModels\Page $pageData */
/** @var string $title */
?>

<section class="relative w-full overflow-hidden">
    <div class="absolute inset-0">
        <img src="/Assets/History/History_Hero_Homepage.png" 
             alt="Haarlem History Hero" 
             class="w-full h-[52vh] object-cover object-center"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-transparent"></div>
    </div>

    <div class="relative container mx-auto max-w-[1100px] px-4 flex items-end h-[52vh]">
        <div class="pb-10">
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-white leading-tight drop-shadow-md">
                <?= htmlspecialchars($title ?? $pageData->title) ?>
            </h1>
        </div>
    </div>
</section>