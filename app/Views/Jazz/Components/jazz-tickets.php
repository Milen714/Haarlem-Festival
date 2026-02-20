<?php
namespace App\Views\Jazz\Components;
?>

<section class="py-16 bg-white" aria-labelledby="tickets-heading">
    <article class="container mx-auto px-4">
        <h2 id="tickets-heading" class="text-4xl font-bold mb-12" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($ticketsSection->title ?? 'Tickets & Passes') ?>
        </h2>
        
        <ul class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <!-- Single Show Ticket (Lavender) -->
            <li class="jazz_event_border_lavender rounded-lg p-6 bg-white hover:shadow-lg transition-shadow">
                <article>
                    <header>
                        <h3 class="text-2xl font-bold mb-2">Single Show</h3>
                        <p class="text-gray-600 mb-4">Pay just one performance</p>
                    </header>
                    
                    <data value="10-15" class="block mb-6">
                        <span class="text-5xl font-bold">€10-15</span>
                        <span class="text-gray-600 block text-sm mt-1">per show</span>
                    </data>
                    
                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Choose your shows
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Main Hall: €15
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Second/Third Hall: €10
                        </li>
                    </ul>
                    
                    <footer>
                        <a href="/tickets" class="block w-full jazz_event_button_lavender text-center">
                            Buy Tickets
                        </a>
                    </footer>
                </article>
            </li>
            
            <!-- Day Pass (Pink) -->
            <li class="jazz_event_border_pink rounded-lg p-6 bg-white hover:shadow-lg transition-shadow relative">
                <article>
                    <mark class="absolute top-4 right-4 jazz_event_bg_pink text-white text-xs font-bold px-3 py-1 rounded-full">
                        BEST VALUE
                    </mark>
                    
                    <header>
                        <h3 class="text-2xl font-bold mb-2">Day Pass</h3>
                        <p class="text-gray-600 mb-4">All shows on one day</p>
                    </header>
                    
                    <data value="35" class="block mb-6">
                        <span class="text-5xl font-bold">€35</span>
                        <span class="text-gray-600 block text-sm mt-1">Thu + Fri + Sat</span>
                    </data>
                    
                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Unlimited access Thu/Fri/Sat
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All 3 halls per night
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All venues included
                        </li>
                    </ul>
                    
                    <footer>
                        <a href="/tickets" class="block w-full jazz_event_button_pink text-center">
                            Buy Day Pass
                        </a>
                    </footer>
                </article>
            </li>
            
            <!-- Weekend Pass (Yellow) -->
            <li class="jazz_event_border_yellow rounded-lg p-6 bg-white hover:shadow-lg transition-shadow relative">
                <article>
                    <mark class="absolute top-4 right-4 jazz_event_bg_yellow text-gray-800 text-xs font-bold px-3 py-1 rounded-full">
                        SAVE €25
                    </mark>
                    
                    <header>
                        <h3 class="text-2xl font-bold mb-2">Weekend Pass</h3>
                        <p class="text-gray-600 mb-4">Four full days of access</p>
                    </header>
                    
                    <data value="80" class="block mb-6">
                        <span class="text-5xl font-bold">€80</span>
                        <span class="text-gray-600 block text-sm mt-1">Thu + Fri + Sat</span>
                    </data>
                    
                    <ul class="space-y-2 mb-6" role="list">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All 19 indoor shows
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            All venues & stages
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Priority entry
                        </li>
                    </ul>
                    
                    <footer>
                        <a href="/tickets" class="block w-full jazz_event_button_yellow text-center">
                            Buy Weekend Pass
                        </a>
                    </footer>
                </article>
            </li>
        </ul>
        
        <!-- Footer Note -->
        <footer class="text-center mt-12">
            <aside class="inline-flex items-center text-gray-700 bg-gray-50 px-6 py-3 rounded-lg">
                <svg class="w-5 h-5 mr-2 text-[var(--pastel-coral)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <strong>Sunday at Grote Markt is FREE - no tickets needed!</strong>
            </aside>
            <address class="text-sm text-gray-600 mt-3 not-italic">
                Contact: <a href="mailto:tickets@haarlemfestival.nl" class="text-[var(--pastel-lavender)] hover:underline">tickets@haarlemfestival.nl</a>
            </address>
        </footer>
    </article>
</section>
