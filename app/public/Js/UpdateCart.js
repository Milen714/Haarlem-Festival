
const deleteButtons = document.querySelectorAll('.delete-order-item');
const editButtons = document.querySelectorAll('.edit-order-item');
const overlayContainer = document.getElementById('overlay-container');
const overlay = document.getElementById('overlay');

deleteButtons.forEach(button => {
    button.addEventListener('click', async () => {
        const orderItemSessionId = button.getAttribute('data-orderItemSessionId');
        if (!orderItemSessionId) {
            console.error('Missing sessionOrderitem_id for delete button.');
            return;
        }
        await fetch('/deleteOrderItem', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    sessionOrderitem_id: orderItemSessionId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    console.error('Failed to delete item:', data.error);
                }
            })
            .catch(error => console.error('Error deleting item:', error));
    });
});

function createOrderItemRow(orderItem, sessionOrderItemId) {
    const ticketName = orderItem?.ticket_type?.ticket_scheme?.name ?? 'Unknown ticket';
    const quantity = Math.max(1, Number(orderItem?.quantity ?? 1) || 1);
    const resolvedSessionId = sessionOrderItemId ?? orderItem?.sessionOrderitem_id ?? null;

    const row = document.createElement('div');
    row.classList.add('order-item-row', 'w-full', 'max-w-md', 'mx-auto');
    row.innerHTML = `
       <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
           <h2 class="text-xl font-semibold text-gray-900">Edit Order Item</h2>
           <p class="mt-1 text-sm text-gray-600">${ticketName}</p>

           <div class="mt-5 rounded-lg bg-gray-50 p-3">
               <p class="text-xs uppercase tracking-wide text-gray-500">Quantity</p>
               <div class="mt-2 flex items-center gap-3">
                   <button
                       type="button"
                       class="qty-minus h-10 w-10 rounded-full border border-gray-300 bg-white text-2xl leading-none text-gray-700 hover:bg-gray-100"
                       aria-label="Decrease quantity"
                   >-</button>
                   <input
                       type="number"
                       id="edit-quantity"
                       class="h-10 w-20 rounded-md border border-gray-300 text-center text-lg font-medium"
                       value="${quantity}"
                       min="1"
                   >
                   <button
                       type="button"
                       class="qty-plus h-10 w-10 rounded-full border border-gray-300 bg-white text-2xl leading-none text-gray-700 hover:bg-gray-100"
                       aria-label="Increase quantity"
                   >+</button>
               </div>
           </div>

           <button
               id="save-changes"
               class="mt-5 w-full rounded-lg bg-orange-500 px-4 py-2 font-medium text-white hover:bg-orange-600"
           >Save Changes</button>
       </div>
    `;

    const quantityInput = row.querySelector('#edit-quantity');
    const minusButton = row.querySelector('.qty-minus');
    const plusButton = row.querySelector('.qty-plus');
    const saveChangesButton = row.querySelector('#save-changes');

    minusButton?.addEventListener('click', () => {
        const current = Math.max(1, Number(quantityInput?.value ?? 1) || 1);
        quantityInput.value = String(Math.max(1, current - 1));
    });

    plusButton?.addEventListener('click', () => {
        const current = Math.max(1, Number(quantityInput?.value ?? 1) || 1);
        quantityInput.value = String(current + 1);
    });

    quantityInput?.addEventListener('input', () => {
        const normalized = Math.max(1, Number(quantityInput.value) || 1);
        quantityInput.value = String(normalized);
    });

    saveChangesButton?.addEventListener('click', () => {
        const newQuantity = quantityInput ? Number(quantityInput.value) : null;

        if (newQuantity === null || Number.isNaN(newQuantity) || newQuantity < 1) {
            console.error('Invalid quantity:', quantityInput?.value);
            return;
        }

        if (!resolvedSessionId) {
            console.error('Missing sessionOrderItemId on save button.');
            return;
        }

        updateOrderItem(resolvedSessionId, newQuantity);
    });

    return row;
}

function openOverlay(content) {
    overlay.innerHTML = '';
    overlay.appendChild(content);
    overlayContainer.classList.remove('hidden');
    overlayContainer.addEventListener('click', closeOverlay);
}
//** Closes the Overlay */
function closeOverlay(event) {
    if (event.target === overlayContainer) {
        overlayContainer.classList.add('hidden');
    }
}

editButtons.forEach(button => {
    button.addEventListener('click', async () => {
        const orderItemSessionId = button.getAttribute('data-orderItemSessionId');
        if (!orderItemSessionId) {
            console.error('Missing sessionOrderitem_id for edit button.');
            return;
        }
        await fetch(`/getOrderItemData?sessionOrderitem_id=${orderItemSessionId}`)
            .then(async response => {
                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`HTTP ${response.status}: ${text}`);
                }
                return response.json();
            })
            .then(data => {
                const orderItem = data?.data?.orderItem ?? data?.orderItem ?? null;

                if (data.success && orderItem) {
                    // Handle the retrieved order item data, e.g., populate a modal for editing
                    const orderItemRow = createOrderItemRow(orderItem, orderItemSessionId);
                    openOverlay(orderItemRow);
                } else {
                    console.error('Failed to retrieve order item data:', data.message);
                }
            })
            .catch(error => console.error('Error retrieving order item data:', error));
    });
});

async function updateOrderItem(sessionOrderItemId, newQuantity) {
    return fetch('/updateOrderItem', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                sessionOrderitem_id: sessionOrderItemId,
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                console.error('Failed to update item:', data.error);
            }
        })
        .catch(error => console.error('Error updating item:', error));
}
