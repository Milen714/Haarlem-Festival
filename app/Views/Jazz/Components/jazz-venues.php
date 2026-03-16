<?php

namespace App\Views\Jazz\Components;

$venues = is_array($venues ?? null) ? $venues : [];

if (empty($venues) && !empty($scheduleByDate) && is_array($scheduleByDate)) {
    $fallbackVenueMap = [];

    foreach ($scheduleByDate as $daySchedules) {
        if (!is_array($daySchedules)) {
            continue;
        }

        foreach ($daySchedules as $schedule) {
            if (!is_object($schedule)) {
                continue;
            }

            $scheduleVenue = $schedule->venue ?? null;
            if (!is_object($scheduleVenue) || empty($scheduleVenue->name)) {
                continue;
            }

            $fallbackKey = (int) ($scheduleVenue->venue_id ?? 0);
            if ($fallbackKey <= 0) {
                $fallbackKey = crc32(strtolower(trim((string) $scheduleVenue->name)));
            }

            if (!isset($fallbackVenueMap[$fallbackKey])) {
                $fallbackVenueMap[$fallbackKey] = [
                    'name' => (string) ($scheduleVenue->name ?? 'Venue'),
                    'street_address' => (string) ($scheduleVenue->street_address ?? ''),
                    'city' => (string) ($scheduleVenue->city ?? 'Haarlem'),
                    'postal_code' => (string) ($scheduleVenue->postal_code ?? ''),
                    'capacity' => (int) ($scheduleVenue->capacity ?? 0),
                    'phone' => (string) ($scheduleVenue->phone ?? ''),
                    'description_html' => (string) ($scheduleVenue->description_html ?? ''),
                    'image_path' => method_exists($scheduleVenue, 'getImagePath') ? (string) $scheduleVenue->getImagePath() : '',
                    'image_alt' => method_exists($scheduleVenue, 'getImageAlt') ? (string) $scheduleVenue->getImageAlt() : (string) ($scheduleVenue->name ?? 'Venue'),
                ];
            }
        }
    }

    $venues = array_values($fallbackVenueMap);
}

$renderableVenues = array_values(array_filter(
    $venues,
    static fn($v) => is_object($v) || is_array($v)
));

$readField = static function ($item, string $key, $default = null) {
    if (is_array($item)) {
        return $item[$key] ?? $default;
    }
    if (is_object($item)) {
        return $item->{$key} ?? $default;
    }
    return $default;
};

$sectionTitle = 'Venues';
$sectionContentHtml = '';
$sectionContentHtml2 = '';
if (isset($venuesSection)) {
    if (is_object($venuesSection) && isset($venuesSection->title)) {
        $sectionTitle = (string) $venuesSection->title;
        $sectionContentHtml = (string) ($venuesSection->content_html ?? '');
        $sectionContentHtml2 = (string) ($venuesSection->content_html_2 ?? '');
    } elseif (is_array($venuesSection) && isset($venuesSection['title'])) {
        $sectionTitle = (string) $venuesSection['title'];
        $sectionContentHtml = (string) ($venuesSection['content_html'] ?? '');
        $sectionContentHtml2 = (string) ($venuesSection['content_html_2'] ?? '');
    }
}

?>

