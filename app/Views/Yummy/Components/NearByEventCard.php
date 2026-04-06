<?php
namespace App\Views\Yummy\Components;
/**
 * @var object $event EventCategory object
 */
?>

<article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow h-full flex flex-col">
    <!-- Image Container with Fixed Size -->
    <div class="h-48 w-full overflow-hidden bg-gray-200">
        <img src="<?php echo htmlspecialchars($event->event_media?->file_path ?? ''); ?>"
            class="w-full h-full object-cover"
            alt="<?php echo htmlspecialchars($event->event_media?->alt_text ?? $event->title); ?>" />
    </div>

    <!-- Content Container -->
    <div class="p-5 flex flex-col flex-grow">
        <!-- Type Badge -->
        <div class="mb-2">
            <span
                class="inline-block bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] text-xs font-semibold px-3 py-1 rounded-full">
                <?php echo htmlspecialchars($event->type?->value ?? 'Event'); ?>
            </span>
        </div>

        <!-- Title -->
        <h3 class="text-lg font-bold text-gray-900 mb-2">
            <?php echo htmlspecialchars($event->title); ?>
        </h3>

        <!-- Description -->
        <p class="text-sm text-gray-600 mb-4 flex-grow line-clamp-2">
            <?php echo strip_tags($event->category_description) ?? 'Discover more about this amazing event'; ?>
        </p>

        <!-- Button -->
        <a href="<?php echo htmlspecialchars($event->slug ?? '#'); ?>"
            class="bg-[var(--yummy-sec-btn)] text-[var(--yummy-sec-btn-text)] hover:bg-[var(--yummy-sec-hover-btn)] hover:text-[var(--yummy-sec-hover-btn-text)] text-white text-sm font-semibold px-4 py-2 rounded transition-colors text-center">
            View Event
        </a>
    </div>
</article>