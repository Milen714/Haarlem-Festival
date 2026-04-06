<?php
namespace App\Views\History;

use App\Models\History\Landmark;

/** @var Landmark $landmark */
/** @var string $introImage */
/** @var string $historyImage */
/** @var string $whyVisitImage */

$breadcrumbs = [
    ['label' => 'Home',            'url' => '/'],
    ['label' => 'Haarlem History', 'url' => '/events-history'],
    ['label' => $landmark->name],
];
?>

<div class="antialiased text-ink-800 bg-[var(--color-bg-history)] min-h-screen">

    <?php include __DIR__ . '/Components/breadcrumb.php'; ?>

    <?php include __DIR__ . '/Components/HistoryDetailLandmark.php'; ?>

    <?php include __DIR__ . '/Components/HistoryDiscoverMore.php'; ?>

    <?php include __DIR__ . '/Components/HistoryBookTour.php'; ?>

</div>
