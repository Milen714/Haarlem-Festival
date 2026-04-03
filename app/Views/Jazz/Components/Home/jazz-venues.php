<?php

namespace App\Views\Jazz\Components\Home;

/**
 * Jazz Venues — card grid listing all festival venues.
 *
 * Expects $venues to be an array of Venue objects or associative arrays.
 * Falls back to extracting unique venues from $scheduleByDate when $venues is empty.
 *
 * @var array       $venues         Venue objects / arrays passed from the controller.
 * @var array       $scheduleByDate Schedule grouped by date (used as fallback venue source).
 * @var object|null $venuesSection  CMS section with a title and optional content.
 */

$venues = is_array($venues ?? null) ? $venues : [];

/* ── Fallback: extract unique venues from the schedule when none were passed directly ── */
if (empty($venues) && !empty($scheduleByDate) && is_array($scheduleByDate)) {
    $fallbackVenueMap = [];

    try {
        foreach ($scheduleByDate as $daySchedules) {
            if (!is_array($daySchedules)) continue;

            foreach ($daySchedules as $schedule) {
                if (!is_object($schedule)) continue;

                $scheduleVenue = $schedule->venue ?? null;
                if (!is_object($scheduleVenue) || empty($scheduleVenue->name)) continue;

                $venueId = (int) ($scheduleVenue->venue_id ?? 0);
                $mapKey  = $venueId > 0 ? $venueId : crc32(strtolower(trim((string) $scheduleVenue->name)));

                if (!isset($fallbackVenueMap[$mapKey])) {
                    $fallbackVenueMap[$mapKey] = [
                        'name'             => (string) ($scheduleVenue->name ?? 'Venue'),
                        'street_address'   => (string) ($scheduleVenue->street_address ?? ''),
                        'city'             => (string) ($scheduleVenue->city ?? 'Haarlem'),
                        'postal_code'      => (string) ($scheduleVenue->postal_code ?? ''),
                        'capacity'         => (int)    ($scheduleVenue->capacity ?? 0),
                        'phone'            => (string) ($scheduleVenue->phone ?? ''),
                        'description_html' => (string) ($scheduleVenue->description_html ?? ''),
                        'image_path'       => method_exists($scheduleVenue, 'getImagePath') ? (string) $scheduleVenue->getImagePath() : '',
                        'image_alt'        => method_exists($scheduleVenue, 'getImageAlt')  ? (string) $scheduleVenue->getImageAlt()  : (string) ($scheduleVenue->name ?? 'Venue'),
                    ];
                }
            }
        }
    } catch (\Throwable $e) {
        $fallbackVenueMap = [];
    }

    $venues = array_values($fallbackVenueMap);
}

/* ── Filter to renderable items only ── */
$renderableVenues = array_values(array_filter(
    $venues,
    static fn($venue) => is_object($venue) || is_array($venue)
));

/* ── Helper: read a field from either an object or an associative array ── */
$readVenueField = static function ($venue, string $field, $default = null) {
    return is_array($venue) ? ($venue[$field] ?? $default) : ($venue->{$field} ?? $default);
};

/* ── Section metadata ── */
$sectionTitle       = 'Venues';
$sectionContentHtml = '';

if (isset($venuesSection)) {
    $sectionTitle       = (string) ($venuesSection->title       ?? $venuesSection['title']       ?? 'Venues');
    $sectionContentHtml = (string) ($venuesSection->content_html ?? $venuesSection['content_html'] ?? '');
}
?>

