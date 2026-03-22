<dialog id="dance-ticket-modal" 
    class="w-1/2 rounded-2xl shadow-2xl p-0 bg-[#121212] border border-white/10 backdrop:bg-black/80 backdrop:backdrop-blur-sm"
    aria-modal="true">

    <article class="flex flex-col text-white">
        <button type="button" class="self-end mt-4 mr-4 text-gray-500 hover:text-white transition-colors" onclick="closeTicketModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <section id="modal-state-select" class="p-8 pt-0">
            <header class="text-center mb-6">
                <h2 class="text-2xl font-black uppercase tracking-tighter">Buy Tickets</h2>
                <p class="text-gray-500 text-sm">Choose how many tickets you want</p>
            </header>

            <div class="bg-[#1a1a1a] rounded-xl border-l-4 border-[#f5c35e] p-4 mb-6">
                <h3 id="select-artist-name" class="font-bold text-lg"></h3>
                <p id="select-datetime" class="text-sm text-gray-400 mt-1"></p>
                <p id="select-venue-name" class="text-sm text-gray-400 mt-0.5"></p>
                <p class="text-sm font-semibold text-[#f5c35e] mt-2"><span id="select-price-per"></span> per ticket</p>
            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="flex items-center bg-black border border-white/10 rounded-lg overflow-hidden">
                    <button id="qty-decrease" class="px-5 py-2 hover:bg-white/5 text-xl" onclick="changeQty(-1)">&minus;</button>
                    <output id="ticket-qty" class="w-12 text-center text-xl font-bold">1</output>
                    <button id="qty-increase" class="px-5 py-2 bg-[#f5c35e] text-black font-bold text-xl" onclick="changeQty(1)">+</button>
                </div>
                <div class="ml-auto text-right">
                    <span class="text-xs text-gray-500 block uppercase">Total</span>
                    <data id="select-line-total" class="font-black text-2xl text-white"></data>
                </div>
            </div>

            <footer class="grid grid-cols-2 gap-4">
                <button onclick="closeTicketModal()" class="py-3 rounded-lg border border-white/10 font-bold text-gray-400 hover:text-white transition">Cancel</button>
                <button id="modal-add-btn" onclick="confirmTicketAdd()" class="py-3 rounded-lg bg-[#f5c35e] text-black font-black uppercase hover:bg-white transition">Add to Program</button>
            </footer>
        </section>

        <section id="modal-state-confirm" class="hidden p-8 pt-0 text-center">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-[#f5c35e] mx-auto mb-4">
                <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7" /></svg>
            </div>
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Added to Your Program!</h2>
            <p id="confirm-subtitle" class="text-gray-400 text-sm mb-6"></p>

            <div class="bg-[#1a1a1a] rounded-xl border-l-4 border-[#f5c35e] p-4 mb-6 text-left">
                <h3 id="confirm-artist-name" class="font-bold text-white"></h3>
                <p id="confirm-datetime" class="text-sm text-gray-400"></p>
                <div class="flex justify-between items-center mt-3">
                    <mark id="confirm-ticket-label" class="bg-[#f5c35e]/20 text-[#f5c35e] text-xs font-bold px-3 py-1 rounded-full"></mark>
                    <data id="confirm-line-total" class="font-bold text-white"></data>
                </div>
            </div>

            <div class="flex justify-between text-sm border-t border-white/5 pt-4 mb-8">
                <div class="text-left">
                    <p class="text-gray-400">Current cart total:</p>
                    <p id="confirm-cart-items" class="text-[10px] uppercase tracking-widest text-gray-600"></p>
                </div>
                <data id="confirm-cart-total" class="font-bold text-[#f5c35e] text-lg"></data>
            </div>

            <footer class="grid grid-cols-2 gap-4">
                <button onclick="closeTicketModal()" class="py-3 rounded-lg border border-white/10 text-gray-400 font-bold hover:text-white">Continue Shopping</button>
                <a href="/my-program" class="py-3 rounded-lg bg-[#f5c35e] text-black font-black uppercase text-center hover:bg-white transition">View Program &rarr;</a>
            </footer>
        </section>
    </article>
</dialog>