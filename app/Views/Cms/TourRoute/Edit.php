<?php
/** @var App\CmsModels\PageSection|null $tourRoute */
/** @var string[] $stops */
?>

<div class="w-full">
    <header class="headers_home pb-5 flex justify-center items-center w-full">
        <article class="flex flex-row gap-4 w-full justify-center items-center relative">
            <a href="/cms" class="button_primary absolute left-0"><span>← </span>Back</a>
            <div class="text-center">
                <h1 class="text-4xl font-bold">Edit Tour Route</h1>
                <p class="text-sm text-gray-600 mt-1">Add, remove or reorder the stops shown in the tour timeline</p>
            </div>
        </article>
    </header>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="w-full bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
    <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="w-full bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
    <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<section class="flex flex-col gap-4 items-start p-3 mx-auto mb-10 max-w-xl">

    <form method="POST" action="/cms/history/tour-route/update"
          class="flex flex-col gap-4 w-full">

        <div class="border p-4 rounded-md bg-white inset-shadow-sm">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">Tour Stops</h2>
            <p class="text-sm text-gray-500 mb-4">Each row is one stop in the timeline. Use ↑↓ to reorder.</p>

            <div id="stops-list" class="flex flex-col gap-2 mb-4">
                <?php foreach ($stops as $i => $stop): ?>
                <div class="flex items-center gap-2 stop-row">
                    <span class="text-sm text-gray-400 w-5 stop-num"><?= $i + 1 ?>.</span>
                    <input type="text"
                           name="stops[]"
                           value="<?= htmlspecialchars($stop) ?>"
                           class="form_input flex-1"
                           placeholder="Stop name"
                           required>
                    <button type="button" onclick="moveStop(this,-1)" title="Move up"
                            class="text-gray-400 hover:text-gray-700 px-1 text-lg leading-none">↑</button>
                    <button type="button" onclick="moveStop(this,1)" title="Move down"
                            class="text-gray-400 hover:text-gray-700 px-1 text-lg leading-none">↓</button>
                    <button type="button" onclick="removeStop(this)" title="Remove"
                            class="text-red-400 hover:text-red-600 font-bold px-1 text-lg leading-none">✕</button>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="button" onclick="addStop()"
                    class="button_secondary text-sm">+ Add Stop</button>
        </div>

        <div> 
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">Tour Route Map</h2>
            <p class="text-sm text-gray-500 mb-4">The map is not auto-generated based, please use the map editor for updates:</p>
            <a href="https://mapforge.org/m/112be19b393a?join=true" target="_blank" class="text-blue-500 hover:text-blue-700">https://mapforge.org/m/112be19b393a?join=true</a>   
        </div>

        <div class="flex gap-4">
            <button type="submit" class="button_primary">💾 Save Changes</button>
            <a href="/cms" class="button_secondary">Cancel</a>
        </div>

    </form>
</section>

<script>
function addStop() {
    const list = document.getElementById('stops-list');
    const row  = document.createElement('div');
    row.className = 'flex items-center gap-2 stop-row';
    row.innerHTML = `
        <span class="text-sm text-gray-400 w-5 stop-num"></span>
        <input type="text" name="stops[]" class="form_input flex-1" placeholder="Stop name" required>
        <button type="button" onclick="moveStop(this,-1)" title="Move up"
                class="text-gray-400 hover:text-gray-700 px-1 text-lg leading-none">↑</button>
        <button type="button" onclick="moveStop(this,1)" title="Move down"
                class="text-gray-400 hover:text-gray-700 px-1 text-lg leading-none">↓</button>
        <button type="button" onclick="removeStop(this)" title="Remove"
                class="text-red-400 hover:text-red-600 font-bold px-1 text-lg leading-none">✕</button>`;
    list.appendChild(row);
    renumber();
    row.querySelector('input').focus();
}

function removeStop(btn) {
    btn.closest('.stop-row').remove();
    renumber();
}

function moveStop(btn, dir) {
    const row  = btn.closest('.stop-row');
    const list = document.getElementById('stops-list');
    if (dir === -1 && row.previousElementSibling) {
        list.insertBefore(row, row.previousElementSibling);
    } else if (dir === 1 && row.nextElementSibling) {
        list.insertBefore(row.nextElementSibling, row);
    }
    renumber();
}

function renumber() {
    document.querySelectorAll('#stops-list .stop-num').forEach((el, i) => {
        el.textContent = (i + 1) + '.';
    });
}
</script>
