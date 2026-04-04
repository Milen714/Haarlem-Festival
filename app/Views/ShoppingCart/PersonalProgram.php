<?php
namespace App\Views\ShoppingCart;
use App\ViewModels\ShoppingCart\PaidTicketsViewModel;

/** @var PaidTicketsViewModel|null $viewModel */
?>

<header class="flex flex-col mx-auto w-full md:w-[85%]">
    <h1 id="header" class="my-6 text-5xl font-montserrat font-bold text-[var(--text-home-primary)]">
        <?php echo $viewModel->showMyTicketsSection ? htmlspecialchars('My Tickets') : htmlspecialchars('My Personal Program'); ?>
    </h1>
    <?php
        include __DIR__ . '/Components/ProgramAndTicketsNav.php';
        ?>
</header>
<section
    class="flex flex-col gap-2 mx-auto w-full md:w-[85%] bg-white p-6 rounded-lg rounded-tl-none shadow-sm border border-gray-100">
    <ul class="magicDayUl grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
        <?php foreach($availableDates ?? [] as $date):
            $d = new \DateTime($date);
            $isActive = $viewModel && $viewModel->selectedDate === $date;
        ?>
        <li>
            <a href="/personal-program?date=<?= htmlspecialchars($date) ?>&showMyTicketSection=false"
                class="schedule-filter-link <?= $isActive ? 'home_calendar_button_active' : 'home_calendar_button_inactive' ?>">
                <span><?= $d->format('l') ?></span><span><?= $d->format('j') ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php if ($viewModel): ?>
    <?php include __DIR__ . '/../Home/Components/Spinner.php'; ?>

    <section id="my-program-section" class="<?php echo $viewModel->showMyTicketsSection ? 'hidden' : ''; ?>">
        <?php include __DIR__ . '/Partials/MyProgramPartial.php'; ?>
    </section>


    <section id="my-tickets-section" class="<?php echo $viewModel->showMyTicketsSection ? '' : 'hidden'; ?>">
        <?php foreach($viewModel->orderItems as $item): ?>
        <?php include __DIR__ . '/Components/TicketWithQr.php'; ?>
        <?php endforeach; ?>
    </section>
    <?php else: ?>
    <p class="text-gray-500"><?= $error ?? 'No paid tickets found.' ?></p>
    <?php endif; ?>
</section>

<script src="/Js/PersonalProgram.js"></script>