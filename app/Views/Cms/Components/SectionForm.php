<?php

namespace App\Views\Home\Components;

use App\CmsModels\Enums\SectionType;
?>
<section class="border p-4 rounded-md mb-4 inset-shadow-sm bg-white w-full"
    data-section-type="<?= $section->section_type->value ?>">

    <div class="w-full">
        <input type="hidden" name="sections[<?= $i ?>][section_id]" value="<?= $section->section_id ?>">
        <input type="hidden" name="sections[<?= $i ?>][section_type]" value="<?= $section->section_type->value ?>">

        <!-- Section Header -->
        <div class="mb-4 pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                Section <?= $i + 1 ?>: <?= htmlspecialchars($section->title ?: $section->section_type->value) ?>
            </h3>
        </div>

        <article class="flex flex-row gap-2 justify-between mb-4">
            <article class="input_group flex-1">
                <label class="input_label">Section Title:</label>
                <input class="form_input" type="text" name="sections[<?= $i ?>][title]"
                    value="<?= htmlspecialchars($section->title ?? '') ?>">
            </article>
            <article class="input_group" style="width: 150px;">
                <label class="input_label">Section Order:</label>
                <input class="form_input" type="number" name="sections[<?= $i ?>][display_order]"
                    value="<?= (int)($section->display_order ?? 0) ?>">
            </article>
        </article>

        <?php if (isset($section->cta_text)): ?>
        <article class="flex flex-row gap-2 justify-between mb-4">
            <article class="input_group flex-1">
                <label class="input_label">CTA Text:</label>
                <input class="form_input" type="text" name="sections[<?= $i ?>][cta_text]"
                    value="<?= htmlspecialchars($section->cta_text ?? '') ?>">
            </article>
            <article class="input_group flex-1">
                <label class="input_label">CTA URL:</label>
                <input class="form_input" type="text" name="sections[<?= $i ?>][cta_url]"
                    value="<?= htmlspecialchars($section->cta_url ?? '') ?>">
            </article>
        </article>
        <?php endif; ?>

        <article class="input_group mb-4">
            <label class="input_label">Section Content:</label>
            <textarea class="tinymce"
                name="sections[<?= $i ?>][content_html]"><?= htmlspecialchars($section->content_html ?? '') ?></textarea>
        </article>
        <?php if (isset($section->content_html_2)): ?>
        <article class="input_group mb-4">
            <label class="input_label">Section Content 2:</label>
            <textarea class="tinymce"
                name="sections[<?= $i ?>][content_html_2]"><?= htmlspecialchars($section->content_html_2 ?? '') ?></textarea>
        </article>
        <?php endif; ?>

        <?php if (isset($section->media)): ?>
        <article class="input_group border-t pt-4">
            <input type="hidden" name="sections[<?= $i ?>][media_id]" value="<?= $section->media->media_id ?? '' ?>">

            <label class="input_label">Section Media:</label>

            <!-- Show current image if exists -->
            <?php if ($section->media && $section->media->file_path): ?>
            <div class="mb-3 p-3 bg-gray-50 rounded border">
                <p class="text-sm font-semibold mb-2">Current Image:</p>
                <img src="<?= htmlspecialchars($section->media->file_path) ?>"
                    alt="<?= htmlspecialchars($section->media->alt_text ?? 'Section Media') ?>"
                    class="max-w-sm rounded shadow">
                <p class="text-xs text-gray-600 mt-2">
                    <?= htmlspecialchars($section->media->file_path) ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- File upload input -->
            <input class="form_input mb-2" type="file" name="section_media_<?= $i ?>"
                accept="image/jpeg,image/png,image/webp">
            <p class="text-xs text-gray-500 mb-3">
                Max 5MB • JPG, PNG, or WebP
            </p>

            <!-- Alt text -->
            <label class="input_label">Alt Text:</label>
            <input class="form_input" type="text" name="sections[<?= $i ?>][alt_text]"
                value="<?= htmlspecialchars($section->media->alt_text ?? '') ?>" placeholder="Describe the image">
        </article>
        <?php endif; ?>
    </div>

    <?php
    if ($section->section_type === SectionType::event_left || $section->section_type === SectionType::event_right) {
        $isReverse = $section->section_type === SectionType::event_right;
        include __DIR__ . '/../../Home/Components/EventCard.php';
    } ?>
</section>