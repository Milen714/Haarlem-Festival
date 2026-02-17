<?php
namespace App\Views\Jazz\Components;

?>

<section class="py-16 bg-gray-50" aria-labelledby="venues-heading">
    <div class="container mx-auto px-4 max-w-[1200px]">
        <header class="mb-12">
            <h2 id="venues-heading" class="text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                <?= htmlspecialchars($venuesSection->title ?? 'Venues') ?>
            </h2>
        </header>
        
        <?php if (!empty($venues)): ?>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-6" role="list">
            <?php foreach ($venues as $venue): ?>
            <li>
                <article class="bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow h-full flex flex-col">
                    <!-- Venue Image -->
                    <?php if ($venue->hasImage()): ?>
                    <figure class="relative h-48 overflow-hidden">
                        <img src="<?= htmlspecialchars($venue->getImagePath()) ?>" 
                             alt="<?= htmlspecialchars($venue->getImageAlt()) ?>" 
                             class="w-full h-full object-cover"
                             loading="lazy" />
                    </figure>
                    <?php endif; ?>
                    
                    <!-- Venue Content -->
                    <div class="p-6 flex flex-col flex-grow">
                        <header class="mb-4">
                            <h3 class="text-2xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                                <?= htmlspecialchars($venue->name) ?>
                            </h3>
                        </header>
                        
                        <!-- Venue Details -->
                        <dl class="space-y-2 mb-4">
                            <!-- Address -->
                            <div class="flex items-start text-sm text-gray-700">
                                <dt class="sr-only">Address</dt>
                                <dd class="flex items-start w-full">
                                    <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <address class="not-italic"><?= htmlspecialchars($venue->getFullAddress()) ?></address>
                                </dd>
                            </div>
                            
                            <!-- Capacity -->
                            <div class="flex items-center text-sm text-gray-700">
                                <dt class="sr-only">Capacity</dt>
                                <dd class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <data value="<?= $venue->capacity ?? 0 ?>"><?= htmlspecialchars($venue->getCapacityDisplay()) ?></data>
                                </dd>
                            </div>
                            
                            <!-- Phone (if available) -->
                            <?php if ($venue->phone): ?>
                            <div class="flex items-center text-sm text-gray-700">
                                <dt class="sr-only">Phone</dt>
                                <dd class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <a href="tel:<?= htmlspecialchars($venue->phone) ?>" class="hover:underline">
                                        <?= htmlspecialchars($venue->phone) ?>
                                    </a>
                                </dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                        
                        <!-- Description -->
                        <?php if ($venue->description_html): ?>
                        <aside class="text-sm text-gray-600 mb-4 prose prose-sm max-w-none">
                            <?= $venue->description_html ?>
                        </aside>
                        <?php endif; ?>
                        
                        <!-- Actions -->
                        <footer class="mt-auto">
                            <nav aria-label="Venue actions">
                                <a href="<?= htmlspecialchars($venue->getMapLink()) ?>" 
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center text-sm font-semibold text-gray-900 hover:text-gray-600 transition-colors"
                                   aria-label="View <?= htmlspecialchars($venue->name) ?> on Google Maps">
                                    <span>View Map</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </nav>
                        </footer>
                    </div>
                </article>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <div class="text-center py-16" role="status" aria-live="polite">
                <figure class="inline-block border-2 border-gray-200 rounded-2xl p-12 bg-white">
                    <div class="text-6xl mb-4" aria-hidden="true">📍</div>
                    <figcaption class="text-gray-600 text-lg">Venue information coming soon.</figcaption>
                </figure>
            </div>
        <?php endif; ?>
    </div>
</section>