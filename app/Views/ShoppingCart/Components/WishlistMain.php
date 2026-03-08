<main class="bg-gray-50 min-h-screen py-10 px-4">
  <div class="max-w-7xl mx-auto">
    <header class="mb-8">
      <nav class="text-xs text-gray-400 mb-4" aria-label="Breadcrumb">Home</nav>
      <h1 class="text-3xl font-bold text-[#1e4b6e]">My Tickets</h1>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <section class="lg:col-span-2 space-y-6">
        <nav class="flex bg-gray-200 p-1 rounded-md w-fit" aria-label="Ticket Tabs">
          <button class="px-4 py-2 text-xs font-bold text-gray-500">My Program (3)</button>
          <button class="px-4 py-2 text-xs font-bold bg-white text-[#1e4b6e] rounded shadow-sm">My Tickets</button>
          <button class="px-4 py-2 text-xs font-bold text-gray-500">Shopping Cart</button>
        </nav>

        <div class="bg-white p-6 rounded-lg shadow-sm">
          <div class="flex space-x-px mb-6 overflow-hidden rounded-md border border-gray-200">
            <button class="flex-1 py-2 text-center bg-blue-50 text-[#1e4b6e] border-r border-gray-200">
                <p class="text-[10px] uppercase">Thursday</p>
                <p class="font-bold">24</p>
            </button>
            <button class="flex-1 py-2 text-center bg-blue-50 text-[#1e4b6e] border-r border-gray-200">
                <p class="text-[10px] uppercase">Friday</p>
                <p class="font-bold">25</p>
            </button>
            <button class="flex-1 py-2 text-center bg-[#004a7c] text-white">
                <p class="text-[10px] uppercase">Saturday</p>
                <p class="font-bold">26</p>
            </button>
            <button class="flex-1 py-2 text-center bg-blue-50 text-[#1e4b6e]">
                <p class="text-[10px] uppercase">Sunday</p>
                <p class="font-bold">27</p>
            </button>
          </div>

          <article class="relative flex border border-gray-200 rounded-md overflow-hidden bg-white mb-6">
            <div class="w-2 bg-yellow-400"></div>
            <div class="p-4 flex-grow">
              <span class="text-[10px] font-bold text-amber-600 uppercase tracking-tighter">Haarlem Dance!</span>
              <div class="flex justify-between items-start mt-1">
                <div>
                    <h2 class="font-bold text-lg">Hardwell</h2>
                    <p class="text-xs text-gray-500">📅 2026-06-26 · 19:00 - 20:00</p>
                    <p class="text-xs text-gray-500">📍 XD the Club</p>
                    <p class="text-xs text-gray-500">👤 x 1</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold">€ 90</p>
                </div>
              </div>
              <footer class="mt-4 flex items-center justify-between border-t pt-2">
                <button class="text-[10px] text-blue-600 font-bold uppercase tracking-widest">🎟 View Ticket</button>
                <span class="bg-[#00c9a7] text-white text-[10px] px-3 py-1 rounded-full">Owned</span>
              </footer>
            </div>
            <figure class="w-32 bg-gray-100">
                <img src="artist.jpg" alt="Hardwell" class="w-full h-full object-cover grayscale">
            </figure>
          </article>

          <aside class="flex items-start bg-amber-50 p-4 rounded-md border border-amber-100">
            <span class="mr-3 text-amber-500">ℹ️</span>
            <div>
              <h4 class="text-xs font-bold text-amber-900">Your Program is saved automatically</h4>
              <p class="text-xs text-amber-800 opacity-80 mt-1 leading-relaxed">Share with friends or convert saved Tickets to order when ready. Items in your Program are not confirmed until payment is completed.</p>
            </div>
          </aside>
        </div>
      </section>

      <aside class="space-y-4">
        <button class="w-full bg-[#004a7c] text-white py-3 rounded-md font-bold text-sm flex items-center justify-center">
            <span class="mr-2">🔗</span> Share Program
        </button>

        <section class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
          <h3 class="font-bold text-lg border-b-2 border-[#1e4b6e] pb-2 mb-6 w-fit">Order Summary</h3>
          
          <dl class="space-y-4 text-xs font-medium text-gray-600">
            <div class="flex justify-between">
              <dt>Haarlem Jazz (0 Tickets)</dt>
              <dd class="font-bold text-black">€00.00</dd>
            </div>
            <div class="flex justify-between">
              <dt>Yummy! (0 Reservations)</dt>
              <dd class="font-bold text-black">€00.00</dd>
            </div>
            <div class="flex justify-between">
              <dt>Dance! (0 Day Pass)</dt>
              <dd class="font-bold text-black">€00.00</dd>
            </div>
            <hr class="border-gray-100 my-4">
            <div class="flex justify-between">
              <dt>Subtotal</dt>
              <dd class="font-bold text-black">€00.00</dd>
            </div>
            <div class="flex justify-between items-center">
              <dt class="flex items-center">Service Fee (2.5%) <span class="ml-1 text-blue-500 cursor-help">ⓘ</span></dt>
              <dd class="font-bold text-black">€00.00</dd>
            </div>
          </dl>

          <div class="mt-8 flex justify-between items-end">
            <span class="text-2xl font-bold text-gray-800">Total</span>
            <span class="text-3xl font-bold text-black">€00.00</span>
          </div>
        </section>
      </aside>
    </div>
  </div>
</main>