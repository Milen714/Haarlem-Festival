document.addEventListener('DOMContentLoaded', () => {
    const form          = document.getElementById('profile-form');
    const inputs        = document.querySelectorAll('.profile-input');
    const btnEdit       = document.getElementById('btn-edit');
    const btnCancel     = document.getElementById('btn-cancel');
    const actionButtons = document.getElementById('action-buttons');

    btnEdit.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = false);
        btnEdit.classList.add('hidden');
        actionButtons.classList.remove('hidden');
        actionButtons.classList.add('flex');
        inputs[0].focus();
    });

    btnCancel.addEventListener('click', () => {
        form.reset();
        inputs.forEach(input => input.disabled = true);
        btnEdit.classList.remove('hidden');
        actionButtons.classList.remove('flex');
        actionButtons.classList.add('hidden');
    });
});