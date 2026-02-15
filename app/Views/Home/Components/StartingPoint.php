<?php
namespace App\Views\Home\Components;
$buttonIcon = '/Assets/Home/MapJazzIcon.svg';
$iconAlt = 'Default Icon';
switch ($venue->venue_type) {
    case 'Jazz':
        $buttonIcon = '/Assets/Home/MapJazzIcon.svg';
        $iconAlt = 'Jazz Icon';
        break;
    case 'Yummy':
        $buttonIcon = '/Assets/Home/MapYummyIcon.svg';
        $iconAlt = 'Food Icon';
        break;
    case 'History':
        $buttonIcon = '/Assets/Home/MapHistoryIcon.svg';
        $iconAlt = 'History Icon';
        break;
    case 'Dance':
        $buttonIcon = '/Assets/Home/MapDanceIcon.svg';
        $iconAlt = 'Dance Icon';
        break;
    case 'Magic':
        $buttonIcon = '/Assets/Home/MapMagicIcon.svg';
        $iconAlt = 'Magic Icon';
        break;
    default:
        $buttonIcon = '/Assets/Home/MapDefaultIcon.svg';
        $iconAlt = 'Default Icon';
}
?>

<li class="text-lg max-w-xs w-full ">
    <button type="button"
        class="location-btn flex flex-col gap-1 text-black font-medium w-full text-left px-2 py-2 
        border-1 border-gray-300 rounded-lg shadow-md hover:bg-gray-200 hover:inset-shadow-sm  focus:outline-none focus:ring-2 focus:ring-gray-400"
        aria-controls="interactive-map">
        <span class="flex flex-row gap-1" aria-hidden="true">
            <img src="<?= $buttonIcon ?>" alt="<?= $iconAlt ?>">
            <span class="location-title"><?= htmlspecialchars($venue->name) ?></span>
        </span>
        <span class="text-content flex flex-col items-start ">
            <span class="location-desc"><?= htmlspecialchars($venue->venue_type) ?></span>
        </span>
    </button>
</li>