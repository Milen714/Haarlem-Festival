<?php /** @var App\ViewModels\History\TicketHistoryViewModel $ticketOptions */ ?>

<script>
    const tourOptionsTree = <?= json_encode($ticketOptions->options) ?>;
</script>

<section id="book-tour" class="container mx-auto max-w-[1100px] px-4 my-24">
    
    <div class="mb-12">
        <h3 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900 font-bold">
            Tickets
        </h3>
        <div class="underline-history"></div>
    </div>

    <form id="ticket-form" class="flex flex-col lg:flex-row gap-8 items-start">
        
        <div class="ticket-options w-full lg:w-2/3 p-6 md:p-8 ">
            
            <div class="mb-8">
                <h3 class="font-history-serif text-xl text-ink-900 font-bold mb-4">Select Tickets</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-[#FFF1C8] border border-[#CAA359] rounded-md">
                        <div>
                            <div class="font-semibold text-ink-900">Normal Ticket</div>
                            <div class="text-sm text-ink-700">€<?= number_format($ticketOptions->normalPrice, 2) ?> per person</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="changeQty('qty-normal',-1)" class="w-8 h-8 rounded-full border border-[#CAA359] font-bold text-lg hover:bg-[#CAA359] transition-colors">−</button>
                            <span id="qty-normal-display" class="w-6 text-center font-semibold">0</span>
                            <button type="button" onclick="changeQty('qty-normal',1)" class="w-8 h-8 rounded-full border border-[#CAA359] font-bold text-lg hover:bg-[#CAA359] transition-colors">+</button>
                        </div>
                        <input type="hidden" id="qty-normal" name="qtyNormal" value="0" data-precio="<?= $ticketOptions->normalPrice ?>">
                    </div>

                    <div class="flex justify-between items-center p-4 bg-[#FFF1C8] border border-[#CAA359] rounded-md">
                        <div>
                            <div class="font-semibold text-ink-900">Family Ticket</div>
                            <div class="text-sm text-ink-700">Max 4 participants. €<?= number_format($ticketOptions->familyPrice, 2) ?> total</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="changeQty('qty-family',-1)" class="w-8 h-8 rounded-full border border-[#CAA359] font-bold text-lg hover:bg-[#CAA359] transition-colors">−</button>
                            <span id="qty-family-display" class="w-6 text-center font-semibold">0</span>
                            <button type="button" onclick="changeQty('qty-family',1)" class="w-8 h-8 rounded-full border border-[#CAA359] font-bold text-lg hover:bg-[#CAA359] transition-colors">+</button>
                        </div>
                        <input type="hidden" id="qty-family" name="qtyFamily" value="0" data-precio="<?= $ticketOptions->familyPrice ?>">
                    </div>
                </div>
            </div>

            <div class="mb-8" id="step-language">
                <h3 class="font-history-serif text-xl text-ink-900 font-bold mb-4">Select Language</h3>
                <div class="flex flex-wrap gap-3">
                    <?php foreach (array_keys($ticketOptions->options) as $language): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="language" value="<?= htmlspecialchars($language) ?>" class="peer sr-only" required>
                            <div class="tour-radio-btn"><?= htmlspecialchars($language) ?></div>
                        </label>    
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mb-8 hidden opacity-0 transition-opacity duration-500" id="step-date">
                <h3 class="font-history-serif text-xl text-ink-900 font-bold mb-4">Select Date</h3>
                <div class="flex flex-wrap gap-3" id="dates-container">
                    </div>
            </div>

            <div class="mb-8 hidden opacity-0 transition-opacity duration-500" id="step-time">
                <h3 class="font-history-serif text-xl text-ink-900 font-bold mb-4">Select Time</h3>
                <div class="flex flex-wrap gap-3" id="times-container">
                    </div>
            </div>

        </div>

        <div class="order-overview w-full lg:w-1/3 bg-[#FFF0C2] border border-[#CAA359] rounded-[0.5rem] p-6 shadow-sm sticky top-6">
            <h2 class="font-history-serif text-2xl text-ink-900 font-bold mb-6 border-b border-[#CAA359] pb-4">
                Order Overview
            </h2>
            
            <div id="summary-details" class="space-y-3 mb-6 text-ink-900 hidden">
                <div class="flex justify-between">
                    <span class="font-semibold">Tickets:</span>
                    <span id="summary-qty-text" class="text-right">0</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="font-semibold">Date:</span>
                    <span id="summary-date-text" class="text-right">-</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="font-semibold">Time:</span>
                    <span id="summary-time-text" class="text-right">-</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="font-semibold">Language:</span>
                    <span id="summary-lang-text" class="text-right">-</span>
                </div>
            </div>
            <div class="border-t border-[#CAA359] pt-4 mb-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-ink-900">Total:</h3>
                    <h3 class="text-2xl font-bold text-ink-900">€<span id="summary-total">0.00</span></h3>
                </div>
            </div>
            
            <button type="submit" id="btn-submit" class="w-full bg-[#546A21] hover:bg-[#465e10] text-white font-semibold py-3 px-4 rounded-md transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#546A21]">
                Add to Cart
            </button>

            <div id="error-container" class="mt-4"></div>

            <div id="tour-cart-success" class="hidden mt-4 p-4 bg-[#eef3e2] border border-[#546A21] rounded-md font-history-serif text-ink-900 text-center text-sm font-semibold">
                ✓ Tickets added to your cart!
            </div>
        </div>

    </form>
</section>

<script>
function changeQty(id, delta) {
    const input = document.getElementById(id);
    const display = document.getElementById(id + '-display');
    const newVal = Math.max(0, parseInt(input.value || 0) + delta);
    input.value = newVal;
    display.textContent = newVal;
    input.dispatchEvent(new Event('input'));
}
</script>
<script src="/Js/TicketHistory.js"></script>