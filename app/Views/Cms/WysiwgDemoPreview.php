<?php

namespace App\Views\Cms;
$existingHtml = '<h1><strong>Hello</strong></h1><p>This is saved content.</p>';
$html = isset($content) ? $content : $existingHtml;
?>


<div class="w-[90%] mx-auto mt-10">
    <header class="mb-5 border-b-4 border-[--home-gold-accent] pb-2 ">
        <h1 class="text-4xl text-[var(--text-home-primary)] font-bold text-center mb-4">
            WYSIWYG Demo Preview</h1>
    </header>
    <div class="prose max-w-full border p-5 rounded-lg bg-white text-red-700">
        <?= $html ?>
    </div>
    <p><a href="/wysiwyg-demo">Back</a></p>

    <div class="magic_paragraph">
        <?= $html ?>
    </div>