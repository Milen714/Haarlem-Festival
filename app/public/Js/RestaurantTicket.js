


document.querySelectorAll('.increment').forEach(btn => [
    btn.addEventListener('click', () =>{
        const id = btn.dataset.ticketId;
        const element = document.querySelector(`.quantity[data-ticket-id="${id}"]`);
        element.textContent = parseInt(element.textContent) + 1;
    })
]);

document.querySelectorAll('.decrement').forEach(btn => [
    btn.addEventListener('click', () =>{
        const id = btn.dataset.ticketId;
        const element = document.querySelector(`.quantity[data-ticket-id="${id}"]`);
        const current = parseInt(element.textContent);
        if (current > 0) element.textContent = current - 1; 
    })
]);