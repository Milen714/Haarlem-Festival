<?php
/** @var \app\ViewModels\Dance\LineupViewModel $vm */
$groupedSchedule = [];
foreach ($vm->schedulesSection as $item) {
    $dateKey = $item->date->format('Y-m-d');
    $groupedSchedule[$dateKey][] = $item;
}
?>
<div class="space-y-16" id="schedule-container">
    <?php foreach ($vm->groupedSchedules as $date => $sessions): ?>
        <div class="schedule-day-group space-y-8" id="group-<?= $date ?>">
            <h3 class="text-[var(--dance-tag-color-1)] text-xl font-bold mb-8 border-b border-gray-800 pb-2 uppercase tracking-widest">
                <?= date('l, d F Y', strtotime($date)) ?>
            </h3>

            <div class="grid grid-cols-1 gap-6">
                <?php foreach ($sessions as $session): ?>
                    <?php 
                        // Find the ticket info in our lookup table using the schedule_id from your dump
                        $ticket = $ticketLookup[$session->schedule_id] ?? null;
                        $ticketId = $ticket['id'] ?? null;
                        $price = $ticket['price'] ?? 0;
                    ?>
                    <div class="flex flex-col md:flex-row rounded-lg overflow-hidden border border-gray-800 hover:border-gray-600 transition-all duration-300">
                        
                        <div class="w-full md:w-48 h-48 md:h-auto overflow-hidden">
                            <img src="<?= $session->artist->getProfileImagePath() ?>" 
                                 alt="<?= $session->artist->name ?>"
                                 class="w-full h-full object-cover transition duration-500">
                        </div>

                        <div class="flex-1 p-6 flex flex-col md:flex-row justify-between items-start gap-6">
                            <div class="md:text-left">
                                <h4 class="text-2xl font-black uppercase tracking-tighter text-white mb-2">
                                    <?= htmlspecialchars($session->artist->name) ?>
                                </h4>
                                <p class="text-sm font-medium">
                                    <span class="mr-2">📍 <?= htmlspecialchars($session->venue->name) ?></span>
                                    <span class="opacity-50">|</span>
                                    <span class="ml-2">🕒 <?= $session->start_time->format('H:i') ?> - <?= $session->end_time->format('H:i') ?></span>
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="/events-dance/artist/<?= $session->artist->slug ?>" 
                                   class="px-6 py-2 border border-gray-600 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-white hover:text-black transition text-center">
                                    More Info
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
        </div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/ticket-modal.php'; ?>

<script src="/Js/dance-modal.js"></script>