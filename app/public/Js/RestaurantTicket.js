// application state used to track the currently selected date, session, and ticket quantities
const state = {
    selectedDate: null,
    selectedScheduleId: null,
    tickets: {} // stores ticket quantity by ticket type id
}

// helper function to update the text inside a DOM element by ID
function setElementText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('reservation-modal');
    const confModal = document.getElementById('confirmation-modal');
    const openBtn = document.getElementById('open-model');
    const closeBtn = document.getElementById('close-model');
    const closeConfbtn = document.getElementById('close-confirmation');
    const continuebtn = document.getElementById('continue-shopping');

    // Prevent runtime errors when the modal markup is not present on the page
    if (!openBtn || !closeBtn || !modal) {
        console.error('Modal elements not found. Check your HTML IDs:', {
            modal: !!modal,
            openBtn: !!openBtn,
            closeBtn: !!closeBtn
        });
        return;
    }

    // open reservation modal when button is clicked
    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
    });

    // close reservation modal when close button is clicked
    closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.add('hidden');
    });

    // close confirmation modal when continue button is clicked
    if (continuebtn) {
        continuebtn.addEventListener('click', (e) => {
            e.preventDefault();
            confModal.classList.add('hidden');
        });
    }

    // close confirmation modal when the close icon/button is clicked
    if (closeConfbtn) {
        closeConfbtn.addEventListener('click', (e) => {
            e.preventDefault();
            confModal.classList.add('hidden');
        });
    }

    // allow clicking outside the modal content to close the reservation modal
    modal.addEventListener('click', (e) => {
        if (e.target.id === 'reservation-modal') {
            modal.classList.add('hidden');
        }
    });

    // allow clicking outside the modal content to close the confirmation modal
    confModal.addEventListener('click', (e) => {
        if (e.target.id === 'confirmation-modal') {
            confModal.classList.add('hidden');
        }
    });
});

document.querySelectorAll('.date-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // visually highlight the selected date button
        document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('bg-[#d4a356]', 'text-black'));
        btn.classList.add('bg-[#d4a356]', 'text-black');

        const selectedDate = btn.dataset.date;
        state.selectedDate = selectedDate;
        state.selectedDateLabel = btn.dataset.dateLabel; // store the readable label for confirmations
        console.log('Selected date: ', state.selectedDate);

        // show only sessions for the selected date and hide the rest
        document.querySelectorAll('.session-btn').forEach(session => {
            if (session.dataset.date === selectedDate) {
                state.selectedDate = selectedDate;
                session.classList.remove('hidden');
            } else {
                session.classList.add('hidden');
            }
        });

        // clear any previously selected session when a new date is chosen
        state.selectedScheduleId = null;
        document.querySelectorAll('.session-btn').forEach(b => b.classList.remove('bg-[#d4a356]', 'text-black'));
    });
});

document.querySelectorAll('.session-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // visually highlight the selected session button
        document.querySelectorAll('.session-btn').forEach(b => b.classList.remove('bg-[#d4a356]', 'text-black'));
        btn.classList.add('bg-[#d4a356]', 'text-black');

        const scheduleId = btn.dataset.scheduleId;
        state.selectedScheduleId = scheduleId;
        state.selectedTime = btn.dataset.time; // store the session time for confirmation display

        // display only tickets that belong to the selected session
        document.querySelectorAll('.ticket-item').forEach(ticket => {
            if (ticket.dataset.scheduleId === scheduleId) {
                ticket.classList.remove('hidden');
            } else {
                ticket.classList.add('hidden');
            }
        });

        // reset the ticket selection counts whenever session changes
        state.tickets = {};
        document.querySelectorAll('.quantity').forEach(q => q.textContent = '0');
        updateSummary();

        console.log('Selected session: ', state.selectedScheduleId);
    });
});

document.querySelectorAll('.increment').forEach(btn => {
    btn.addEventListener('click', () => {
        // do not allow incrementing if the ticket type is sold out
        const ticketCard = btn.closest('.ticket-item');
        if (ticketCard.dataset.soldOut === '1') {
            return;
        }

        const id = btn.dataset.ticketId;
        const element = document.querySelector(`.quantity[data-ticket-id="${id}"]`);

        // increment the ticket quantity in the state and update the displayed quantity
        let current = state.tickets[id] || 0;
        current++;
        state.tickets[id] = current;
        element.textContent = current;

        updateSummary();
    });
});

