/** * dance-modal.js 
 * Global handler for the Dance Ticket Modal
 */
let modalData = { qty: 1, price: 0, ticketTypeId: null };

function openDanceModal(btn) {
    const modal = document.getElementById('dance-ticket-modal');
    if (!modal) return;

    document.getElementById('modal-state-select').classList.remove('hidden');
    document.getElementById('modal-state-confirm').classList.add('hidden');
    
    modalData = {
        qty: 1,
        price: parseFloat(btn.dataset.price) || 0,
        ticketTypeId: btn.dataset.ticketTypeId,
        artist: btn.dataset.artist,
        date: btn.dataset.date,
        time: btn.dataset.time,
        venue: btn.dataset.venue
    };

    document.getElementById('select-artist-name').textContent = modalData.artist;
    document.getElementById('select-datetime').textContent = modalData.date + ' • ' + modalData.time;
    document.getElementById('select-venue-name').textContent = '📍 ' + modalData.venue;
    document.getElementById('select-price-per').textContent = '€' + modalData.price.toFixed(2);
    
    updateModalUI();
    modal.showModal();
}

function changeQty(delta) {
    const next = modalData.qty + delta;
    if (next >= 1 && next <= 10) {
        modalData.qty = next;
        updateModalUI();
    }
}

function updateModalUI() {
    document.getElementById('ticket-qty').textContent = modalData.qty;
    const total = modalData.qty * modalData.price;
    document.getElementById('select-line-total').textContent = '€' + total.toFixed(2);
    
    document.getElementById('qty-decrease').disabled = (modalData.qty <= 1);
    document.getElementById('qty-increase').disabled = (modalData.qty >= 10);
}

async function confirmTicketAdd() {
    const btn = document.getElementById('modal-add-btn');
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Adding...';

    try {
        const response = await fetch('/addToCart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ticketTypeId: parseInt(modalData.ticketTypeId),
                quantity: modalData.qty
            })
        });

        const result = await response.json();
        if (result.success) {
            showConfirmation(result.cart);
            updateCartCount();
        } else {
            alert(result.message || "Failed to add ticket.");
        }
    } catch (e) {
        alert("Server error. Please try again.");
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

function showConfirmation(cart) {
    document.getElementById('modal-state-select').classList.add('hidden');
    document.getElementById('modal-state-confirm').classList.remove('hidden');

    document.getElementById('confirm-subtitle').textContent = `${modalData.qty} ${modalData.qty > 1 ? 'tickets' : 'ticket'} added to your program`;
    document.getElementById('confirm-artist-name').textContent = modalData.artist;
    document.getElementById('confirm-datetime').textContent = modalData.date + ' • ' + modalData.time;
    
    const lineTotal = modalData.qty * modalData.price;
    document.getElementById('confirm-ticket-label').textContent = `${modalData.qty} ticket${modalData.qty > 1 ? 's' : ''} × €${modalData.price.toFixed(2)}`;
    document.getElementById('confirm-line-total').textContent = '€' + lineTotal.toFixed(2);

    if (cart) {
        document.getElementById('confirm-cart-total').textContent = '€' + parseFloat(cart.total || 0).toFixed(2);
        const itemCount = cart.orderItems ? cart.orderItems.length : 0;
        document.getElementById('confirm-cart-items').textContent = `${itemCount} items in your program`;
    }
}

function closeTicketModal() {
    document.getElementById('dance-ticket-modal').close();
}