function showError(message) {
    const errorContainer = document.getElementById('error-container');
    if (!errorContainer) return;
    errorContainer.innerHTML = `
        <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
            ${message}
        </div>
    `;
    clearTimeout(window._errorTimer);
    window._errorTimer = setTimeout(() => { errorContainer.innerHTML = ''; }, 5000);
}