<section class="py-10 bg-gray-50" aria-labelledby="venues-heading">
    <div class="container mx-auto px-4 max-w-[1200px]">

        <header class="mb-12">
            <h2 id="venues-heading"
                class="text-4xl font-bold"
                style="font-family: 'Cormorant Garamond', serif;">
                <?= htmlspecialchars($sectionTitle) ?>
            </h2>
        </header>

        <?php if (!empty($renderableVenues)): ?>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-6" role="list">
                <?php foreach ($renderableVenues as $venue): ?>
                <?php
                    $venueName       = htmlspecialchars((string) $readVenueField($venue, 'name', 'Venue'));
                    $rawVenueName    = (string) $readVenueField($venue, 'name', 'Venue');

                    $hasImage        = is_object($venue) && method_exists($venue, 'hasImage')
                                        ? $venue->hasImage()
                                        : !empty((string) $readVenueField($venue, 'image_path', ''));

                    $imagePath       = is_object($venue) && method_exists($venue, 'getImagePath')
                                        ? $venue->getImagePath()
                                        : ((string) $readVenueField($venue, 'image_path', '') ?: '/Assets/Home/ImagePlaceholder.png');

                    $imageAlt        = is_object($venue) && method_exists($venue, 'getImageAlt')
                                        ? $venue->getImageAlt()
                                        : $rawVenueName;

                    $fullAddress     = is_object($venue) && method_exists($venue, 'getFullAddress')
                                        ? $venue->getFullAddress()
                                        : trim((string) $readVenueField($venue, 'street_address', '') . ', ' . (string) $readVenueField($venue, 'city', ''));

                    $capacity        = (int) $readVenueField($venue, 'capacity', 0);
                    $capacityDisplay = is_object($venue) && method_exists($venue, 'getCapacityDisplay')
                                        ? $venue->getCapacityDisplay()
                                        : ($capacity > 0 ? $capacity . ' capacity' : 'Open Air');

                    $mapLink         = is_object($venue) && method_exists($venue, 'getMapLink')
                                        ? $venue->getMapLink()
                                        : ('https://www.google.com/maps/search/?api=1&query=' . urlencode($fullAddress));

                    $phone           = (string) $readVenueField($venue, 'phone', '');
                    $descriptionHtml = (string) $readVenueField($venue, 'description_html', '');
                ?>
                <li>
                    <article class="bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow h-full flex flex-col">

                        <!-- Venue image -->
                        <?php if ($hasImage): ?>
                            <figure class="relative h-48 overflow-hidden">
                                <img src="<?= htmlspecialchars($imagePath) ?>"
                                     alt="<?= htmlspecialchars($imageAlt) ?>"
                                     class="w-full h-full object-cover"
                                     loading="lazy"/>
                            </figure>
                        <?php endif; ?>

                        <div class="p-6 flex flex-col flex-grow">
                            <header class="mb-4">
                                <h3 class="text-2xl font-bold" style="font-family: 'Cormorant Garamond', serif;">
                                    <?= $venueName ?>
                                </h3>
                            </header>

                            <!-- Venue metadata list -->
                            <dl class="space-y-2 mb-4">

                                <div class="flex items-start text-sm text-gray-700">
                                    <dt class="sr-only">Address</dt>
                                    <dd class="flex items-start w-full">
                                        <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <address class="not-italic"><?= htmlspecialchars($fullAddress) ?></address>
                                    </dd>
                                </div>

                                <div class="flex items-center text-sm text-gray-700">
                                    <dt class="sr-only">Capacity</dt>
                                    <dd class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <data value="<?= $capacity ?>"><?= htmlspecialchars($capacityDisplay) ?></data>
                                    </dd>
                                </div>

                                <?php if ($phone !== ''): ?>
                                <div class="flex items-center text-sm text-gray-700">
                                    <dt class="sr-only">Phone</dt>
                                    <dd class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <a href="tel:<?= htmlspecialchars($phone) ?>" class="hover:underline">
                                            <?= htmlspecialchars($phone) ?>
                                        </a>
                                    </dd>
                                </div>
                                <?php endif; ?>

                            </dl>

                            <?php if ($descriptionHtml !== ''): ?>
                                <div class="text-sm text-gray-600 mb-4 prose prose-sm max-w-none">
                                    <?= $descriptionHtml ?>
                                </div>
                            <?php endif; ?>

                            <footer class="mt-auto">
                                <a href="<?= htmlspecialchars($mapLink) ?>"
                                   target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center text-sm font-semibold text-gray-900 hover:text-gray-600 transition-colors"
                                   aria-label="View <?= $venueName ?> on Google Maps">
                                    View on Map
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </footer>
                        </div>

                    </article>
                </li>
                <?php endforeach; ?>
            </ul>

        <?php elseif ($sectionContentHtml !== ''): ?>
            <div class="prose prose-sm max-w-none text-gray-700">
                <?= $sectionContentHtml ?>
            </div>

        <?php else: ?>
            <div class="text-center py-16" role="status" aria-live="polite">
                <figure class="inline-block border-2 border-gray-200 rounded-2xl p-12 bg-white">
                    <div class="text-6xl mb-4" aria-hidden="true">📍</div>
                    <figcaption class="text-gray-600 text-lg">Venue information coming soon.</figcaption>
                </figure>
            </div>
        <?php endif; ?>

    </div>
</section>
