


<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
  
  <section class="relative w-full max-w-4xl bg-[#4a0e0e] border-2 border-[#d4a356] rounded-xl shadow-2xl overflow-hidden text-white font-sans">
    
    <header class="bg-[#1a0505] py-6 text-center border-b border-[#d4a356]/30">
      <h2 class="text-3xl font-serif tracking-wide">Make a Reservation</h2>
    </header>

    <div class="p-6 md:p-8 space-y-8 overflow-y-auto max-h-[85vh]">
      
      <aside class="border border-[#d4a356] p-3 rounded bg-[#d4a356]/10 flex items-center justify-center space-x-2">
        <span class="text-[#d4a356]">⚠️</span>
        <p class="text-[10px] md:text-xs uppercase font-bold tracking-widest text-[#d4a356]">
          Reservation Fee: €10 extra per booking (non-refundable)
        </p>
      </aside>

      <fieldset>
        <legend class="text-[#d4a356] text-xs font-bold uppercase mb-3 flex items-center">
          <input type="checkbox" checked class="mr-2 accent-[#d4a356]"> Select Date
        </legend>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
          <button class="border border-[#d4a356]/50 p-3 rounded hover:bg-[#d4a356] hover:text-black transition">
            <span class="block text-[10px] uppercase">Thursday</span>
            <span class="block font-bold">24th July</span>
          </button>
          <button class="border border-[#d4a356]/50 p-3 rounded hover:bg-[#d4a356] hover:text-black transition">
            <span class="block text-[10px] uppercase">Friday</span>
            <span class="block font-bold">25th July</span>
          </button>
          <button class="bg-[#d4a356] text-black p-3 rounded border border-[#d4a356]">
            <span class="block text-[10px] uppercase">Saturday</span>
            <span class="block font-bold">26th July</span>
          </button>
          <button class="border border-[#d4a356]/50 p-3 rounded hover:bg-[#d4a356] hover:text-black transition">
            <span class="block text-[10px] uppercase">Sunday</span>
            <span class="block font-bold">27th July</span>
          </button>
        </div>
      </fieldset>

      <fieldset>
        <legend class="text-[#d4a356] text-xs font-bold uppercase mb-3 flex items-center">
          <input type="checkbox" checked class="mr-2 accent-[#d4a356]"> Select Session (2h duration)
        </legend>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <button class="border border-[#d4a356] p-4 rounded text-left flex justify-between items-center group hover:bg-[#d4a356]/10">
            <div>
              <span class="block font-bold">Session 1</span>
              <span class="text-[10px] opacity-60">Duration: 2h</span>
            </div>
            <span class="text-xl font-serif">17:00</span>
          </button>
          <button class="bg-[#1a0505] border-2 border-[#d4a356] p-4 rounded text-left flex justify-between items-center">
             <div>
              <span class="block font-bold text-[#d4a356]">Session 2</span>
              <span class="text-[10px] opacity-60">Duration: 2h</span>
            </div>
            <span class="text-xl font-serif text-[#d4a356]">19:00</span>
          </button>
          <button class="border border-[#d4a356] p-4 rounded text-left flex justify-between items-center opacity-40">
            <div>
              <span class="block font-bold">Session 3</span>
              <span class="text-[10px]">Fully Booked</span>
            </div>
            <span class="text-xl font-serif">21:00</span>
          </button>
        </div>
      </fieldset>

      <div class="flex flex-col">
        <label class="text-[#d4a356] text-xs font-bold uppercase mb-2">Special Requests</label>
        <textarea 
          placeholder="Dietary restrictions, allergies, special occasions, etc..." 
          class="bg-transparent border border-[#d4a356]/50 rounded p-4 text-sm focus:border-[#d4a356] outline-none h-24 italic"
        ></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-[#1a0505] border border-[#d4a356]/30 rounded p-4">
          <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold uppercase tracking-widest">Adults: <span class="text-[#d4a356]">€45</span></span>
            <span class="text-[10px] opacity-50">per person</span>
          </div>
          <div class="flex items-center justify-between border border-[#d4a356] rounded px-2">
            <button class="p-2 text-[#d4a356] font-bold">-</button>
            <span class="font-bold">0</span>
            <button class="p-2 text-[#d4a356] font-bold">+</button>
          </div>
        </div>

        <div class="bg-[#1a0505] border border-[#d4a356]/30 rounded p-4">
          <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold uppercase tracking-widest">Kids: <span class="text-[#d4a356]">€22.50</span></span>
            <span class="text-[10px] opacity-50">per person</span>
          </div>
          <div class="flex items-center justify-between border border-[#d4a356] rounded px-2">
            <button class="p-2 text-[#d4a356] font-bold">-</button>
            <span class="font-bold">0</span>
            <button class="p-2 text-[#d4a356] font-bold">+</button>
          </div>
        </div>
      </div>

      <footer class="border-t border-[#d4a356]/30 pt-4 space-y-2">
        <dl class="text-xs font-medium space-y-1">
          <div class="flex justify-between">
            <dt>0 Adults × €45</dt>
            <dd>€0</dd>
          </div>
          <div class="flex justify-between">
            <dt>0 Children × €22.50</dt>
            <dd>€0</dd>
          </div>
          <div class="flex justify-between italic">
            <dt>Reservation Fee</dt>
            <dd>€0</dd>
          </div>
          <div class="flex justify-between text-xl font-serif text-[#d4a356] border-t border-[#d4a356]/20 mt-2 pt-2">
            <dt>Total</dt>
            <dd>€0</dd>
          </div>
        </dl>
      </footer>

      <div class="grid grid-cols-2 gap-4 pt-4">
        <button class="bg-[#d4a356] text-black font-bold py-3 rounded uppercase text-sm hover:brightness-110">
          Cancel Reservation
        </button>
        <button class="bg-black text-[#d4a356] border border-[#d4a356] font-bold py-3 rounded uppercase text-sm hover:bg-[#d4a356] hover:text-black transition">
          Confirm Reservation
        </button>
      </div>
    </div>
  </section>
</div>