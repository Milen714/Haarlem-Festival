<?php
namespace App\Views\Magic\Components;
$activeStyle = 'magic-nav-button-active';
?>

<nav class="magic-border magic-nav bg-[var(--magic-bg-nav-muted)]">
    <ul class="font-blackopsone text-xl flex flex-col md:flex-row gap-2 md:gap-3 justify-center items-center py-6 ">
        <li><a href="/events-magic"
                class="<?= htmlspecialchars($pageModel->page->slug === 'events-magic' ? $activeStyle : '') ?>">THE
                STORY</a></li>
        <li><a href="/events-magic-lorentz-show"
                class="<?= htmlspecialchars($pageModel->page->slug === 'events-magic-lorentz-show' ? $activeStyle : '') ?>">LORENTZ
                SHOW</a></li>
        <li><a href="/events-magic-tickets"
                class="<?= htmlspecialchars($pageModel->page->slug === 'events-magic-tickets' ? $activeStyle : '') ?>">MUSEUM
                TICKETS</a></li>
        <li><a href="/events-magic-accessibility"
                class="<?= htmlspecialchars($pageModel->page->slug === 'events-magic-accessibility' ? $activeStyle : '') ?> flex flex-row gap-2 items-center"><img
                    src="/Assets/Magic/MagicAccessibility.svg" alt="Accessibility Icon">
                ACCESSIBILITY</a></li>

    </ul>
</nav>