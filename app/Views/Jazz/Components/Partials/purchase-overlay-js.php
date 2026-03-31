<?php

namespace App\Views\Jazz\Components\Partials;

use App\Models\Enums\TicketSchemeEnum;

/**
 * Purchase Overlay — JavaScript controller.
 * Included once per page. Uses event delegation — no global onclick handlers needed.
 */
?>
<script>
(function () {
    'use strict';

    /* ── Constants ── */
    const QUANTITY_MIN = 1;
    const QUANTITY_MAX = 10;

    /** Enum values that represent pass-type tickets (not single-show). */
    const PASS_SCHEME_ENUMS = [
        '<?= TicketSchemeEnum::JAZZ_DAY_PASS->value ?>',
    ];

    /* ── In-memory cart state (scoped to this page load) ── */
    let cartTotal = 0;
    let cartItemCount = 0;

    /* ── Current overlay context ── */
    let currentTicket = {
        ticketTypeId: null,
        scheduleId: '0',
        artistName: '',
        date: '',
        startTime: '',
        endTime: '',
        venueName: '',
        pricePerTicket: 0,
        quantity: 1,
    };

    /* ── Helper: format currency ── */
    function formatPrice(amount) {
        return amount === 0 ? 'Free' : '€' + amount.toFixed(2);
    }

    /* ── Helper: build display string for date + time ── */
    function buildDateTimeLabel(date, startTime, endTime) {
        const timePart = startTime ? ' • ' + startTime + (endTime ? ' - ' + endTime : '') : '';
        return date ? date + timePart : startTime + (endTime ? ' - ' + endTime : '');
    }

    /* ── Helper: sync qty stepper disabled state ── */
    function syncQuantityButtons() {
        const decreaseBtn = document.querySelector('[data-action="qty-decrease"]');
        const increaseBtn = document.querySelector('[data-action="qty-increase"]');
        if (decreaseBtn) decreaseBtn.disabled = currentTicket.quantity <= QUANTITY_MIN;
        if (increaseBtn) increaseBtn.disabled = currentTicket.quantity >= QUANTITY_MAX;
    }

    /* ── Open overlay ── */
    function openPurchaseOverlay(triggerButton) {
        currentTicket = {
            ticketTypeId : triggerButton.dataset.ticketTypeId  || null,
            scheduleId   : triggerButton.dataset.scheduleId    || '0',
            artistName   : triggerButton.dataset.artist        || 'Ticket',
            date         : triggerButton.dataset.date          || '',
            startTime    : triggerButton.dataset.start         || '',
            endTime      : triggerButton.dataset.end           || '',
            venueName    : triggerButton.dataset.venue         || '',
            pricePerTicket: parseFloat(triggerButton.dataset.price) || 0,
            quantity     : 1,
        };

        const dateTimeLabel = buildDateTimeLabel(
            currentTicket.date,
            currentTicket.startTime,
            currentTicket.endTime
        );

        document.getElementById('overlay-state-select').classList.remove('hidden');
        document.getElementById('overlay-state-confirm').classList.add('hidden');

        document.getElementById('select-artist-name').textContent  = currentTicket.artistName;
        document.getElementById('select-datetime').textContent      = dateTimeLabel;
        document.getElementById('select-venue-name').textContent    = currentTicket.venueName;
        document.getElementById('select-price-per').textContent     = formatPrice(currentTicket.pricePerTicket);
        document.getElementById('ticket-qty').textContent           = '1';
        document.getElementById('select-line-total').textContent    = formatPrice(currentTicket.pricePerTicket);

        const errorEl = document.getElementById('overlay-add-error');
        if (errorEl) errorEl.classList.add('hidden');

        syncQuantityButtons();
        document.getElementById('purchase-overlay').showModal();
    }

    /* ── Adjust quantity ── */
    function adjustQuantity(delta) {
        const next = Math.min(QUANTITY_MAX, Math.max(QUANTITY_MIN, currentTicket.quantity + delta));
        if (next === currentTicket.quantity) return;
        currentTicket.quantity = next;

        document.getElementById('ticket-qty').textContent        = next;
        document.getElementById('select-line-total').textContent =
            formatPrice(currentTicket.pricePerTicket * next);

        syncQuantityButtons();
    }

    /* ── Add to cart via API ── */
    function addTicketTypeToCart(ticketTypeId) {
        return fetch('/addToCart', {
            method  : 'POST',
            headers : { 'Content-Type': 'application/json' },
            body    : JSON.stringify({
                ticketTypeId: Number(ticketTypeId),
                quantity    : currentTicket.quantity,
            }),
        }).then(function (response) { return response.json(); });
    }

    /* ── Resolve ticketTypeId, then add to cart ── */
    function resolveTicketTypeAndAdd() {
        if (currentTicket.ticketTypeId) {
            return addTicketTypeToCart(currentTicket.ticketTypeId);
        }

        return fetch('/jazz-get-tickettypes?schedule_id=' + encodeURIComponent(currentTicket.scheduleId))
            .then(function (response) { return response.json(); })
            .then(function (typesData) {
                if (!typesData.success || !Array.isArray(typesData.data) || typesData.data.length === 0) {
                    throw new Error(typesData.message || 'No ticket types found for this performance.');
                }

                /* Prefer a single-show ticket over a pass type */
                const singleShowTicket = typesData.data.find(function (ticketType) {
                    const schemeEnum = ticketType.ticket_scheme && ticketType.ticket_scheme.scheme_enum;
                    return !PASS_SCHEME_ENUMS.includes(schemeEnum);
                }) || typesData.data[0];

                return addTicketTypeToCart(singleShowTicket.ticket_type_id);
            });
    }

    /* ── Confirm and submit purchase ── */
    function confirmPurchase() {
        const addBtn  = document.getElementById('overlay-add-btn');
        const errorEl = document.getElementById('overlay-add-error');

        addBtn.disabled    = true;
        addBtn.textContent = 'Adding…';
        if (errorEl) errorEl.classList.add('hidden');

        resolveTicketTypeAndAdd()
            .then(function (responseData) {
                if (!responseData.success) {
                    throw new Error(responseData.message || 'Could not add tickets. Please try again.');
                }

                const lineTotal   = currentTicket.pricePerTicket * currentTicket.quantity;
                const cart        = responseData.cart || {};
                cartTotal         = typeof cart.total === 'number' ? cart.total : (cartTotal + lineTotal);
                cartItemCount     = Array.isArray(cart.orderItems) ? cart.orderItems.length : (cartItemCount + 1);

                const ticketWord      = currentTicket.quantity === 1 ? 'ticket' : 'tickets';
                const dateTimeLabel   = buildDateTimeLabel(
                    currentTicket.date,
                    currentTicket.startTime,
                    currentTicket.endTime
                );
                const ticketLabel = currentTicket.quantity + ' ticket' +
                    (currentTicket.quantity > 1 ? 's' : '') +
                    (currentTicket.pricePerTicket === 0 ? ' — Free' : ' × €' + currentTicket.pricePerTicket.toFixed(2));

                document.getElementById('overlay-state-select').classList.add('hidden');
                document.getElementById('overlay-state-confirm').classList.remove('hidden');

                document.getElementById('confirm-subtitle').textContent      = currentTicket.quantity + ' ' + ticketWord + ' added to your personal program';
                document.getElementById('confirm-artist-name').textContent   = currentTicket.artistName;
                document.getElementById('confirm-datetime').textContent      = dateTimeLabel;
                document.getElementById('confirm-venue-name').textContent    = currentTicket.venueName;
                document.getElementById('confirm-ticket-label').textContent  = ticketLabel;
                document.getElementById('confirm-line-total').textContent    = formatPrice(lineTotal);
                document.getElementById('confirm-cart-total').textContent    = '€' + cartTotal.toFixed(2);
                document.getElementById('confirm-cart-items').textContent    =
                    cartItemCount + ' item' + (cartItemCount !== 1 ? 's' : '') + ' in your program';

                if (typeof updateCartCount === 'function') updateCartCount();
            })
            .catch(function (error) {
                if (errorEl) {
                    errorEl.textContent = (error && error.message) || 'Network error. Please check your connection and try again.';
                    errorEl.classList.remove('hidden');
                }
            })
            .finally(function () {
                addBtn.disabled    = false;
                addBtn.textContent = 'Add to Program';
            });
    }

    /* ── Close overlay ── */
    function closePurchaseOverlay() {
        document.getElementById('purchase-overlay').close();
    }

    /* ── Event delegation: single listener handles all overlay interactions ── */
    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.getElementById('purchase-overlay');
        if (!overlay) return;

        /* Close on backdrop click (ESC is handled natively by <dialog>) */
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) closePurchaseOverlay();
        });

        document.addEventListener('click', function (event) {
            const target = event.target.closest('[data-action]');
            if (!target) return;

            switch (target.dataset.action) {
                case 'buy-ticket':
                    openPurchaseOverlay(target);
                    break;
                case 'close-overlay':
                    closePurchaseOverlay();
                    break;
                case 'confirm-purchase':
                    confirmPurchase();
                    break;
                case 'qty-decrease':
                    adjustQuantity(-1);
                    break;
                case 'qty-increase':
                    adjustQuantity(1);
                    break;
            }
        });
    });

}());
</script>
