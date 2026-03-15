document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('ticket-form');
    const inputNormal = document.getElementById('qty-normal');
    const inputFamily = document.getElementById('qty-family');
    const spanTotal = document.getElementById('summary-total');

    // 1. Función para calcular el precio total visualmente
    function updateTotalPrice() {
        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;

        let priceNormal = parseFloat(inputNormal.getAttribute('data-precio'));
        let priceFamily = parseFloat(inputFamily.getAttribute('data-precio'));

        let total = (qtyNormal * priceNormal) + (qtyFamily * priceFamily);
        spanTotal.innerText = total.toFixed(2);
    }

    // Escuchar si el usuario cambia la cantidad de tickets
    inputNormal.addEventListener('input', updateTotalPrice);
    inputFamily.addEventListener('input', updateTotalPrice);

    // 2. Enviar por AJAX (Fetch) cuando se hace submit en el form
    form.addEventListener('submit', (e) => {
        e.preventDefault(); // Evita que la página parpadee o se recargue

        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;

        // Validar que hayan comprado al menos 1 ticket
        if (qtyNormal === 0 && qtyFamily === 0) {
            alert("Please select at least one ticket.");
            return;
        }

        // Buscar qué radio buttons seleccionó el usuario usando CSS selectors
        let selectedDate = document.querySelector('input[name="date"]:checked').value;
        let selectedLanguage = document.querySelector('input[name="language"]:checked').value;

        // Empaquetar todo para el Backend
        let backendData = {
            date: selectedDate,
            language: selectedLanguage,
            qtyNormal: qtyNormal,
            qtyFamily: qtyFamily
        };

        // Enviar al servidor
        fetch('/api/tickets/comprar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(backendData)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Tickets added to cart!');
                window.location.href = '/cart';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});