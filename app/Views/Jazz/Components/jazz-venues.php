<?php
namespace App\Views\Jazz\Components;
?>

<section class="py-16 bg-gray-50" aria-labelledby="venues-heading">
    <article class="container mx-auto px-4">
        <h2 id="venues-heading" class="text-4xl font-bold mb-12" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($venuesSection->title ?? 'Venues') ?>
        </h2>
        
        <?php if (!empty($venues)): ?>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($venues as $venue): ?>
            <li class="border-2 border-gray-200 rounded-lg overflow-hidden bg-white hover:shadow-lg transition-shadow">
                <article>
                    <?php if (!empty($venue->image_path)): ?>
                    <figure>
                        <img src="/<?= htmlspecialchars($venue->image_path) ?>" 
                             alt="<?= htmlspecialchars($venue->image_alt ?? $venue->name) ?>" 
                             class="w-full h-48 object-cover" />
                    </figure>
                    <?php endif; ?>
                    
                    <section class="p-6">
                        <h3 class="text-2xl font-bold mb-3"><?= htmlspecialchars($venue->name) ?></h3>
                        
                        <dl class="space-y-2 text-sm text-gray-700 mb-4">
                            <dt class="sr-only">Address</dt>
                            <dd class="flex items-start">
                                <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <address class="not-italic">
                                    <?= htmlspecialchars($venue->street_address) ?>, 
                                    <?= htmlspecialchars($venue->postal_code) ?> 
                                    <?= htmlspecialchars($venue->city) ?>
                                </address>
                            </dd>
                            
                            <?php if ($venue->capacity): ?>
                            <dt class="sr-only">Capacity</dt>
                            <dd class="flex items-start">
                                <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <data value="<?= $venue->capacity ?>">Capacity: <?= number_format($venue->capacity) ?></data>
                            </dd>
                            <?php endif; ?>
                            
                            <?php if ($venue->phone): ?>
                            <dt class="sr-only">Phone</dt>
                            <dd class="flex items-start">
                                <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $venue->phone)) ?>" class="hover:text-[var(--pastel-lavender)]">
                                    <?= htmlspecialchars($venue->phone) ?>
                                </a>
                            </dd>
                            <?php endif; ?>
                        </dl>
                        
                        <?php if ($venue->description_html): ?>
                            <div class="text-gray-600 mb-4">
                                <?= $venue->description_html ?>
                            </div>
                        <?php endif; ?>
                    </section>
                </article>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p class="text-center text-gray-500 py-12">Venue information coming soon!</p>
        <?php endif; ?>
    </article>
</section>