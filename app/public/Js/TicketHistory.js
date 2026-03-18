document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('ticket-form');
    const inputNormal = document.getElementById('qty-normal');
    const inputFamily = document.getElementById('qty-family');
    const spanTotal = document.getElementById('summary-total');
    
    // Nuevos elementos para el resumen visual
    const summaryDetails = document.getElementById('summary-details');
    const summaryQtyText = document.getElementById('summary-qty-text');
    const summaryDateText = document.getElementById('summary-date-text');
    const summaryTimeText = document.getElementById('summary-time-text');
    const summaryLangText = document.getElementById('summary-lang-text');

    // 1. Función Maestra para actualizar todo el panel derecho
    function updateOrderOverview() {
        // --- A. Calcular Cantidades y Precio ---
        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;
        
        let priceNormal = parseFloat(inputNormal.getAttribute('data-precio'));
        let priceFamily = parseFloat(inputFamily.getAttribute('data-precio'));
        
        let total = (qtyNormal * priceNormal) + (qtyFamily * priceFamily);
        spanTotal.innerText = total.toFixed(2);

        // Actualizar el texto de cantidad de tickets
        let qtyTextParts = [];
        if (qtyNormal > 0) qtyTextParts.push(`${qtyNormal}x Normal`);
        if (qtyFamily > 0) qtyTextParts.push(`${qtyFamily}x Family`);
        summaryQtyText.innerText = qtyTextParts.length > 0 ? qtyTextParts.join(', ') : '0';

        // --- B. Obtener selecciones de los Radio Buttons ---
        let selectedDate = document.querySelector('input[name="date"]:checked');
        let selectedLang = document.querySelector('input[name="language"]:checked');
        let selectedTime = document.querySelector('input[name="time"]:checked');

        // Para mostrar la fecha bonita, leemos el texto del <div> hermano
        if (selectedDate) summaryDateText.innerText = selectedDate.nextElementSibling.innerText;
        
        // Para el idioma, igual leemos el texto visible
        if (selectedLang) summaryLangText.innerText = selectedLang.nextElementSibling.innerText;
        
        // Para la hora, igual
        if (selectedTime) summaryTimeText.innerText = selectedTime.nextElementSibling.innerText;

        // --- C. Mostrar u ocultar el panel de detalles ---
        // Si hay al menos un ticket o alguna selección, mostramos el panel
        if (qtyNormal > 0 || qtyFamily > 0 || selectedDate || selectedLang || selectedTime) {
            summaryDetails.classList.remove('hidden');
        } else {
            summaryDetails.classList.add('hidden');
        }
    }

    // 2. Asignar Event Listeners (Escuchadores)
    // Escuchar inputs numéricos
    inputNormal.addEventListener('input', updateOrderOverview);
    inputFamily.addEventListener('input', updateOrderOverview);

    // Escuchar los radio buttons (usamos change en el form para capturar todos los radios)
    form.addEventListener('change', (e) => {
        if(e.target.type === 'radio') {
            updateOrderOverview();
        }
    });

    // 3. Enviar por AJAX (Fetch) cuando se hace submit en el form
    form.addEventListener('submit', (e) => {
        e.preventDefault(); 

        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;

        // Validaciones antes de enviar
        if (qtyNormal === 0 && qtyFamily === 0) {
            alert("Please select at least one ticket.");
            return;
        }

        let selectedDate = document.querySelector('input[name="date"]:checked');
        let selectedLanguage = document.querySelector('input[name="language"]:checked');
        let selectedTime = document.querySelector('input[name="time"]:checked');

        if (!selectedDate || !selectedLanguage || !selectedTime) {
            alert("Please select a date, language, and time.");
            return;
        }

        // Empaquetar todo para el Backend (Mandamos los 'value' reales, no los textos bonitos)
        let backendData = {
            date: selectedDate.value,
            language: selectedLanguage.value,
            time: selectedTime.value,
            qtyNormal: qtyNormal,
            qtyFamily: qtyFamily
        };

        // Enviar al servidor
        fetch('/history/add-to-cart', {
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