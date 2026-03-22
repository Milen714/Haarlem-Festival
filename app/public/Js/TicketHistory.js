document.addEventListener('DOMContentLoaded', () => {
    // Referencias a los contenedores
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
                // Formateamos la fecha bonita (ej. Sat, 25 Jul)
                const dateObj = new Date(date);
                const niceDate = dateObj.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });

                datesContainer.innerHTML += `
                    <label class="cursor-pointer">
                        <input type="radio" name="date" value="${date}" class="peer sr-only" required>
                        <div class="tour-radio-btn">${niceDate}</div>
                    </label>
                `;
            }

            // Mostramos el contenedor de fechas con animación
            stepDate.classList.remove('hidden');
            setTimeout(() => stepDate.classList.remove('opacity-0'), 50);

            // VOLVEMOS A ASIGNAR LISTENERS A LAS FECHAS RECIÉN CREADAS
            attachDateListeners(selectedLang);
            updateOrderOverview(); // Tu función existente
        });
    });

    // 2. Función para escuchar cuando cambian la Fecha
   // 2. Función para escuchar cuando cambian la Fecha
    function attachDateListeners(selectedLang) {
        document.querySelectorAll('input[name="date"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const selectedDate = e.target.value;
                const availableTimes = tourOptionsTree[selectedLang][selectedDate]; // Ahora es un objeto con horas e IDs

                timesContainer.innerHTML = '';

                // Pintamos las horas leyendo las llaves del objeto
                Object.keys(availableTimes).forEach(time => {
                    const ticketIds = availableTimes[time]; // Extraemos los IDs
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
            normalTicketId: selectedTime.getAttribute('data-normal-id'),
            familyTicketId: selectedTime.getAttribute('data-family-id'),
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