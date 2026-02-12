<?php
namespace App\Views\Home\Components;
?>
<input type="hidden" name="sections[<?= $i ?>][section_id]" value="<?= $section->section_id ?>">
<input type="hidden" name="sections[<?= $i ?>][section_type]" value="<?= $section->section_type->value ?>">

<label class="input_label">Title</label>
<input class="form_input" type="text" name="sections[<?= $i ?>][title]"
    value="<?= htmlspecialchars($section->title ?? '') ?>">

<label class="input_label">Content</label>
<textarea class="tinymce" name="sections[<?= $i ?>][content_html]"><?= htmlspecialchars($section->content_html ?? '') ?></textarea>

<label class="input_label">Order</label>
<input class="form_input" type="number" name="sections[<?= $i ?>][display_order]"
    value="<?= (int)($section->display_order ?? 0) ?>">