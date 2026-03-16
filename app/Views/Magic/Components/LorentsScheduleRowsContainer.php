<?php
namespace App\Views\Magic\Components;
use App\CmsModels\PageSection;
/** @var PageSection $section */

?>
<header
    class="flex flex-col gap-4 text-center mb-4 text-3xl font-courierprime text-[var(--magic-gold-accent)] border-b border-[#C69A4D] pb-2 w-[90%]">

    <?php echo isset($section->content_html) ? $section->content_html : ''; ?>

</header>
<section class="flex flex-row flex-wrap gap-2 justify-center mt-5">

    <?php include 'LorentzScheduleCard.php'; ?>
    <?php include 'LorentzScheduleCard.php'; ?>
    <?php include 'LorentzScheduleCard.php'; ?>

</section>
<article class="my-4">

    <?php echo isset($section->content_html_2) ? $section->content_html_2 : ''; ?>

</article>