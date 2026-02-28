<?php /** @var string $title */ ?>
<section class="relative w-full overflow-hidden">
    <div class="absolute inset-0">
        <img src="/Assets/History/History_Hero_Tour.png" alt="Tour Hero" class="w-full h-[46vh] min-h-[320px] object-cover object-center"/>
        <div class="absolute inset-0 bg-black/40"></div>
    </div>
    <div class="relative z-10 mx-auto flex h-[46vh] min-h-[320px] max-w-[1100px] items-center px-4">
        <div>
            <h1 class="font-serif text-white text-4xl sm:text-5xl md:text-6xl font-extrabold leading-tight drop-shadow-md">
                <?= htmlspecialchars($title) ?>
            </h1>
        </div>
    </div>
</section>