document.querySelectorAll('.decrement').forEach(btn => {
    btn.addEventListener('click', () => {
        // do not allow decrementing if the ticket type is sold out
        const ticketCard = btn.closest('.ticket-item');
        if (ticketCard.dataset.soldOut === '1') {
            return;
        }

        const id = btn.dataset.ticketId;
        // prevent decrementing below 0 by checking the current state before allowing the decrement
        const element = document.querySelector(`.quantity[data-ticket-id="${id}"]`);

        let current = state.tickets[id] || 0;
        current = Math.max(0, current - 1);
        state.tickets[id] = current;
        element.textContent = current;

        updateSummary();
    });
});

document.querySelector('.confirm-btn').addEventListener('click', async () => {
    // require a session to be selected before adding tickets to cart
    if (!state.selectedScheduleId) {
        alert('Please select a session!');
        return;
    }

    const selectedTickets = Object.entries(state.tickets).filter(([id, qty]) => qty > 0);

    // require at least one ticket quantity to proceed
    if (selectedTickets.length === 0) {
        alert('Please select at least one ticket!');
        return;
    }

    try {
        // send each selected ticket type and quantity to the cart endpoint
        for (const [ticketTypeId, quantity] of selectedTickets) {
            const payload = {
                ticketTypeId: Number(ticketTypeId),
                quantity: quantity
            };

            const response = await fetch('/addToCart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error('Could not add tickets to cart');
            }
        }

        // show confirmation and close the reservation modal after successful add-to-cart
        document.getElementById('confirmation-modal').classList.remove('hidden');
        document.getElementById('reservation-modal').classList.add('hidden');
        updateCartCount();

    } catch (error) {
        alert(error.message || 'Something went wrong.');
    }
});

const goToCartBtn = document.getElementById('go-to-cart');
if(goToCartBtn){
    goToCartBtn.addEventListener('click', () => {
        window.location.href = '/payment';
    })
}

// function that rebuilds the reservation summary in the sidebar or modal
function updateSummary() {
    let subtotal = 0;
    const summaryContainer = document.querySelector('#summary-container');
    let html = '';

    // iterate over all selected tickets and build the summary lines
    for (const id in state.tickets) {
        const qty = state.tickets[id];
        if (qty === 0) continue;

        const price = ticketPrices[id] || 0;
        const name = ticketNames[id];
        const total = qty * price;

        subtotal += total;
        // debug logs to verify correct ticket data is being accessed
        console.log('tickets: ', state.tickets);
        console.log('names: ', ticketNames);
        console.log('prices: ', ticketPrices);
        console.log('ID:', id);
        console.log('Price:', ticketPrices[id]);

        html += `
           <div class="flex justify-between">
            <dt>${qty} ${name} × €${price}</dt>
            <dd>€${total.toFixed(2)}</dd>
        </div>`;
    }

    // only charge the reservation fee when there are selected tickets
    const fee = subtotal > 0 ? reservationFee : 0;
    const finalTotal = subtotal + fee;

    html += `
    <div class="flex justify-between italic">
        <dt>Reservation Fee</dt>
        <dd>€${fee.toFixed(2)}</dd>
    </div>
    <div class="flex justify-between text-xl font-serif text-[#d4a356] border-t border-[#d4a356]/20 mt-2 pt-2">
        <dt>Total</dt>
        <dd>€${finalTotal.toFixed(2)}</dd>
    </div>`;

    summaryContainer.innerHTML = html;
}

// commented-out confirmation modal helper; keeps this code for reference
// function fillConfModal(){
//     // build the ticket summary text string
//     let ticketSummary = '';
//     let total = 0;

//     for (const id in state.tickets) {
//         const qty = state.tickets[id];
//         if (qty === 0) continue;

//         const name = ticketNames[id];
//         const price = ticketPrices[id];

//         ticketSummary += `${qty}x ${name}, `;
//         total += qty * price;
//     }

//     ticketSummary = ticketSummary.slice(0, -2); // remove the last comma
//     const finalTotal = total + (total > 0 ? reservationFee : 0);

//     // fill the confirmation modal with the selected date, time, tickets, and total
//     document.getElementById('confirm-day').textContent =
//         state.selectedDateLabel?.split(' ')[0] || '';

//     document.getElementById('confirm-date').textContent =
//         state.selectedDateLabel || '';

//     document.getElementById('confirm-details').innerHTML =
//         `${state.selectedDate} • ${state.selectedTime}`;

//     document.getElementById('confirm-tickets').textContent = ticketSummary;

//     document.getElementById('confirm-total').textContent =
//         `€ ${finalTotal.toFixed(2)}`;
// }