const state = {
    selectedDate: null,
    selectedScheduleId: null,
    tickets: {} //for the quantity of tickets
}

document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('reservation-modal');
    const openBtn = document.getElementById('open-model');
    const closeBtn = document.getElementById('close-model');

    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target.id === 'reservation-modal') {
            modal.classList.add('hidden');
        }
    });

});

document.querySelectorAll('.date-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('bg-[#d4a356]', 'text-black'));
        btn.classList.add('bg-[#d4a356]', 'text-black');
        const selectedDate = btn.dataset.date;
        state.selectedDate = selectedDate;
        //see if its selected
        console.log('Selected date: ', state.selectedDate);

        document.querySelectorAll('.session-btn').forEach(session =>{
            if (session.dataset.date === selectedDate) {
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
        state.selectedScheduleId = btn.dataset.scheduleId;
        //see if its selected
        console.log('Selected session: ', state.selectedScheduleId);
    });
});


document.querySelectorAll('.increment').forEach(btn => {
    btn.addEventListener('click', () =>{
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
        const id = btn.dataset.ticketId;
        const element = document.querySelector(`.quantity[data-ticket-id="${id}"]`);
        
         let current = state.tickets[id] || 0;
        current = Math.max(0, current - 1);
        state.tickets[id] = current;
        element.textContent = current;

        updateSummary();
    })
});

document.querySelector('.confirm-btn').addEventListener('click', () => {
    const payload = {
        schedule_id: state.selectedScheduleId,
        tickets: state.tickets
    };

    console.log(payload);
    fetch('/reserve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        console.log('success:', data);
    });
});

function updateSummary(){
    let subtotal = 0;
    const summaryContainer = document.querySelector('#summary-container');
    let html = '';
    for (const id in state.tickets){
        const qty = state.tickets[id];
        if(qty === 0) continue;

        const price = ticketPrices[id];
        const name = ticketNames[id];
        const total = qty * price;

        subtotal += total;

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