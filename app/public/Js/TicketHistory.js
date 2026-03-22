document.addEventListener('DOMContentLoaded', () => {
    // Referencias a los contenedores
    const form = document.getElementById('ticket-form');
    const inputNormal = document.getElementById('qty-normal');
    const inputFamily = document.getElementById('qty-family');
    const spanTotal = document.getElementById('summary-total');
    
    // Referencias para el resumen
    const summaryDetails = document.getElementById('summary-details');
    const summaryQtyText = document.getElementById('summary-qty-text');
    const summaryDateText = document.getElementById('summary-date-text');
    const summaryTimeText = document.getElementById('summary-time-text');
    const summaryLangText = document.getElementById('summary-lang-text');

    const stepDate = document.getElementById('step-date');
    const stepTime = document.getElementById('step-time');
    const datesContainer = document.getElementById('dates-container');
    const timesContainer = document.getElementById('times-container');

    // 1. Escuchar cuando cambian el Idioma
    document.querySelectorAll('input[name="language"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const selectedLang = e.target.value;
            const availableDates = tourOptionsTree[selectedLang]; // Buscamos en el árbol

            // Limpiamos fechas y horas anteriores
            datesContainer.innerHTML = '';
            timesContainer.innerHTML = '';
            stepTime.classList.add('hidden', 'opacity-0'); // Ocultamos horas

            // Pintamos los nuevos botones de fechas
            for (const date in availableDates) {
                const dateObj = new Date(date);
                const niceDate = dateObj.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });

                datesContainer.innerHTML += `
                    <label class="cursor-pointer">
                        <input type="radio" name="date" value="${date}" class="peer sr-only" required>
                        <div class="tour-radio-btn">${niceDate}</div>
                    </label>
                `;
            }

            stepDate.classList.remove('hidden');
            setTimeout(() => stepDate.classList.remove('opacity-0'), 50);

            attachDateListeners(selectedLang);
            updateOrderOverview(); 
        });
    });

   // 2. Función para escuchar cuando cambian la Fecha
    function attachDateListeners(selectedLang) {
        document.querySelectorAll('input[name="date"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const selectedDate = e.target.value;
                const availableTimes = tourOptionsTree[selectedLang][selectedDate]; 

                timesContainer.innerHTML = '';

                Object.keys(availableTimes).forEach(time => {
                    const ticketIds = availableTimes[time]; 
                    const niceTime = time.substring(0, 5); 
                    
                    timesContainer.innerHTML += `
                        <label class="cursor-pointer">
                            <input type="radio" name="time" value="${time}" 
                                   data-normal-id="${ticketIds.normalId || ''}" 
                                   data-family-id="${ticketIds.familyId || ''}" 
                                   class="peer sr-only" required>
                            <div class="tour-radio-btn">${niceTime}</div>
                        </label>
                    `;
                });

                stepTime.classList.remove('hidden');
                setTimeout(() => stepTime.classList.remove('opacity-0'), 50);

                document.querySelectorAll('input[name="time"]').forEach(t => t.addEventListener('change', updateOrderOverview));
                updateOrderOverview(); 
            });
        });
    }

    // 3. Función Maestra para actualizar todo el panel derecho
    function updateOrderOverview() {
        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;
        
        let priceNormal = parseFloat(inputNormal.getAttribute('data-precio'));
        let priceFamily = parseFloat(inputFamily.getAttribute('data-precio'));
        
        let total = (qtyNormal * priceNormal) + (qtyFamily * priceFamily);
        spanTotal.innerText = total.toFixed(2);

        let qtyTextParts = [];
        if (qtyNormal > 0) qtyTextParts.push(`${qtyNormal}x Normal`);
        if (qtyFamily > 0) qtyTextParts.push(`${qtyFamily}x Family`);
        summaryQtyText.innerText = qtyTextParts.length > 0 ? qtyTextParts.join(', ') : '0';

        let selectedDate = document.querySelector('input[name="date"]:checked');
        let selectedLang = document.querySelector('input[name="language"]:checked');
        let selectedTime = document.querySelector('input[name="time"]:checked');

        if (selectedDate) summaryDateText.innerText = selectedDate.nextElementSibling.innerText;
        if (selectedLang) summaryLangText.innerText = selectedLang.nextElementSibling.innerText;
        if (selectedTime) summaryTimeText.innerText = selectedTime.nextElementSibling.innerText;

        if (qtyNormal > 0 || qtyFamily > 0 || selectedDate || selectedLang || selectedTime) {
            summaryDetails.classList.remove('hidden');
        } else {
            summaryDetails.classList.add('hidden');
        }
    }

    inputNormal.addEventListener('input', updateOrderOverview);
    inputFamily.addEventListener('input', updateOrderOverview);

    form.addEventListener('change', (e) => {
        if(e.target.type === 'radio') updateOrderOverview();
    });

    // 4. Enviar por AJAX (Fetch) al OrderController
    // NOTA: Agregamos "async" aquí para poder usar "await" en los fetch
    form.addEventListener('submit', async (e) => {
        e.preventDefault(); 

        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;

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

        // Extraemos los IDs que guardamos inteligentemente en el HTML
        let normalId = selectedTime.getAttribute('data-normal-id');
        let familyId = selectedTime.getAttribute('data-family-id');

        const btnSubmit = document.getElementById('btn-submit');

        try {
            // Cambiamos el estado del botón para que el usuario no haga doble clic
            btnSubmit.innerText = "Adding...";
            btnSubmit.disabled = true;

            // A) Si compró tickets normales, mandamos un paquete al OrderController
            if (qtyNormal > 0 && normalId) {
                const responseNormal = await fetch('/addToCart', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ticketTypeId: Number(normalId), quantity: qtyNormal })
                });
                const resultNormal = await responseNormal.json();
                if (!responseNormal.ok || !resultNormal.success) throw new Error(resultNormal.message || 'Error adding normal tickets.');
            }

            // B) Si compró tickets familiares, mandamos otro paquete al OrderController
            if (qtyFamily > 0 && familyId) {
                const responseFamily = await fetch('/addToCart', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ticketTypeId: Number(familyId), quantity: qtyFamily })
                });
                const resultFamily = await responseFamily.json();
                if (!responseFamily.ok || !resultFamily.success) throw new Error(resultFamily.message || 'Error adding family tickets.');
            }

            btnSubmit.innerText = "Add to Cart";  // Restauramos el texto
            btnSubmit.disabled = false;
            // Si llegamos hasta aquí, todo fue un éxito
            alert('Tickets added to cart!');

        } catch (error) {
            // Si hubo un error en el servidor, se lo mostramos al usuario
            alert("Error: " + error.message);
            btnSubmit.innerText = "Add to Cart";
            btnSubmit.disabled = false;
        }
    });
});