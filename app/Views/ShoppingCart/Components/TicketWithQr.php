<?
namespace App\Views\ShoppingCart\Components;
/** @var OrderItem $item */
?>
<div class="bg-white border rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row">
    <div class="p-6 flex-1">
        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">
            <?= htmlspecialchars($item->ticket_type->schedule->event_category->title ?? 'Event') ?>
        </span>
        <h2 class="text-2xl font-black mt-1">
            <?= htmlspecialchars($item->ticket_type->description ?? $item->ticket_type->ticket_scheme->name ?? 'Standard Ticket') ?>
        </h2>
        <p class="text-gray-500 mt-2">Quantity: <span class="font-bold text-black"><?= $item->quantity ?>
                Person(s)</span></p>
        <p class="text-sm text-gray-400"><?= htmlspecialchars($item->ticket_type->schedule->venue->name ?? '') ?></p>
    </div>

    <div
        class="bg-gray-50 p-6 flex flex-col items-center justify-center border-t md:border-t-0 md:border-l border-gray-100">
        <?php if ($item->qr_code_hash): ?>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($item->qr_code_hash) ?>"
            alt="QR Code" class="w-32 h-32 bg-white p-2 border shadow-sm">

        <p class="text-[10px] font-mono text-gray-400 mt-2 uppercase">
            <?= htmlspecialchars($item->qr_code_hash) ?>
        </p>
        <?php else: ?>
        <div class="w-32 h-32 border-2 border-dashed border-gray-300 flex items-center justify-center text-center p-2">
            <span class="text-[10px] text-gray-400 uppercase font-bold">No QR Available</span>
        </div>
        <?php endif; ?>
    </div>
</div>