<?php namespace App\Views\Jazz\Components; ?>
<script>
/* ─────────────────────────────────────────────────────────
   Jazz ticket modal — shared across schedule, artist-detail
   and jazz home pages.
   ───────────────────────────────────────────────────────── */

(function () {
    /** In-memory cart counters (per page load). */
    window._cartTotal = window._cartTotal || 0;
    window._cartItems = window._cartItems || 0;

    /** Current modal context. */
    window._modal = window._modal || {};

    const QTY_MIN = 1;
    const QTY_MAX = 10;

    /**
     * Called when the user clicks any "Buy" button.
     * Opens the modal in quantity-selection state.
     *
     * Required data-* attributes on the button:
     *   data-artist, data-date, data-start, data-venue, data-price
     * Optional:
     *   data-end, data-schedule-id
     *
     * @param {HTMLButtonElement} btn
     */
    window.buyTicket = function (btn) {
        window._modal = {
            scheduleId: btn.dataset.scheduleId || '0',
            artist:     btn.dataset.artist     || 'Ticket',
            date:       btn.dataset.date       || '',
            start:      btn.dataset.start      || '',
            end:        btn.dataset.end ? ' - ' + btn.dataset.end : '',
            venue:      btn.dataset.venue      || '',
            price:      parseInt(btn.dataset.price, 10) || 0,
            qty:        1,
        };

        // Reset to selection state
        document.getElementById('modal-state-select').classList.remove('hidden');
        document.getElementById('modal-state-confirm').classList.add('hidden');

        const m = window._modal;
        document.getElementById('select-artist-name').textContent = m.artist;
        document.getElementById('select-datetime').textContent    = m.date
            ? m.date + (m.start ? ' • ' + m.start + m.end : '')
            : (m.start ? m.start + m.end : '');
        document.getElementById('select-venue-name').textContent  = m.venue;
        document.getElementById('select-price-per').textContent   = '€' + m.price;
        document.getElementById('ticket-qty').textContent         = '1';
        document.getElementById('select-line-total').textContent  = '€' + m.price.toFixed(2);

        _syncQtyButtons();
        document.getElementById('ticket-modal').showModal();
    };

    /**
     * Increase or decrease quantity by `delta`.
     * @param {number} delta  +1 or -1
     */
    window.changeQty = function (delta) {
        const next = Math.min(QTY_MAX, Math.max(QTY_MIN, window._modal.qty + delta));
        if (next === window._modal.qty) return;
        window._modal.qty = next;

        document.getElementById('ticket-qty').textContent        = next;
        document.getElementById('select-line-total').textContent =
            '€' + (window._modal.price * next).toFixed(2);
        _syncQtyButtons();
    };

    /** Disable − / + at their limits. */
    function _syncQtyButtons() {
        document.getElementById('qty-decrease').disabled = (window._modal.qty <= QTY_MIN);
        document.getElementById('qty-increase').disabled = (window._modal.qty >= QTY_MAX);
    }

    /**
     * Called by "Add to Program" — disables the button, fires AJAX,
     * then switches to the confirmation state on success or shows an inline error.
     */
    window.confirmTicketAdd = function () {
        const { scheduleId, artist, date, start, end, venue, qty } = window._modal;

        const addBtn     = document.getElementById('modal-add-btn');
        const errorEl    = document.getElementById('modal-add-error');

        // Disable button to prevent double-submit
        addBtn.disabled    = true;
        addBtn.textContent = 'Adding…';
        if (errorEl) errorEl.classList.add('hidden');

        fetch('/cart/add', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body:    JSON.stringify({ schedule_id: scheduleId, quantity: qty }),
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.success) {
                // Show the server error inline, re-enable the button
                if (errorEl) {
                    errorEl.textContent = data.message || 'Could not add tickets. Please try again.';
                    errorEl.classList.remove('hidden');
                }
                addBtn.disabled    = false;
                addBtn.textContent = 'Add to Program';
                return;
            }

            // Use real values returned from the server
            const price     = data.unitPrice  || window._modal.price;
            const lineTotal = price * qty;

            window._cartTotal = data.cartTotal || (window._cartTotal + lineTotal);
            window._cartItems = data.cartItems || (window._cartItems + 1);

            // Switch to confirmation state
            document.getElementById('modal-state-select').classList.add('hidden');
            document.getElementById('modal-state-confirm').classList.remove('hidden');

            const ticketWord = qty === 1 ? 'ticket' : 'tickets';
            document.getElementById('confirm-subtitle').textContent     = qty + ' ' + ticketWord + ' have been added to your personal program';
            document.getElementById('confirm-artist-name').textContent  = artist;
            document.getElementById('confirm-datetime').textContent     = date ? date + (start ? ' • ' + start + end : '') : (start ? start + end : '');
            document.getElementById('confirm-venue-name').textContent   = venue;
            document.getElementById('confirm-ticket-label').textContent = qty + ' ticket' + (qty > 1 ? 's' : '') + ' × €' + price.toFixed(2);
            document.getElementById('confirm-line-total').textContent   = '€' + lineTotal.toFixed(2);
            document.getElementById('confirm-cart-total').textContent   = '€' + window._cartTotal.toFixed(2);
            document.getElementById('confirm-cart-items').textContent   = window._cartItems + ' item' + (window._cartItems !== 1 ? 's' : '') + ' in your program';

            // Reset button for next use
            addBtn.disabled    = false;
            addBtn.textContent = 'Add to Program';
        })
        .catch(function () {
            if (errorEl) {
                errorEl.textContent = 'Network error. Please check your connection and try again.';
                errorEl.classList.remove('hidden');
            }
            addBtn.disabled    = false;
            addBtn.textContent = 'Add to Program';
        });
    };

    /** Close the dialog. */
    window.closeTicketModal = function () {
        document.getElementById('ticket-modal').close();
    };

    // Close on backdrop click; ESC is handled natively by <dialog>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('ticket-modal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) window.closeTicketModal();
            });
        }
    });
}());
</script>
