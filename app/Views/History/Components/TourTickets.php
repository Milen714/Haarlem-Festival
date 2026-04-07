<?php /** @var App\ViewModels\History\TicketHistoryViewModel $ticketOptions */ ?>

<script>
    const tourOptionsTree = <?= json_encode($ticketOptions->options) ?>;
</script>

<section id="book-tour" class="container mx-auto max-w-[1100px] px-4 my-24">
    
    <div class="mb-12">
        <h3 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900 font-bold">
            <?= htmlspecialchars($tourTickets->title ?? '') ?>
        </h3>
        <div class="underline-history"></div>
        <div class="text-sm text-ink-700"><?= $tourTickets->content_html ?? '' ?></div>
    </div>
                            
    <form id="ticket-form" class="flex flex-col lg:flex-row gap-8 items-start">
        
        <div class="ticket-options w-full lg:w-2/3 p-6 md:p-8 ">
            
            <div class="mb-8">                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-[#FFF1C8] border border-[#CAA359] rounded-md">
                        <div>
                            <div class="font-semibold text-ink-900">Normal Ticket</div>
                            <div class="text-sm text-ink-700">€<?= number_format($ticketOptions->normalPrice, 2) ?></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="changeQty('qty-normal',-1)" class="w-8 h-8 rounded-full bg-[#D1EC92]  border border-[#263209] font-bold text-lg hover:bg-[#CAA359] transition-colors">−</button>
                            <span id="qty-normal-display" class="w-6 text-center font-semibold">0</span>
                            <button type="button" onclick="changeQty('qty-normal',1)" class="w-8 h-8 rounded-full bg-[#D1EC92] border border-[#263209] font-bold text-lg hover:bg-[#CAA359] transition-colors">+</button>
                        </div>
                        <input type="hidden" id="qty-normal" name="qtyNormal" value="0" data-precio="<?= $ticketOptions->normalPrice ?>">
                    </div>

                    <div class="flex justify-between items-center p-4 bg-[#FFF1C8] border border-[#CAA359] rounded-md">
                        <div>
                            <div class="font-semibold text-ink-900">Family Ticket</div>
                            <div class="text-sm text-ink-700">€<?= number_format($ticketOptions->familyPrice, 2) ?> </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="changeQty('qty-family',-1)" class="w-8 h-8 rounded-full bg-[#D1EC92] border border-[#263209] font-bold text-lg hover:bg-[#CAA359] transition-colors">−</button>
                            <span id="qty-family-display" class="w-6 text-center font-semibold">0</span>
                            <button type="button" onclick="changeQty('qty-family',1)" class="w-8 h-8 rounded-full bg-[#D1EC92] border border-[#263209] font-bold text-lg hover:bg-[#CAA359] transition-colors">+</button>
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
            <div class="border-t border-[#CAA359] pt-4 mb-10">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-ink-900">Total:</h3>
                    <h3 class="text-2xl font-bold text-ink-900">€<span id="summary-total">0.00</span></h3>
                </div>
            </div>
            
            <button type="submit" id="btn-submit" class="w-full btn-history text-center" style="padding: 0.5rem 1.25rem; font-size: 1rem;">
                Add to Cart
            </button>

            <a href="/personal-program" class="mt-3 w-full block text-center btn-history" style="padding: 0.5rem 1.25rem; font-size: 1rem;">
                See my program
            </a>

            <div id="error-container" class="mt-4"></div>

        </div>

    </form>
</section>

<!-- Success Modal -->
<div id="tour-cart-success" class="hidden fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative bg-[#FFF0C2] border border-[#CAA359] rounded-xl shadow-2xl p-8 max-w-xs w-full mx-4 flex flex-col items-center gap-5">
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-[#546A21]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <p class="font-history-serif text-lg font-bold text-ink-900 text-center">Tickets added to your cart!</p>
        <div class="flex gap-3 w-full">
            <button id="modal-keep-looking" type="button" class="flex-1 px-5 py-2 rounded-md font-medium text-ink-900 transition-colors bg-[#FAEBBD] border border-[#CAA359] hover:bg-[#FFE598]">
                Keep Looking
            </button>
            <a href="/payment" class="flex-1 px-5 py-2 rounded-md font-semibold text-white text-center bg-[#546A21] hover:bg-[#465e10] transition-colors">
                Go to Cart
            </a>
        </div>
    </div>
</div>

<script>
function changeQty(id, delta) {
    const input = document.getElementById(id);
    const display = document.getElementById(id + '-display');
    const max = id === 'qty-family' ? 3 : 12;
    const newVal = Math.min(max, Math.max(0, parseInt(input.value || 0) + delta));
    input.value = newVal;
    display.textContent = newVal;
    input.dispatchEvent(new Event('input'));
}
</script>
<script src="/Js/ShowError.js"></script>
<script src="/Js/TicketHistory.js"></script>