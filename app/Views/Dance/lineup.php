<?php
namespace App\Views\Dance;
/** @var \App\ViewModels\Dance\LineupViewModel $vm */
$items = $vm->breadcrumbs;
?>
<section class="dance-bg min-h-screen pt-20 text-white">
    <div class="max-w-7xl mx-auto px-6">
        <?php include __DIR__ . '/Components/breadcrumb.php'; ['items' => $items]; ?>
        <div class="mb-20">
            <?= $vm->pageData->sidebar_html ?>
        </div>

        <?php include 'Components/dance-headliners.php'; ?>

        <section class="mt-24 pb-32">
            <h2 class="inline-block text-white text-2xl font-bold uppercase tracking-widest border-b-2 border-[var(--dance-tag-color-1)] mb-12 pb-2">
                Program Schedule
            </h2>
            
            <div class="flex gap-4 mb-12">
                <button class="bg-[var(--dance-tag-color-1)] text-black px-6 py-2 rounded-md font-bold text-xs uppercase">Friday 24 July</button>
                <button class="bg-[#1A1D29] text-gray-400 px-6 py-2 rounded-md font-bold text-xs uppercase hover:bg-white hover:text-black transition">Saturday 25 July</button>
                <button class="bg-[#1A1D29] text-gray-400 px-6 py-2 rounded-md font-bold text-xs uppercase hover:bg-white hover:text-black transition">Sunday 26 July</button>
            </div>

            <?php include 'Components/lineup-schedule-grid.php'; ?>
        </section>
    </div>
</section>