<?php
namespace App\Views\Jazz\Components;
?>

<section class="py-16 bg-white" aria-labelledby="about-heading">
    <article class="container mx-auto px-4">
        <h2 id="about-heading" class="text-4xl font-bold text-center mb-12" style="font-family: 'Cormorant Garamond', serif;">
            <?= htmlspecialchars($aboutSection->title ?? 'About the Festival') ?>
        </h2>
        
        <aside class="max-w-4xl mx-auto bg-[#F8F4F0] rounded-lg p-12">
            <p class="text-xl text-center leading-relaxed text-gray-800">
                Step into the world of <strong>jazz in Haarlem</strong> – where music comes alive! Get ready for the 
                <strong>Vibey tunes, cool beats, and great performances</strong> that make Haarlem a 
                <strong>THE</strong> jazz lover's city. Join us as we explore the local jazz scene, events, and 
                talented musicians that make Haarlem jazz what it is. Let's get into the music together and enjoy 
                the rhythm of <strong>Haarlem Jazz!</strong>
            </p>
        </aside>
    </article>
</section>