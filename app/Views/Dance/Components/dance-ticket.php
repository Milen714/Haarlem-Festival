<?php
namespace App\Views\Dance\Components;

/** @var array $passTicketTypes */
/** @var object|null $ticketSection */

$dayPassByDate = [];
$weekendPass = null;

foreach ($passTicketTypes as $pt) {
    $enum = $pt->ticket_scheme->scheme_enum->value ?? $pt->ticket_scheme->scheme_enum ?? '';
    
    if ($enum === 'DANCE_WEEK_PASS' || $enum === 'DANCE_WEEKEND_PASS') {
        if (!$weekendPass) $weekendPass = $pt;
        
    } elseif ($enum === 'DANCE_ALL_DAY') {
        $dateKey = $pt->schedule->date ? $pt->schedule->date->format('Y-m-d') : 'unknown';
        if (!isset($dayPassByDate[$dateKey])) {
            $dayPassByDate[$dateKey] = $pt;
        }
    }
}

ksort($dayPassByDate);
$dayPasses = array_values($dayPassByDate);
?>

<section class="bg-black py-24 px-6">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="inline-block text-[var(--dance-tag-color-1)] text-2xl font-bold uppercase tracking-[0.2em] border-b-2 border-[var(--dance-tag-color-1)] mb-8 pb-2">
            <?= htmlspecialchars($ticketSection->section_title ?? 'All-Access Experience') ?>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 max-w-6xl mx-auto">
            
            <?php if ($weekendPass): 
                $wPrice = (float)($weekendPass->ticket_scheme->price ?? 0);
                $wId = $weekendPass->ticket_type_id;
            ?>
            <div class="bg-[#050505] border border-white/5 p-8 flex flex-col items-center hover:border-[#f5c35e] transition-all relative">
                <span class="absolute -top-3 left-4 bg-red-600 text-white text-[10px] font-bold px-3 py-1 uppercase rounded-sm z-10">Best Value</span>
                <h3 class="text-white text-xl font-bold mb-4 mt-4"><?= htmlspecialchars($weekendPass->ticket_scheme->name) ?></h3>
                <p class="text-[#f5c35e] text-3xl font-black mb-6">€<?= number_format($wPrice, 2) ?></p>
                <ul class="text-gray-400 text-xs space-y-3 mb-12 text-center list-none flex-grow">
                    <li>• Access to ALL 4 days</li>
                    <li>• ALL Back-to-Back Specials</li>
                    <li>• ALL Club Venues</li>
                </ul>
                <button 
                    onclick="openDanceModal(this)"
                    data-ticket-type-id="<?= $wId ?>"
                    data-artist="Full Weekend Pass"
                    data-date="July 23 - July 26, 2026"
                    data-venue="All Participating Venues"
                    data-price="<?= $wPrice ?>"
                    data-time="4-Day Access"
                    class="w-full bg-[#f5c35e] hover:bg-white py-3 text-black font-bold uppercase text-[10px] rounded-md transition-all active:scale-95">
                    BUY TICKETS
                </button>
            </div>
            <?php endif; ?>

            <?php foreach ($dayPasses as $dp): 
                $date = $dp->schedule->date;
                $dayName = $date->format('l');
                $shortDate = $date->format('d M');
                $dPrice = (float)($dp->ticket_scheme->price ?? 0);
                $dId = $dp->ticket_type_id;
            ?>
            <div class="bg-[#050505] border border-white/5 p-8 flex flex-col items-center hover:border-[#f5c35e] transition-all">
                <h3 class="text-white text-xl font-bold mb-4 mt-4"><?= $dayName ?> Access</h3>
                <p class="text-white text-3xl font-black mb-6">€<?= number_format($dPrice, 2) ?></p>
                <ul class="text-gray-400 text-xs space-y-3 mb-12 text-center list-none flex-grow">
                    <li>• All Club Shows on <?= $dayName ?></li>
                    <li>• All B2B Sessions on <?= $shortDate ?></li>
                    <li>• Priority Club Entry</li>
                </ul>
                <button 
                    onclick="openDanceModal(this)"
                    data-ticket-type-id="<?= $dId ?>"
                    data-artist="<?= $dayName ?> Day Pass"
                    data-date="<?= $date->format('l, F d, Y') ?>"
                    data-venue="All Participating Venues"
                    data-price="<?= $dPrice ?>"
                    data-time="Full Day Access"
                    class="w-full bg-[#f5c35e] hover:bg-white py-3 text-black font-bold uppercase text-[10px] rounded-md transition-all active:scale-95">
                    BUY TICKETS
                </button>
            </div>
            <?php endforeach; ?>

        </div>

        <p class="mt-12 text-gray-500 text-xs italic">
            ℹ️ Access to clubs is subject to safety regulations and venue capacity.
        </p>
    </div>
</section>