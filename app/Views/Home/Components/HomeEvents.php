<?php
namespace App\Views\Home\Components;
$isReverse = false;
?>

<div
    class="flex flex-col items-center justify-center p-5 w-[90%] mx-auto border-4 border-[--home-gold-accent] rounded-[20px] mb-10">
    <header class="headers_home pb-5">
        <?php echo isset($eventTitleSection->content_html) ? $eventTitleSection->content_html : 
        '<h1 class="text-4xl font-bold">Section Not Found</h1>' ?>

    </header>
    <div class="flex flex-col gap-6 ">

        <?php foreach ($eventSections as $section):
        if ($section->section_type === \App\CmsModels\Enums\SectionType::event_left) {
            $isReverse = false;
        } else {
            $isReverse = true;
        }
        include __DIR__ . '/EventCard.php';?>
        <?php endforeach; ?>
    </div>
</div>