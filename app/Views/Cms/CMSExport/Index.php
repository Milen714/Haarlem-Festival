<?php

namespace App\Views\Cms\CMSExport;

?>

<section class="mx-auto max-w-7xl p-4 md:p-8">
    <!-- Header -->
    <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Export Orders
            </h1>
            <p class="text-gray-600">Download orders as CSV or Excel with custom columns</p>
        </div>
    </header>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
        <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
        <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- CSV Export -->
        <article class="bg-white border rounded-lg p-8 shadow-sm hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <span class="text-5xl mr-4">📄</span>
                <div>
                    <h2 class="text-2xl font-bold">CSV Export</h2>
                    <p class="text-sm text-gray-500">Comma-separated values format</p>
                </div>
            </div>
            <p class="text-gray-600 mb-6">Export orders as a CSV file compatible with spreadsheet applications</p>
            <form id="csvExportForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Select Columns to Export</label>
                    <div id="csvColumns" class="space-y-2 max-h-48 overflow-y-auto border rounded-lg p-3 bg-gray-50">
                        <div class="text-gray-500 text-sm">Loading columns...</div>
                    </div>
                </div>

                <div>
                    <label for="csvPaidAfter" class="block text-sm font-semibold text-gray-700 mb-2">
                        Filter by Paid Date (Optional)
                    </label>
                    <input type="date" id="csvPaidAfter" name="paidAfter"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="button" onclick="exportCSV()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    📥 Download as CSV
                </button>
            </form>
        </article>

        <!-- Excel Export -->
        <article class="bg-white border rounded-lg p-8 shadow-sm hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <span class="text-5xl mr-4">📊</span>
                <div>
                    <h2 class="text-2xl font-bold">Excel Export</h2>
                    <p class="text-sm text-gray-500">Formatted spreadsheet</p>
                </div>
            </div>
            <p class="text-gray-600 mb-6">Export orders as a formatted Excel file with styling and proper column widths
            </p>
            <form id="excelExportForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Select Columns to Export</label>
                    <div id="excelColumns" class="space-y-2 max-h-48 overflow-y-auto border rounded-lg p-3 bg-gray-50">
                        <div class="text-gray-500 text-sm">Loading columns...</div>
                    </div>
                </div>

                <div>
                    <label for="excelPaidAfter" class="block text-sm font-semibold text-gray-700 mb-2">
                        Filter by Paid Date (Optional)
                    </label>
                    <input type="date" id="excelPaidAfter" name="paidAfter"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <button type="button" onclick="exportExcel()"
                    class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    📥 Download as Excel
                </button>
            </form>
        </article>

    </div>
</section>

<script src="/Js/ShowError.js"></script>
<script>
// Load available columns on page load
document.addEventListener('DOMContentLoaded', function() {
    loadColumns();
});

// Fetch and display available columns
async function loadColumns() {
    try {
        const response = await fetch('/getOrderColumns', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();
        if (result.success && result.columns) {
            populateColumnCheckboxes(result.columns);
        } else {
            showError('Failed to load columns');
        }
    } catch (error) {
        console.error('Error loading columns:', error);
        showError('Error loading columns: ' + error.message);
    }
}

// Create checkbox for each column
function populateColumnCheckboxes(columns) {
    const csvContainer = document.getElementById('csvColumns');
    const excelContainer = document.getElementById('excelColumns');

    csvContainer.innerHTML = '';
    excelContainer.innerHTML = '';

    columns.forEach(column => {
        // CSV columns
        const csvCheckbox = document.createElement('label');
        csvCheckbox.className = 'flex items-center space-x-2 cursor-pointer p-1 hover:bg-white rounded';
        csvCheckbox.innerHTML = `
            <input type="checkbox" name="columns" value="${column}" class="csv-column" checked>
            <span class="text-sm text-gray-700">${column}</span>
        `;
        csvContainer.appendChild(csvCheckbox);

        // Excel columns
        const excelCheckbox = document.createElement('label');
        excelCheckbox.className = 'flex items-center space-x-2 cursor-pointer p-1 hover:bg-white rounded';
        excelCheckbox.innerHTML = `
            <input type="checkbox" name="columns" value="${column}" class="excel-column" checked>
            <span class="text-sm text-gray-700">${column}</span>
        `;
        excelContainer.appendChild(excelCheckbox);
    });
}

// Export as CSV
async function exportCSV() {
    const selectedColumns = Array.from(document.querySelectorAll('.csv-column:checked'))
        .map(checkbox => checkbox.value);
    const paidAfter = document.getElementById('csvPaidAfter').value || null;

    if (selectedColumns.length === 0) {
        showError('Please select at least one column');
        return;
    }

    try {
        const response = await fetch('/exportOrders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                columns: selectedColumns,
                paidAfter: paidAfter
            })
        });

        if (response.ok && response.headers.get('Content-Type')?.includes('text/csv')) {
            // Trigger download
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'orders_export_' + new Date().getTime() + '.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } else {
            const data = await response.json();
            showError(data.message || 'Failed to export CSV');
        }
    } catch (error) {
        console.error('Error exporting CSV:', error);
        showError('Error exporting CSV: ' + error.message);
    }
}

// Export as Excel
async function exportExcel() {
    const selectedColumns = Array.from(document.querySelectorAll('.excel-column:checked'))
        .map(checkbox => checkbox.value);
    const paidAfter = document.getElementById('excelPaidAfter').value || null;

    if (selectedColumns.length === 0) {
        showError('Please select at least one column');
        return;
    }

    try {
        const response = await fetch('/exportOrdersExcel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                columns: selectedColumns,
                paidAfter: paidAfter
            })
        });

        if (response.ok && (response.headers.get('Content-Type')?.includes('application/vnd.ms-excel') ||
                response.headers.get('Content-Type')?.includes('application/octet-stream'))) {
            // Trigger download
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'orders_export_' + new Date().getTime() + '.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } else {
            const data = await response.json();
            showError(data.message || 'Failed to export Excel');
        }
    } catch (error) {
        console.error('Error exporting Excel:', error);
        showError('Error exporting Excel: ' + error.message);
    }
}
</script>