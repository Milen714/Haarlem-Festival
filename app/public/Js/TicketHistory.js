document.addEventListener('DOMContentLoaded', function() {
    // Get references to the form elements
    const form = document.getElementById('ticket-form');
    const inputNormal = document.getElementById('qty-normal');
    const inputFamily = document.getElementById('qty-family');
    const spanTotal = document.getElementById('summary-total');

    // Get references for the order summary
    const summaryDetails = document.getElementById('summary-details');
    const summaryQtyText = document.getElementById('summary-qty-text');
    const summaryDateText = document.getElementById('summary-date-text');
    const summaryTimeText = document.getElementById('summary-time-text');
    const summaryLangText = document.getElementById('summary-lang-text');

    const stepDate = document.getElementById('step-date');
    const stepTime = document.getElementById('step-time');
    const datesContainer = document.getElementById('dates-container');
    const timesContainer = document.getElementById('times-container');

    // Listen for language selection changes
    document.querySelectorAll('input[name="language"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const selectedLang = e.target.value;
            const availableDates = tourOptionsTree[selectedLang];

            // Clear previous dates and times
            datesContainer.innerHTML = '';
            timesContainer.innerHTML = '';
            stepTime.classList.add('hidden', 'opacity-0');

            // Create a button for each available date
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

    // Listen for date selection changes
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

    // Update the right panel with the current selections and totals
    function updateOrderOverview() {
        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;

        let priceNormal = parseFloat(inputNormal.getAttribute('data-precio'));
        let priceFamily = parseFloat(inputFamily.getAttribute('data-precio'));

        let total = (qtyNormal * priceNormal) + (qtyFamily * priceFamily);
        spanTotal.innerText = total.toFixed(2);

        // Build the quantity summary text
        let qtyText = '0';
        if (qtyNormal > 0 && qtyFamily > 0) {
            qtyText = qtyNormal + 'x Normal, ' + qtyFamily + 'x Family';
        } else if (qtyNormal > 0) {
            qtyText = qtyNormal + 'x Normal';
        } else if (qtyFamily > 0) {
            qtyText = qtyFamily + 'x Family';
        }
        summaryQtyText.innerText = qtyText;

        let selectedDate = document.querySelector('input[name="date"]:checked');
        let selectedLang = document.querySelector('input[name="language"]:checked');
        let selectedTime = document.querySelector('input[name="time"]:checked');

        if (selectedDate) summaryDateText.innerText = selectedDate.nextElementSibling.innerText;
        if (selectedLang) summaryLangText.innerText = selectedLang.nextElementSibling.innerText;
        if (selectedTime) summaryTimeText.innerText = selectedTime.nextElementSibling.innerText;

        // Show the summary panel if anything has been selected
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

    // Send the form data to the server using fetch
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        let qtyNormal = parseInt(inputNormal.value) || 0;
        let qtyFamily = parseInt(inputFamily.value) || 0;

        if (qtyNormal === 0 && qtyFamily === 0) {
            showError("Please select at least one ticket.");
            return;
        }

        let selectedDate = document.querySelector('input[name="date"]:checked');
        let selectedLanguage = document.querySelector('input[name="language"]:checked');
        let selectedTime = document.querySelector('input[name="time"]:checked');

        if (!selectedDate || !selectedLanguage || !selectedTime) {
            showError("Please select a date, language, and time.");
            return;
        }

        // Get the ticket IDs stored in the radio button's data attributes
        let normalId = selectedTime.getAttribute('data-normal-id');
        let familyId = selectedTime.getAttribute('data-family-id');

        const btnSubmit = document.getElementById('btn-submit');

        try {
            // Disable the button so the user cannot double-click
            btnSubmit.innerText = "Adding...";
            btnSubmit.disabled = true;

            // Add normal tickets to cart
            if (qtyNormal > 0 && normalId) {
                const responseNormal = await fetch('/addToCart', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ticketTypeId: Number(normalId), quantity: qtyNormal })
                });
                const resultNormal = await responseNormal.json();
                if (!responseNormal.ok || !resultNormal.success) throw new Error(resultNormal.message || 'Error adding normal tickets.');
            }

            // Add family tickets to cart
            if (qtyFamily > 0 && familyId) {
                const responseFamily = await fetch('/addToCart', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ticketTypeId: Number(familyId), quantity: qtyFamily })
                });
                const resultFamily = await responseFamily.json();
                if (!responseFamily.ok || !resultFamily.success) throw new Error(resultFamily.message || 'Error adding family tickets.');
            }

            // Show the success message
            btnSubmit.innerText = "Add to Cart";
            btnSubmit.disabled = false;
            const successMsg = document.getElementById('tour-cart-success');
            successMsg.classList.remove('hidden');
            successMsg.classList.add('flex');

        } catch (error) {
            // Show the error to the user and re-enable the button
            showError(error.message || 'Something went wrong. Please try again.');
            btnSubmit.innerText = "Add to Cart";
            btnSubmit.disabled = false;
        }
    });

    // Close the success modal when the user clicks "keep looking"
    const keepLooking = document.getElementById('modal-keep-looking');
    if (keepLooking) {
        keepLooking.addEventListener('click', () => {
            const successMsg = document.getElementById('tour-cart-success');
            successMsg.classList.add('hidden');
            successMsg.classList.remove('flex');
        });
    }
});
