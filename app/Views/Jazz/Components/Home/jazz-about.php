<?php

namespace App\Views\Jazz\Components\Home;

/**
 * Jazz About — short festival description section.
 *
 * @var object|null $aboutSection CMS section with title and content_html.
 */
?>

<section class="py-4 bg-white" aria-labelledby="about-heading">
    <div class="container mx-auto px-4">

        <?php if (!empty($aboutSection->content_html)): ?>
            <?= $aboutSection->content_html ?>
        <?php else: ?>
            <h2 id="about-heading"
                class="text-2xl md:text-4xl font-bold text-center mb-8"
                style="font-family: 'Cormorant Garamond', serif;">
                <?= htmlspecialchars($aboutSection->title ?? 'About the Festival') ?>
            </h2>
            <div class="max-w-4xl mx-auto bg-[#F8F4F0] rounded-lg p-6 md:p-12 text-base md:text-xl text-center leading-relaxed text-gray-800">
                <p>
                    Step into the world of <strong>jazz in Haarlem</strong> – where music comes alive!
                    Get ready for the <strong>vibey tunes, cool beats, and great performances</strong>
                    that make Haarlem a jazz lover's city. Join us as we explore the local jazz scene,
                    events, and talented musicians that make Haarlem Jazz what it is.
                </p>
            </div>
        <?php endif; ?>

    </div>
</section>
