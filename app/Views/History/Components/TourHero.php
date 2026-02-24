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

 <!-- <nav aria-label="Breadcrumb" class="flex items-center gap-2 py-6 text-sm text-ink-500">
    <a href="/history" class="inline-flex items-center gap-2 text-ink-900 hover:text-brand-600 transition-colors">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/></svg>
        Back
    </a>
    <span class="mx-2 text-neutral-400">|</span>
    <a href="/" class="hover:text-brand-600 transition-colors">Home</a>
    <span class="mx-2 text-neutral-400">/</span>
    <span class="text-ink-700 font-medium">Haarlem History Tour</span>
</nav>-->