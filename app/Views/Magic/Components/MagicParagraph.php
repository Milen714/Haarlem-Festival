<?php 
namespace App\Views\Magic\Components;
use App\CmsModels\PageSection;
/** @var PageSection $section */

?>

<article class="flex flex-col justify-center items-center gap-4 w-[55%] ">
    <?php echo $section->content_html ?>
    <!-- <div>
        <header class="text-center mb-4 text-3xl font-courierprime text-[var(--magic-gold-accent)]">
            <strong>
                <h2>The Lorentz Formula: LIVE!</h2>
            </strong>
        </header>
        <p class="text-center font-robotomono text-lg">A High-Voltage Spectacular for the Whole Family</p>
        <p class="text-center font-robotomono text-lg"> Step away from the screen and into the laboratory! Join our "Mad
            Scientists" for an electrifying 50-minute demonstration. We are recreating the famous experiments of Nobel
            Prize winner Hendrik Lorentz right before your eyes. Witness giant sparks, invisible forces, and
            hair-raising physics in this interactive show where you help prove the formula works.
            Can you handle the voltage?</p>
    </div> -->
    <!-- <header class="text-center mb-3 text-3xl font-courierprime text-[var(--magic-gold-accent)]">
        <strong>
            <h2>Join Forces To Crack The Secret Code With:</h2>
        </strong>
    </header> -->
    <?php if(isset($section->content_html_2)): ?>
    <?php echo $section->content_html_2 ?>
    <?php endif ?>
</article>