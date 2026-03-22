<?php
namespace App\Views\Dance\Components;
/** @var object|null $specialSection */
/** @var array $backtoback */
?>
<section class="bg-black py-20 px-6">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="inline-block text-[var(--dance-tag-color-1)] text-2xl font-bold uppercase tracking-[0.2em] border-b-2 border-[var(--dance-tag-color-1)] mb-6 pb-2">
            <?= htmlspecialchars($specialSection->section_title ?? 'Back2Back Specials') ?>
        </h2>
        
        <?php if ($specialSection?->content_html): ?>
        <div class="text-white max-w-3xl mx-auto mb-16 leading-relaxed text-lg md:text-xl">
            <?= $specialSection->content_html ?? '' ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-3 md:grid-cols-3 gap-8">
            <?php foreach ($backtoback as $session): ?>
                <?php 
                    // Find the ticket info in our lookup table using the schedule_id from your dump
                    $ticket = $ticketLookup[$session->schedule_id] ?? null;
                    $ticketId = $ticket['id'] ?? null;
                    $price = $ticket['price'] ?? 0;
                ?>
                <div class="relative group rounded-lg overflow-hidden bg-[#121212] flex flex-col">
                    
                    <div class="absolute bg-[var(--dance-tag-color-1)] top-4 left-4 z-20 px-3 py-2 rounded-md font-bold flex flex-col items-center leading-tight text-black">
                        <span class="text-[10px] uppercase"><?= $session->date->format('M') ?></span>
                        <span class="text-lg"><?= $session->date->format('d') ?></span>
                    </div>

                    <div class="h-80 overflow-hidden relative">
                        <img src="<?= $session->artist->profile_image->file_path ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             alt="<?= htmlspecialchars($session->artist->name) ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#121212] to-transparent opacity-80"></div>
                    </div>

                    <div class="p-6 text-left mt-auto">
                        <h4 class="text-white font-bold text-lg mb-4">
                            <?= htmlspecialchars($session->artist->name) ?>
                        </h4>
                        
                        <div class="space-y-1">
                            <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto">
                                📍 <?= htmlspecialchars($session->venue->name) ?>
                            </p>
                            <p class="text-gray-400 text-xs uppercase tracking-widest font-roboto">
                                ⏰ <?= $session->start_time->format('H:i') ?> - <?= $session->end_time->format('H:i') ?>
                            </p>
                        </div>
                        
                        <div class="mt-8 flex justify-between items-center">
                            <a href="/events-dance/artist/<?= $session->artist->slug ?>" 
                               class="text-[10px] text-gray-500 underline hover:text-white transition-colors">
                               More Info >
                            </a>
                            <button 
                                onclick="<?= $ticketId ? 'openDanceModal(this)' : '' ?>"
                                data-ticket-type-id="<?= $ticketId ?>"
                                data-price="<?= $price ?>"
                                data-artist="<?= htmlspecialchars($session->artist->name) ?>"
                                data-venue="<?= htmlspecialchars($session->venue->name) ?>"
                                data-date="<?= $session->date->format('l, F d, Y') ?>"
                                data-time="<?= $session->start_time->format('H:i') ?> - <?= $session->end_time->format('H:i') ?>"
                                class="px-6 py-2 rounded text-[10px] font-bold uppercase tracking-widest transition flex items-center gap-2
                                    <?= $ticketId ? 'bg-[#f5c35e] text-black hover:bg-white' : 'bg-gray-800 text-gray-500 cursor-not-allowed' ?>">
                                
                                <?php if ($ticketId): ?>
                                    <div class="flex flex-col">
                                        <span>Buy Tickets</span>
                                        <span>€<?= number_format($price, 2) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span>Sold Out</span>
                                <?php endif; ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($specialSection?->content_html_2): ?>
        <div class="text-white max-w-3xl mx-auto mt-16 leading-relaxed text-lg md:text-xl">
            <?= $specialSection?->content_html_2 ?? '' ?>
        </div>
        <?php endif; ?>
        <?php if ($specialSection?->cta_url): ?>
            <a href="<?= $specialSection->cta_url ?>" 
               class="inline-block mt-12 bg-[var(--dance-button-color)] hover:bg-white text-black font-bold py-4 px-12 rounded-full transition-all uppercase tracking-widest text-sm">
                <?= $specialSection->cta_text?>
            </a>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/ticket-modal.php'; ?>
<script src="/Js/dance-modal.js"></script>