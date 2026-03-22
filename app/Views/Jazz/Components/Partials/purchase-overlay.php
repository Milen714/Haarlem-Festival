<?php

namespace App\Views\Jazz\Components\Partials;

/**
 * Purchase Overlay — modal dialog used on the Jazz index, schedule, and artist-detail pages.
 * Receives no PHP variables; all state is injected by purchase-overlay-js.php at runtime.
 */
?>

<dialog id="purchase-overlay"
        class="w-full max-w-lg rounded-2xl shadow-2xl p-0 bg-white border-0
               backdrop:bg-black/40 backdrop:backdrop-blur-sm"
        aria-labelledby="purchase-overlay-title"
        aria-modal="true">

    <article class="flex flex-col">

        <!-- Close button — visible in both states -->
        <button type="button"
                data-action="close-overlay"
                class="self-end mt-4 mr-4 text-gray-400 hover:text-gray-600 transition-colors
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--pastel-lavender)] rounded-full p-1"
                aria-label="Close dialog">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- ── State 1: Choose quantity ── -->
        <section id="overlay-state-select" aria-label="Select ticket quantity">

            <header class="flex flex-col items-center px-8 pb-2 text-center">
                <h2 id="purchase-overlay-title"
                    class="text-2xl font-bold text-gray-900"
                    style="font-family: 'Cormorant Garamond', serif;">
                    Buy Tickets
                </h2>
                <p class="text-gray-500 text-sm mt-1">Choose how many tickets you want</p>
            </header>

            <!-- Performance summary card -->
            <section class="mx-6 mt-4 mb-5 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden"
                     aria-label="Performance details">
                <div class="flex items-stretch">
                    <span class="w-1 flex-shrink-0 bg-[var(--pastel-lavender)] rounded-l-xl" aria-hidden="true"></span>
                    <div class="flex-1 px-4 py-4">
                        <h3 id="select-artist-name" class="font-bold text-gray-900 text-base leading-snug"></h3>
                        <p id="select-datetime"     class="text-sm text-gray-500 mt-1"></p>
                        <p class="flex items-center gap-1 text-sm text-gray-500 mt-0.5">
                            <span aria-hidden="true">📍</span>
                            <span id="select-venue-name"></span>
                        </p>
                        <p class="text-sm font-semibold text-gray-700 mt-2">
                            <span id="select-price-per"></span> per ticket
                        </p>
                    </div>
                </div>
            </section>

            <!-- Quantity stepper -->
            <section class="mx-6 mb-6" aria-label="Ticket quantity">
                <label for="ticket-qty" class="block text-sm font-semibold text-gray-700 mb-2">
                    Number of tickets
                </label>
                <div class="flex items-center gap-4">
                    <button type="button"
                            data-action="qty-decrease"
                            class="w-10 h-10 flex items-center justify-center rounded-full border-2
                                   border-[var(--pastel-lavender)] text-[var(--pastel-lavender)]
                                   text-xl font-bold hover:bg-[var(--pastel-lavender)]/10 transition-colors
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--pastel-lavender)]
                                   disabled:opacity-30 disabled:cursor-not-allowed"
                            aria-label="Decrease quantity">
                        &minus;
                    </button>

                    <output id="ticket-qty"
                            class="w-12 text-center text-2xl font-extrabold text-gray-900"
                            aria-live="polite"
                            aria-atomic="true">
                        1
                    </output>

                    <button type="button"
                            data-action="qty-increase"
                            class="w-10 h-10 flex items-center justify-center rounded-full border-2
                                   border-[var(--pastel-lavender)] text-[var(--pastel-lavender)]
                                   text-xl font-bold hover:bg-[var(--pastel-lavender)]/10 transition-colors
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--pastel-lavender)]"
                            aria-label="Increase quantity">
                        +
                    </button>

                    <p class="ml-auto text-right">
                        <span class="text-xs text-gray-400 block">Total</span>
                        <data id="select-line-total" class="font-bold text-gray-900 text-lg"></data>
                    </p>
                </div>
            </section>

            <!-- Inline server error (shown by JS when the cart API rejects the request) -->
            <p id="overlay-add-error"
               class="hidden mx-6 mb-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2"
               role="alert"></p>

            <footer class="flex flex-col sm:flex-row gap-3 px-6 pb-6">
                <button type="button"
                        data-action="close-overlay"
                        class="flex-1 py-3 px-5 rounded-full border-2 border-gray-300 text-gray-500
                               font-semibold text-sm hover:border-gray-400 transition-colors
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400">
                    Cancel
                </button>
                <button type="button"
                        id="overlay-add-btn"
                        data-action="confirm-purchase"
                        class="flex-1 py-3 px-5 rounded-full bg-[var(--pastel-lavender)] text-white
                               font-semibold text-sm hover:opacity-90 transition-opacity
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                               focus-visible:ring-[var(--pastel-lavender)]">
                    Add to Program
                </button>
            </footer>

        </section><!-- /state-select -->

        <!-- ── State 2: Confirmation ── -->
        <section id="overlay-state-confirm" class="hidden" aria-label="Purchase confirmation">

            <header class="flex flex-col items-center px-8 pb-4 text-center">
                <span class="flex items-center justify-center w-16 h-16 rounded-full bg-[var(--pastel-lavender)] mb-4"
                      aria-hidden="true">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                <h2 class="text-2xl font-bold text-gray-900"
                    style="font-family: 'Cormorant Garamond', serif;">
                    Added to Your Program!
                </h2>
                <p id="confirm-subtitle" class="text-gray-500 text-sm mt-1"></p>
            </header>

            <!-- Confirmed ticket details -->
            <section class="mx-6 mb-5 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden"
                     aria-label="Confirmed ticket details">
                <div class="flex items-stretch">
                    <span class="w-1 flex-shrink-0 bg-[var(--pastel-lavender)] rounded-l-xl" aria-hidden="true"></span>
                    <div class="flex-1 px-4 py-4">
                        <h3 id="confirm-artist-name" class="font-bold text-gray-900 text-base leading-snug"></h3>
                        <p id="confirm-datetime"     class="text-sm text-gray-500 mt-1"></p>
                        <p class="flex items-center gap-1 text-sm text-gray-500 mt-0.5">
                            <span aria-hidden="true">📍</span>
                            <span id="confirm-venue-name"></span>
                        </p>
                        <footer class="flex items-center justify-between mt-3">
                            <mark id="confirm-ticket-label"
                                  class="bg-[var(--pastel-lavender)]/15 text-[var(--pastel-lavender)]
                                         text-xs font-semibold px-3 py-1 rounded-full not-italic">
                            </mark>
                            <data id="confirm-line-total" class="font-bold text-gray-900 text-base"></data>
                        </footer>
                    </div>
                </div>
            </section>

            <!-- Cart summary -->
            <section class="mx-6 mb-6 flex items-start justify-between text-sm text-gray-600 border-t border-gray-100 pt-4"
                     aria-label="Current cart summary">
                <div>
                    <p class="font-medium text-gray-800">Current cart total:</p>
                    <p id="confirm-cart-items" class="text-gray-400 text-xs mt-0.5"></p>
                </div>
                <data id="confirm-cart-total" class="font-bold text-gray-900 text-base"></data>
            </section>

            <footer class="flex flex-col sm:flex-row gap-3 px-6 pb-6">
                <button type="button"
                        data-action="close-overlay"
                        class="flex-1 py-3 px-5 rounded-full border-2 border-[var(--pastel-lavender)]
                               text-[var(--pastel-lavender)] font-semibold text-sm
                               hover:bg-[var(--pastel-lavender)]/10 transition-colors
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--pastel-lavender)]">
                    Continue Shopping
                </button>
                <a href="/my-program"
                   class="flex-1 py-3 px-5 rounded-full bg-[var(--pastel-lavender)] text-white
                          font-semibold text-sm text-center hover:opacity-90 transition-opacity
                          focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                          focus-visible:ring-[var(--pastel-lavender)]">
                    View My Program &rarr;
                </a>
            </footer>

        </section><!-- /state-confirm -->

    </article>

</dialog>
