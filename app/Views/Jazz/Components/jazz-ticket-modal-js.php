<?php

namespace App\Views\Jazz\Components; ?>
<script>
    /* ─────────────────────────────────────────────────────────
   Jazz ticket modal — shared across schedule, artist-detail
   and jazz home pages.
   ───────────────────────────────────────────────────────── */

    (function() {
        /** In-memory cart counters (per page load). */
        window._cartTotal = window._cartTotal || 0;
        window._cartItems = window._cartItems || 0;

        /** Current modal context. */
        window._modal = window._modal || {};

        const QTY_MIN = 1;
        const QTY_MAX = 10;


        window.buyTicket = function(btn) {
            window._modal = {
                ticketTypeId: btn.dataset.ticketTypeId || null,
                scheduleId: btn.dataset.scheduleId || '0',
                artist: btn.dataset.artist || 'Ticket',
                date: btn.dataset.date || '',
                start: btn.dataset.start || '',
                end: btn.dataset.end ? ' - ' + btn.dataset.end : '',
                venue: btn.dataset.venue || '',
                price: parseFloat(btn.dataset.price) || 0,
                qty: 1,
            };

            document.getElementById('modal-state-select').classList.remove('hidden');
            document.getElementById('modal-state-confirm').classList.add('hidden');

            const m = window._modal;
            document.getElementById('select-artist-name').textContent = m.artist;
            document.getElementById('select-datetime').textContent = m.date ?
                m.date + (m.start ? ' • ' + m.start + m.end : '') :
                (m.start ? m.start + m.end : '');
            document.getElementById('select-venue-name').textContent = m.venue;
            document.getElementById('select-price-per').textContent = m.price === 0 ? 'Free' : '€' + m.price;
            document.getElementById('ticket-qty').textContent = '1';
            document.getElementById('select-line-total').textContent = m.price === 0 ? 'Free' : '€' + m.price.toFixed(2);

            _syncQtyButtons();
            document.getElementById('ticket-modal').showModal();
        };

        window.changeQty = function(delta) {
            const next = Math.min(QTY_MAX, Math.max(QTY_MIN, window._modal.qty + delta));
            if (next === window._modal.qty) return;
            window._modal.qty = next;

            document.getElementById('ticket-qty').textContent = next;
            document.getElementById('select-line-total').textContent =
                window._modal.price === 0 ? 'Free' : '€' + (window._modal.price * next).toFixed(2);
            _syncQtyButtons();
        };

        function _syncQtyButtons() {
            document.getElementById('qty-decrease').disabled = (window._modal.qty <= QTY_MIN);
            document.getElementById('qty-increase').disabled = (window._modal.qty >= QTY_MAX);
        }
        window.confirmTicketAdd = function() {
            const {
                scheduleId,
                artist,
                date,
                start,
                end,
                venue,
                qty
            } = window._modal;

            const addBtn = document.getElementById('modal-add-btn');
            const errorEl = document.getElementById('modal-add-error');

            addBtn.disabled = true;
            addBtn.textContent = 'Adding…';
            if (errorEl) errorEl.classList.add('hidden');

            const addToCart = function(ticketTypeId) {
                return fetch('/addToCart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ticketTypeId: Number(ticketTypeId),
                        quantity: qty
                    }),
                }).then(function(res) {
                    return res.json();
                });
            };

            // If ticketTypeId is already known (e.g. Day Pass / Weekend Pass), skip the schedule lookup
            const cartPromise = window._modal.ticketTypeId ?
                addToCart(window._modal.ticketTypeId) :
                fetch('/jazz-get-tickettypes?schedule_id=' + encodeURIComponent(scheduleId))
                .then(function(res) {
                    return res.json();
                })
                .then(function(typesData) {
                    if (!typesData.success || !Array.isArray(typesData.data) || typesData.data.length === 0) {
                        throw new Error(typesData.message || 'No ticket types found for this performance.');
                    }
                    return addToCart(typesData.data[0].ticket_type_id);
                });

            cartPromise
                .then(function(data) {
                    if (!data.success) {
                        throw new Error(data.message || 'Could not add tickets. Please try again.');
                    }

                    const price = window._modal.price;
                    const lineTotal = price * qty;
                    const cart = data.cart || {};
                    const cartTotal = typeof cart.total === 'number' ? cart.total : (window._cartTotal +
                        lineTotal);
                    const cartItems = Array.isArray(cart.orderItems) ? cart.orderItems.length : (window
                        ._cartItems + 1);

                    window._cartTotal = cartTotal;
                    window._cartItems = cartItems;

                    // Switch to confirmation state
                    document.getElementById('modal-state-select').classList.add('hidden');
                    document.getElementById('modal-state-confirm').classList.remove('hidden');

                    const ticketWord = qty === 1 ? 'ticket' : 'tickets';
                    document.getElementById('confirm-subtitle').textContent = qty + ' ' + ticketWord +
                        ' have been added to your personal program';
                    document.getElementById('confirm-artist-name').textContent = artist;
                    document.getElementById('confirm-datetime').textContent = date ? date + (start ? ' • ' +
                        start + end : '') : (start ? start + end : '');
                    document.getElementById('confirm-venue-name').textContent = venue;
                    document.getElementById('confirm-ticket-label').textContent = qty + ' ticket' + (qty > 1 ?
                        's' : '') + (price === 0 ? ' — Free' : ' × €' + price.toFixed(2));
                    document.getElementById('confirm-line-total').textContent = price === 0 ? 'Free' : '€' + lineTotal.toFixed(2);
                    document.getElementById('confirm-cart-total').textContent = '€' + cartTotal.toFixed(2);
                    document.getElementById('confirm-cart-items').textContent = cartItems + ' item' + (
                        cartItems !== 1 ? 's' : '') + ' in your program';

                    if (typeof updateCartCount === 'function') updateCartCount();

                    addBtn.disabled = false;
                    addBtn.textContent = 'Add to Program';
                })
                .catch(function(err) {
                    if (errorEl) {
                        errorEl.textContent = (err && err.message) ? err.message :
                            'Network error. Please check your connection and try again.';
                        errorEl.classList.remove('hidden');
                    }
                    addBtn.disabled = false;
                    addBtn.textContent = 'Add to Program';
                });
        };

        /** Close the dialog. */
        window.closeTicketModal = function() {
            document.getElementById('ticket-modal').close();
        };

        // Close on backdrop click; ESC is handled natively by <dialog>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('ticket-modal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) window.closeTicketModal();
                });
            }
        });
    }());
</script>