<?php
namespace App\Views\Home\Components;
use App\Models\Enums\EventType;
use App\ViewModels\Home\ScheduleList;

/** @var ScheduleList $scheduleList */
?>


<div class="flex flex-col gap-4 items-center justify-center p-5 w-[90%] mx-auto mb-10">
    <section>
        <?php if (isset($scheduleSection->content_html)) {
            echo $scheduleSection->content_html;
        }?>

    </section>
    <?php include 'Spinner.php'; ?>


    <div id="schedule_container" class="w-full">
        <?php include 'ScheduleList.php'; ?>
    </div>
</div>