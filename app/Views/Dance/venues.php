<?php
namespace App\Views\Venues;
/** @var \App\ViewModels\Venues\VenueViewModel $vm */
$items = $vm->breadcrumbs;
$headerSection = $vm->pageData->content_sections[0] ?? null;
?>
<section class="dance-bg min-h-screen pt-20 text-white">
    <div class="max-w-7xl mx-auto px-6">
        <?php include __DIR__ . '/Components/breadcrumb.php'; ['items' => $items]; ?>

        <header class="mb-12 mt-8">
            <h3 class="text-4xl font-bold uppercase tracking-tighter leading-none mb-4">
                <?= htmlspecialchars($headerSection?->title ?? '') ?>
            </h3>
            
            <p class="text-white text-lg max-w-7xl mb-16"> 
                <?= $headerSection?->content_html ?? '' ?>
            </p>
        </header>

        <div class="space-y-6 pb-32">
            <?php foreach ($vm->venues as $venue): ?>
                <div class="flex flex-col md:flex-row bg-[#1A1D29] rounded-sm overflow-hidden shadow-xl border border-gray-800/50">
                    <div class="md:w-1/3 aspect-video md:aspect-auto md:h-64 overflow-hidden bg-gray-900">
                        <img src="<?= htmlspecialchars($venue->getImagePath()) ?>" 
                            alt="<?= htmlspecialchars($venue->getImageAlt()) ?>" 
                            class="w-full h-full object-cover object-center hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-8 flex flex-col justify-between flex-grow">
                            <div>
                                <h2 class="text-2xl font-bold mb-3"><?= htmlspecialchars($venue->name) ?></h2>
                                
                            <p class="text-gray-400 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[var(--dance-tag-color-1)]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <?= htmlspecialchars($venue->street_address) ?>, 
                                <?= htmlspecialchars($venue->postal_code) ?> 
                                <?= htmlspecialchars($venue->city) ?>
                            </p>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <a href="/venues/detail?id=<?= $venue->venue_id ?>" 
                            class="bg-[#F3E5AB] text-black px-6 py-2 rounded-md font-bold text-xs uppercase hover:bg-white transition duration-300">
                                More info
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>