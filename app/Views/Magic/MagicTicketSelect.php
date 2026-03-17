<?php
namespace App\Views\Magic;

use App\ViewModels\Magic\MagicAccessibility;

/** @var MagicAccessibility $pageModel */
$heroSection = $pageModel->heroSection ?? null;

$calendarTitle = isset($calendarTitle) ? (string) $calendarTitle : 'July 2026';
$calendarDays = isset($calendarDays) && is_array($calendarDays) ? $calendarDays : [
	['weekday' => 'Mon', 'day' => '24', 'isSelected' => false],
	['weekday' => 'Tue', 'day' => '25', 'isSelected' => false],
	['weekday' => 'Wed', 'day' => '26', 'isSelected' => false],
	['weekday' => 'Thu', 'day' => '27', 'isSelected' => true],
	['weekday' => 'Fri', 'day' => '28', 'isSelected' => false],
	['weekday' => 'Sat', 'day' => '29', 'isSelected' => false],
	['weekday' => 'Sun', 'day' => '30', 'isSelected' => false],
];
$timeSlots = isset($timeSlots) && is_array($timeSlots) ? $timeSlots : [
	'11:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
	'10:00 AM',
];
$supportUrl = isset($supportUrl) ? (string) $supportUrl : '/events-magic-accessibility';
?>

<section class="flex flex-col gap-6 bg_colors_home text-white pt-4 bg-[var(--magic-bg-primary)] overflow-x-hidden">
    <section class="w-[90%] mx-auto">
        <?php
        if ($pageModel->heroSection): 
            include 'Components/MagicAltHero.php';
            ?>
        <?php endif ?>
    </section>

    <section class="w-[90%] mx-auto">
        <?php include 'Components/MagicNav.php'; ?>
    </section>

    <section class="w-[90%] mx-auto mb-10 magic-border py-4 px-3 md:py-6 md:px-5 bg-[var(--magic-bg-secondary-dark)]">
        <section class="grid grid-cols-1 xl:grid-cols-[2fr_1fr] gap-6">
            <section class="flex flex-col gap-6">
                <header>
                    <h2 class="font-courierprime text-xl md:text-2xl text-[var(--magic-gold-accent)]">Select a date and
                        time</h2>
                </header>

                <section
                    class="bg-[var(--magic-bg-primary)] rounded-md border border-[var(--magic-border-transperent-dark)] p-4 md:p-6">
                    <section class="flex items-center justify-between mb-4">
                        <button type="button" aria-label="Previous month"
                            class="font-robotomono text-xl text-[var(--magic-gold-accent)] hover:text-[var(--magic-bright-gold-accent)] transition-colors">
                            &lsaquo;
                        </button>
                        <h3 class="font-courierprime text-lg md:text-xl text-[var(--magic-gold-accent)]">
                            <?= htmlspecialchars($calendarTitle) ?></h3>
                        <button type="button" aria-label="Next month"
                            class="font-robotomono text-xl text-[var(--magic-gold-accent)] hover:text-[var(--magic-bright-gold-accent)] transition-colors">
                            &rsaquo;
                        </button>
                    </section>

                    <section class="grid grid-cols-7 gap-2 mb-2">
                        <?php foreach ($calendarDays as $calendarDay): ?>
                        <p class="text-center text-xs md:text-sm font-robotomono text-[#9FB0C8]">
                            <?= htmlspecialchars((string) ($calendarDay['weekday'] ?? '')) ?>
                        </p>
                        <?php endforeach; ?>
                    </section>

                    <section class="grid grid-cols-7 gap-2">
                        <?php foreach ($calendarDays as $calendarDay): ?>
                        <?php $isSelectedDay = !empty($calendarDay['isSelected']); ?>
                        <button type="button"
                            class="h-10 rounded-md border font-robotomono text-sm md:text-base transition-colors
									<?= $isSelectedDay
										? 'bg-[#202f56] border-[var(--magic-creme-gold-accent)] text-white'
										: 'bg-[#12284a] border-[var(--magic-border-transperent-dark)] text-[#d6dff0] hover:border-[var(--magic-creme-gold-accent)]' ?>">
                            <?= htmlspecialchars((string) ($calendarDay['day'] ?? '')) ?>
                        </button>
                        <?php endforeach; ?>
                    </section>
                </section>

                <section>
                    <h3 class="font-courierprime text-lg md:text-xl text-[var(--magic-gold-accent)] mb-3">Select a time
                        slot</h3>
                    <section class="bg-[var(--magic-bg-nav-muted)] rounded-md p-4 md:p-5">
                        <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                            <?php foreach ($timeSlots as $index => $slot): ?>
                            <?php
								$timeLabel = is_array($slot) ? (string) ($slot['label'] ?? '') : (string) $slot;
								$isSelectedTime = is_array($slot) ? !empty($slot['isSelected']) : ($index === 0);
								?>
                            <button type="button" data-time-slot="<?= htmlspecialchars($timeLabel) ?>"
                                class="h-10 rounded-md border text-xs md:text-sm font-robotomono transition-colors
										<?= $isSelectedTime
											? 'bg-[#1b2949] border-[var(--magic-bright-gold-accent)] text-[var(--magic-bright-gold-accent)]'
											: 'bg-[#10233e] border-[var(--magic-creme-gold-accent)] text-[var(--magic-creme-gold-accent)] hover:bg-[#1a2f52]' ?>">
                                <?= htmlspecialchars($timeLabel) ?>
                            </button>
                            <?php endforeach; ?>
                        </section>
                    </section>
                </section>

                <button type="button"
                    class="w-full rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-secondary-dark)] py-3 font-courierprime text-[var(--magic-gold-accent)] text-lg hover:bg-[#11253f] transition-colors">
                    Next Step
                </button>
            </section>

            <aside class="flex flex-col gap-4">
                <section
                    class="rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-secondary-dark)] p-5">
                    <section class="mb-6">
                        <h3 class="font-courierprime text-2xl text-[var(--magic-gold-accent)] mb-3">Information</h3>
                        <p class="font-robotomono text-sm text-[#d5e0f0] leading-relaxed">
                            Book your tickets in advance, even if you have free admission (e.g. Netherlands Museum Pass,
                            Vriendenloterij VIP-Card). Purchased tickets will not be refunded. It is possible to change
                            the date and time of your tickets.
                        </p>
                    </section>

                    <section>
                        <h3 class="font-courierprime text-2xl text-[var(--magic-gold-accent)] mb-3">Frequently asked
                            questions</h3>
                        <p class="font-robotomono text-sm text-[#d5e0f0] leading-relaxed">
                            Refer to our
                            <a class="underline text-[var(--magic-blue-text)] hover:text-white"
                                href="/events-magic-accessibility">Frequently asked questions</a>
                            for quick answers about Teylers Museum, the ordering process, or changing your ticket. If
                            you
                            need further assistance, our helpdesk is available to help you.
                        </p>
                    </section>
                </section>

                <a class="rounded-md border border-[var(--magic-creme-gold-accent)] bg-[var(--magic-bg-secondary-dark)] py-3 px-4 text-center font-courierprime text-lg text-[var(--magic-gold-accent)] hover:bg-[#11253f] transition-colors"
                    href="<?= htmlspecialchars($supportUrl) ?>">
                    Contact Support
                </a>
            </aside>
        </section>
    </section>
</section>