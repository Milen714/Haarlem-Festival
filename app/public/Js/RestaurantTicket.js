const state = {
    selectedDate: null,
    selectedScheduleId: null,
    tickets: {} //for the quantity of tickets
}

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

    // Add null checks to prevent errors if elements don't exist
    if (!openBtn || !closeBtn || !modal) {
        console.error('Modal elements not found. Check your HTML IDs:', {
            modal: !!modal,
            openBtn: !!openBtn,
            closeBtn: !!closeBtn
        });
        return;
    }
    //displays pop up and closes if button is hit and if outside the border is hit
    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.add('hidden');
    });

    if (continuebtn) {
        continuebtn.addEventListener('click', (e) => {
            e.preventDefault();
            confModal.classList.add('hidden');
        });
    }

    if (closeConfbtn) {
        closeConfbtn.addEventListener('click', (e) => {
            e.preventDefault();
            confModal.classList.add('hidden');
        });
    }

    modal.addEventListener('click', (e) => {
        if (e.target.id === 'reservation-modal') {
            modal.classList.add('hidden');
        } 

    });
    confModal.addEventListener('click', (e) => {
        if (e.target.id === 'confirmation-modal') {
            confModal.classList.add('hidden');
        }

    });
});

document.querySelectorAll('.date-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('bg-[#d4a356]', 'text-black'));
        btn.classList.add('bg-[#d4a356]', 'text-black');
        const selectedDate = btn.dataset.date;
        state.selectedDate = selectedDate;
        state.selectedDateLabel = btn.dataset.dateLabel; //store it
        //see if its selected
        console.log('Selected date: ', state.selectedDate);

        document.querySelectorAll('.session-btn').forEach(session =>{
            //if date is selected it only hows sessions connected to the date and hides the rest and vice versa
            if (session.dataset.date === selectedDate) {
                state.selectedDate = selectedDate;
                session.classList.remove('hidden');
            } else {
                session.classList.add('hidden');
            }
        });

        //reset selected sessions
        state.selectedScheduleId = null;
        document.querySelectorAll('.session-btn').forEach(b => b.classList.remove('bg-[#d4a356]', 'text-black'));
    });
});

document.querySelectorAll('.session-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.session-btn').forEach(b => b.classList.remove('bg-[#d4a356]', 'text-black'));
        btn.classList.add('bg-[#d4a356]', 'text-black');
        const scheduleId = btn.dataset.scheduleId;
        state.selectedScheduleId = scheduleId;
        state.selectedTime = btn.dataset.time; //store
        //this shows only matching tickets
        document.querySelectorAll('.ticket-item').forEach(ticket => {
            if (ticket.dataset.scheduleId === scheduleId) {
                ticket.classList.remove('hidden');
            } else{
                ticket.classList.add('hidden');
            }
        });

        state.tickets = {};
        document.querySelectorAll('.quantity').forEach(q => q.textContent = '0');
        updateSummary();

        //see if its selected
        console.log('Selected session: ', state.selectedScheduleId);
    });
});


document.querySelectorAll('.increment').forEach(btn => {
    btn.addEventListener('click', () =>{
        //checks if tickets are soldout and blocks it
        const ticketCard = btn.closest('.ticket-item');
        if (ticketCard.dataset.soldOut === '1') {
            return; //stops click if soldout
        }

        const id = btn.dataset.ticketId;
        const element = document.querySelector(`.quantity[data-ticket-id="${id}"]`);

        let current = state.tickets[id] || 0;
        current++;
        state.tickets[id] = current;
        element.textContent = current;

        updateSummary();
    })
});

document.querySelectorAll('.decrement').forEach(btn => {
    btn.addEventListener('click', () =>{

        //checks if tickets are soldout and blocks it
        const ticketCard = btn.closest('.ticket-item');
        if (ticketCard.dataset.soldOut === '1') {
            return; //stops click if soldout
        }

        const id = btn.dataset.ticketId;
        const element = document.querySelector(`.quantity[data-ticket-id="${id}"]`);
        
         let current = state.tickets[id] || 0;
        current = Math.max(0, current - 1);
        state.tickets[id] = current;
        element.textContent = current;

        updateSummary();
    })
});

document.querySelector('.confirm-btn').addEventListener('click', async () => {
    if (!state.selectedScheduleId) {
        alert('Please select a session!');
        return;
    }
    
    //converts my data into correct type for cart
   const selectedTickets = Object.entries(state.tickets).filter(([id, qty]) => qty > 0);


    if (selectedTickets.length === 0) {
        alert('Please select at least one ticket!');
        return;
    }
    
    try {
        //adds converted data into payload
       for(const [ticketTypeId, quantity] of selectedTickets){
            const payload = {
                ticketTypeId: Number(ticketTypeId),
                quantity: quantity
            };
            //sends it to the cart
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
        document.getElementById('confirmation-modal').classList.remove('hidden');
        document.getElementById('reservation-modal').classList.add('hidden');

    } catch (error) {
        alert(error.message || 'Something went wrong.');
    }
});

const goToCartBtn = document.getElementById('go-to-cart');
if(goToCartBtn){
    goToCartBtn.addEventListener('click', () => {
        window.location.href = '/checkout';
    })
}

//for calulating price of ticket
function updateSummary(){
    let subtotal = 0;
    const summaryContainer = document.querySelector('#summary-container');
    let html = '';
    for (const id in state.tickets){
        const qty = state.tickets[id];
        if(qty === 0) continue;

        const price = ticketPrices[id] || 0;
        const name = ticketNames[id];
        const total = qty * price;
        
        subtotal += total;
        console.log('tickets: ' , state.tickets);
        console.log('names: ' , ticketNames);
        console.log('prices: ' , ticketPrices);
        console.log('ID:', id);
        console.log('Price:', ticketPrices[id]);


        html += `
           <div class="flex justify-between">
            <dt>${qty} ${name} × €${price}</dt>
            <dd>€${total.toFixed(2)}</dd>
        </div>`; 
        
    }
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

// function fillConfModal(){
//     //build ticket summary 
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

//     ticketSummary = ticketSummary.slice(0, -2); //remove the last comma
//     const finalTotal = total + (total > 0 ? reservationFee : 0);

//     //fill the buttons with the used data
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