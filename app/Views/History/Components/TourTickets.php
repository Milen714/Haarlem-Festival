<?php 
//hardcoded for now
$normalPrice = 17.50;
$familyPrice = 60.00;
?>

<section id="book-tour" class="container mx-auto max-w-[1100px] px-4 my-24">
    
    <div class="mb-12">
        <h3 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900 font-bold">
            Book your Tour
        </h3>
        <div class="underline-history"></div>
    </div>

    <form id="ticket-form" class="flex flex-col lg:flex-row gap-8 items-start">
        
        <div class="ticket-options w-full lg:w-2/3 p-6 md:p-8 ">
            
            <div class="mb-8">
                <h3 class="font-history-serif text-xl text-ink-900 font-bold mb-4">1. Select Tickets</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-[#FFF1C8] border border-[#CAA359] rounded-md">
                        <div>
                            <div class="font-semibold text-ink-900">Normal Ticket</div>
                            <div class="text-sm text-ink-700">€<?= number_format($normalPrice, 2) ?> per person</div>
                        </div>
                        <input type="number" name="qtyNormal" min="0" value="0" 
                               class="w-20 px-3 py-2 border border-[#CAA359] rounded-md text-center focus:ring-2 focus:ring-[#546A21] focus:border-[#546A21] outline-none">
                    </div>

                    <div class="flex justify-between items-center p-4 bg-[#FFF1C8] border border-[#CAA359] rounded-md">
                        <div>
                            <div class="font-semibold text-ink-900">Family Ticket</div>
                            <div class="text-sm text-ink-700">Max 4 participants. €<?= number_format($familyPrice, 2) ?> total</div>
                        </div>
                        <input type="number" name="qtyFamily" min="0" value="0" 
                               class="w-20 px-3 py-2 border border-[#CAA359] rounded-md text-center focus:ring-2 focus:ring-[#546A21] focus:border-[#546A21] outline-none">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="font-history-serif text-xl text-ink-900 font-bold mb-4">2. Select Date</h3>
                <div class="flex flex-wrap gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="date" value="2026-07-24" class="peer sr-only" required>
                        <div class="tour-radio-btn">Thu, 24 July</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="date" value="2026-07-25" class="peer sr-only">
                        <div class="tour-radio-btn">Fri, 25 July</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="date" value="2026-07-26" class="peer sr-only">
                        <div class="tour-radio-btn">Sat, 26 July</div>
                    </label>
                </div>
            </div>

            <div>
                <h3 class="font-history-serif text-xl text-ink-900 font-bold mb-4">3. Select Language</h3>
                <div class="flex flex-wrap gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="language" value="English" class="peer sr-only" required>
                        <div class="tour-radio-btn">English</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="language" value="Dutch" class="peer sr-only">
                        <div class="tour-radio-btn">Dutch</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="language" value="Chinese" class="peer sr-only">
                        <div class="tour-radio-btn">Chinese</div>
                    </label>
                </div>
            </div>

        </div>

        <div class="order-overview w-full lg:w-1/3 bg-[#FFF0C2] border border-[#CAA359] rounded-[0.5rem] p-6 shadow-sm sticky top-6">
            <h2 class="font-history-serif text-2xl text-ink-900 font-bold mb-6 border-b border-[#CAA359] pb-4">
                Order Overview
            </h2>
            
            <div class="border-t border-[#CAA359] pt-4 mb-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-ink-900">Total:</h3>
                    <h3 class="text-2xl font-bold text-ink-900">€<span id="summary-total">0.00</span></h3>
                </div>
            </div>
            
            <button type="submit" id="btn-submit" class="w-full bg-[#546A21] hover:bg-[#465e10] text-white font-semibold py-3 px-4 rounded-md transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#546A21]">
                Add to Cart
            </button>
        </div>

    </form>
</section>