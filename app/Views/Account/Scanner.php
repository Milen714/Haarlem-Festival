<div class="flex items-center justify-center min-h-screen bg-gray-50">
    
    <div class="max-w-md w-full py-10 px-6 text-center bg-white shadow-xl rounded-2xl border border-gray-100">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Ticket Scanner</h1>
        
        <div id="reader" class="overflow-hidden rounded-xl border-4 border-dashed border-gray-200 bg-gray-50"></div>
        
        <div id="scan-result" class="mt-6 p-4 rounded-lg hidden transition-all duration-300">
            <p id="result-message" class="font-bold text-lg"></p>
            <p id="result-details" class="text-sm mt-1"></p>
        </div>

        <button onclick="location.reload()" class="mt-8 text-indigo-600 font-semibold hover:text-indigo-800 transition">
            <span class="flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset Scanner
            </span>
        </button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
function onScanSuccess(decodedText, decodedResult) {
    html5QrcodeScanner.clear();
    
    const resultDiv = document.getElementById('scan-result');
    const msgEl = document.getElementById('result-message');
    const detailEl = document.getElementById('result-details');

    fetch('/qr-code/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ hash: decodedText })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('hidden');
        
        if (data.success) {
            resultDiv.className = "mt-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200";
            msgEl.textContent = data.message;
            detailEl.textContent = data.description;
        } else {
            resultDiv.className = "mt-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200";
            msgEl.textContent = data.message;
            detailEl.textContent = "Check-in failed.";
        }
    })
    .catch(err => {
        console.error(err);
        alert("Server error. Check console.");
    });
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", 
    { fps: 10, qrbox: {width: 250, height: 250} },
    false
);
html5QrcodeScanner.render(onScanSuccess);
</script>