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
            
            <div class="flex flex-wrap gap-4 mb-12" id="schedule-filters">
                <button 
                    data-date="all"
                    class="filter-btn px-6 py-2 rounded-md font-bold text-xs uppercase transition-all bg-[var(--dance-tag-color-1)] text-black">
                    All Days
                </button>

                <?php foreach (array_keys($vm->groupedSchedules) as $date): ?>
                    <button 
                        data-date="<?= $date ?>"
                        class="filter-btn px-6 py-2 rounded-md font-bold text-xs uppercase transition-all bg-[#1A1D29] text-gray-400 hover:bg-white hover:text-black">
                        <?= date('l d F', strtotime($date)) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div id="schedule-grid-container">
                <?php include 'Components/lineup-schedule-grid.php'; ?>
            </div>
        </section>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.filter-btn');
    const groups = document.querySelectorAll('.schedule-day-group');

    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedDate = this.getAttribute('data-date');

            // Update Button Visual Styles
            buttons.forEach(b => {
                b.classList.remove('bg-[var(--dance-tag-color-1)]', 'text-black');
                b.classList.add('bg-[#1A1D29]', 'text-gray-400');
            });
            this.classList.add('bg-[var(--dance-tag-color-1)]', 'text-black');
            this.classList.remove('bg-[#1A1D29]', 'text-gray-400');

            // Filter the Schedule Groups
            groups.forEach(group => {
                if (selectedDate === 'all') {
                    group.classList.remove('hidden');
                } else {
                    if (group.id === `group-${selectedDate}`) {
                        group.classList.remove('hidden');
                    } else {
                        group.classList.add('hidden');
                    }
                }
            });

            document.getElementById('schedule-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});
</script>