<?php 
namespace App\Views\Magic\Components;
/** @var PageSection $section */

?>
<section class="<?php echo $imageSectionStyle['cardStyle'] ?>   justify-center items-center gap-6">

    <figure class="flex flex-col <?php echo $imageSectionStyle['imageStyle'] ?>">
        <img class=" h-auto object-cover rounded-xl shadow-md "
            src="<?php echo htmlspecialchars($section->media->file_path ?? '') ?>"
            alt="<?php echo htmlspecialchars($section->media->alt_text ?? '') ?>">
        <figcaption class="text-[1rem] mt-3 font-bold font-courierprime">
            <?php echo htmlspecialchars($section->media->alt_text ?? '') ?>
        </figcaption>
    </figure>

    <article class="flex flex-col w-full md:w-[30%] items-center gap-4">
        <?php echo $section->content_html ?>
        <!-- <header class="text-center mb-4 text-3xl font-courierprime text-[var(--magic-gold-accent)]">
            <strong>
                <h2>The Chaos</h2>
            </strong>
        </header>
        <ul class="flex flex-row md:flex-col font-robotomono text-lg">
            <li class="flex flex-row">
                <p>Name: DR. PHINEAS FEATHERS</p>
            </li>
            <li>
                <p>Role: The Clumsy Biologist</p>
            </li>
            <li>
                <p>Trait: Expert on Eggs & Bones</p>
            </li>
            <li>
                <p>The Vibe: "He knows everything about dinosaurs, but he keeps losing his glasses."</p>
            </li>
        </ul> -->

    </article>
</section>



<!-- <section class="<?php echo $imageSectionStyle['cardStyle'] ?> w-full justify-center items-center gap-6">

    <figure class="flex flex-col w-[23%] ">
        <img class=" h-auto object-cover rounded-xl shadow-md <?php echo $imageSectionStyle['imageStyle'] ?>"
            src="/Assets/Magic/LorentzRoomPic.png" alt="Lorentz Room">
        <figcaption class="text-[1rem] mt-3 font-bold font-courierprime">
            Lorentz Room at Teylers Museum
        </figcaption>
    </figure>

    <article class="flex flex-col w-[30%] items-center gap-4">
        <header class="text-center mb-4 text-3xl font-courierprime text-[var(--magic-gold-accent)]">
            <strong>
                <h2>The Lorentz Formula: A Live Science Spectacular</h2>
            </strong>
        </header>
        <p class="font-robotomono text-lg">
            Also at Teylers: The Lorentz Formula. A theatrical journey into the mind of a Nobel Prize winner.
        </p>

    </article>
</section> -->