<section class="py-10 bg-gray-50" aria-labelledby="venues-heading">
    <div class="container mx-auto px-4 max-w-[1200px]">
        <header class="mb-12">
            <h2 id="venues-heading" class="text-4xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                <?= htmlspecialchars($sectionTitle) ?>
            </h2>
        </header>

        <?php if (!empty($renderableVenues)): ?>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-6" role="list">
                <?php foreach ($renderableVenues as $venue): ?>
                    <?php
                    if (!is_object($venue) && !is_array($venue)) {
                        continue;
                    }

                    $venueNameRaw = (string) $readField($venue, 'name', 'Venue');
                    $venueName = htmlspecialchars($venueNameRaw);

                    $hasImage = is_object($venue) && method_exists($venue, 'hasImage')
                        ? $venue->hasImage()
                        : !empty((string) $readField($venue, 'image_path', ''));

                    $imagePath = is_object($venue) && method_exists($venue, 'getImagePath')
                        ? $venue->getImagePath()
                        : (!empty((string) $readField($venue, 'image_path', ''))
                            ? (string) $readField($venue, 'image_path', '')
                            : '/Assets/Home/ImagePlaceholder.png');

                    $imageAlt = is_object($venue) && method_exists($venue, 'getImageAlt')
                        ? $venue->getImageAlt()
                        : $venueNameRaw;

                    $fullAddress = is_object($venue) && method_exists($venue, 'getFullAddress')
                        ? $venue->getFullAddress()
                        : trim((string) $readField($venue, 'street_address', '') . ', ' . (string) $readField($venue, 'city', ''));

                    $capacity = (int) $readField($venue, 'capacity', 0);
                    $capacityDisplay = is_object($venue) && method_exists($venue, 'getCapacityDisplay')
                        ? $venue->getCapacityDisplay()
                        : ($capacity > 0 ? (string) $capacity . ' capacity' : 'Open Air');

                    $mapLink = is_object($venue) && method_exists($venue, 'getMapLink')
                        ? $venue->getMapLink()
                        : ('https://www.google.com/maps/search/?api=1&query=' . urlencode($fullAddress));

                    $phone = (string) $readField($venue, 'phone', '');
                    $descriptionHtml = (string) $readField($venue, 'description_html', '');
                    ?>
                    <li>
                        <article
                            class="bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow h-full flex flex-col">
                            <!-- Venue Image -->
                            <?php if ($hasImage): ?>
                                <figure class="relative h-48 overflow-hidden">
                                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($imageAlt) ?>"
                                        class="w-full h-full object-cover" loading="lazy" />
                                </figure>
                            <?php endif; ?>

                            <!-- Venue Content -->
                            <div class="p-6 flex flex-col flex-grow">
                                <header class="mb-4">
                                    <h3 class="text-2xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                                        <?= $venueName ?>
                                    </h3>
                                </header>

                                <!-- Venue Details -->
                                <dl class="space-y-2 mb-4">
                                    <!-- Address -->
                                    <div class="flex items-start text-sm text-gray-700">
                                        <dt class="sr-only">Address</dt>
                                        <dd class="flex items-start w-full">
                                            <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0 mt-0.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <address class="not-italic"><?= htmlspecialchars($fullAddress) ?></address>
                                        </dd>
                                    </div>

                                    <!-- Capacity -->
                                    <div class="flex items-center text-sm text-gray-700">
                                        <dt class="sr-only">Capacity</dt>
                                        <dd class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <data value="<?= $capacity ?>"><?= htmlspecialchars($capacityDisplay) ?></data>
                                        </dd>
                                    </div>

                                    <!-- Phone (if available) -->
                                    <?php if ($phone !== ''): ?>
                                        <div class="flex items-center text-sm text-gray-700">
                                            <dt class="sr-only">Phone</dt>
                                            <dd class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <a href="tel:<?= htmlspecialchars($phone) ?>" class="hover:underline">
                                                    <?= htmlspecialchars($phone) ?>
                                                </a>
                                            </dd>
                                        </div>
                                    <?php endif; ?>
                                </dl>

                                <!-- Description -->
                                <?php if ($descriptionHtml !== ''): ?>
                                    <aside class="text-sm text-gray-600 mb-4 prose prose-sm max-w-none">
                                        <?= $descriptionHtml ?>
                                    </aside>
                                <?php endif; ?>

                                <!-- Actions -->
                                <footer class="mt-auto">
                                    <div aria-label="Venue actions">
                                        <a href="<?= htmlspecialchars($mapLink) ?>" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center text-sm font-semibold text-gray-900 hover:text-gray-600 transition-colors"
                                            aria-label="View <?= $venueName ?> on Google Maps">
                                            <span>View Map</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </footer>
                            </div>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?php if ($sectionContentHtml !== '' || $sectionContentHtml2 !== ''): ?>
                <div class="prose prose-sm max-w-none text-gray-700">
                    <?= $sectionContentHtml ?>
                    <?= $sectionContentHtml2 ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16" role="status" aria-live="polite">
                    <figure class="inline-block border-2 border-gray-200 rounded-2xl p-12 bg-white">
                        <div class="text-6xl mb-4" aria-hidden="true">📍</div>
                        <figcaption class="text-gray-600 text-lg">Venue information coming soon.</figcaption>
                    </figure>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>