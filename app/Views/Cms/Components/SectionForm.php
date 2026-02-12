<?php
namespace App\Views\Home\Components;
?>
<Section class="border p-4 rounded-md mb-4 inset-shadow-sm" data-section-type="<?= $section->section_type->value ?>">
    <input type="hidden" name="sections[<?= $i ?>][section_id]" value="<?= $section->section_id ?>">
    <input type="hidden" name="sections[<?= $i ?>][section_type]" value="<?= $section->section_type->value ?>">

    <article class="flex flex-row gap-2 justify-between">
        <article class="input_group">
            <label class="input_label">Section Title:</label>
            <input class="form_input" type="text" name="sections[<?= $i ?>][title]"
                value="<?= htmlspecialchars($section->title ?? '') ?>">
        </article>
        <article class="input_group">
            <label class="input_label">Section Order:</label>
            <input class="form_input" type="number" name="sections[<?= $i ?>][display_order]"
                value="<?= (int)($section->display_order ?? 0) ?>">
        </article>
    </article>
    <?php if (isset($section->cta_text) ): ?>
    <article class="flex flex-row gap-2 justify-between">
        <article class="input_group">
            <label class="input_label">Cta Text:</label>
            <input class="form_input" type="text" name="sections[<?= $i ?>][cta_text]"
                value="<?= htmlspecialchars($section->cta_text ?? '') ?>">
        </article>
        <article class="input_group">
            <label class="input_label">Cta URL:</label>
            <input class="form_input" type="text" name="sections[<?= $i ?>][cta_url]"
                value="<?= htmlspecialchars($section->cta_url ?? '') ?>">
        </article>
    </article>
    <?php endif; ?>
    <article class="input_group">
        <label class="input_label">Section Content:</label>
        <textarea class="tinymce"
            name="sections[<?= $i ?>][content_html]"><?= htmlspecialchars($section->content_html ?? '') ?></textarea>
    </article>
    <?php if (isset($section->media->file_path) ): ?>
    <article class="input_group">
        <input type="hidden" name="sections[<?= $i ?>][media_id]" value="<?= $section->media->media_id ?>">
        <input type="hidden" name="sections[<?= $i ?>][file_path]" value="<?= $section->media->file_path ?>">
        <label class="input_label">Section Media</label>
        <input class="form_input" type="file" name="sections[<?= $i ?>][media_file]">
        <?php if ($section->media && $section->media->file_path): ?> <div class="mt-2">
            <p>Current Media:</p> <img src="<?= htmlspecialchars($section->media->file_path) ?>" alt="Section Media"
                class="max-w-xs">
        </div> <?php endif; ?>
        <label class="input_label">Alt text</label>
        <input class="form_input" type="text" name="sections[<?= $i ?>][alt_text]"
            value="<?= htmlspecialchars($section->media->alt_text ?? '') ?>">
    </article>
    </article>
    <?php endif; ?>

</Section>