<?php
namespace App\Views\Magic\Components;
/** @var PageSection $heroSection */

?>

<section class="flex flex-col xl:flex-row gap-5 mb-6">
    <img class="md:w-auto object-cover rounded-lg shadow-lg" src="<?php echo $heroSection->media->file_path ?>"
        alt="<?php echo $heroSection->media->alt_text ?>">
    <!-- <img class="md:w-auto object-cover rounded-lg shadow-lg" src="/Assets/Magic/AltHeroImage.png" alt=""> -->
    <section class="flex flex-col md:flex-row justify-around w-[90%] mx-auto py-10 gap-6 md:gap-10 font-courierprime ">
        <article
            class="bg-[var(--magic-bg-secondary-dark)] h-min p-5 rounded-md shadow-xl text-courierprime w-full md:w-auto min-w-0">

            <?php echo $heroSection->content_html ?>
            <!-- <section class="pb-6 ">
                <header class="flex flex-col gap-2 font-bold mb-3"><strong
                        class="magic-hero-goldtext">Magic@Teylers:</strong>
                    <h1 class="text-3xl">The Secrets of Professor Teyler</h1>
                </header>
                <section class="font-robotomono text-lg mt-5 mb-5">
                    <header class="mb-2">
                        <h3 class="magic-hero-goldtext mb-4">An interactive mystery for young detectives</h3>
                        <h4 class="magic-strong">Play Anywhere, Explore Deeper On-Site.</h4>
                    </header>
                    <p class="magic-hero-goldtext">Experience the magic at home with our free app adventures, or visit
                        the Teylers Museum to unlock
                        the full AR mystery.</p>
                </section>
                <section class="flex flex-col gap-4 mt-6">
                    <div class="flex flex-row gap-4"><a href="../../../"><img src="../../../Assets/Magic/googleplay.svg"
                                alt="Google Play Badge"></a> <a><img src="../../../Assets/Magic/appstore.svg"
                                alt="App Store Badge"></a></div>
                </section>
            </section> -->


            <div class="flex flex-col gap-3 md:flex-row md:gap-5 justify-between pb-6 ">
                <?php
                $ctaURL = $heroSection->cta_url ?? '/events-magic-tickets';
                $buttonLabel = $heroSection->cta_text ?? 'Get Tickets';
                include 'RedButton.php'; ?>
                <?php
                $ctaURL = $heroSection->cta_url ?? '/events-magic-tickets';
                $buttonLabel = $heroSection->cta_text ?? 'Download the App';
                include 'RedButton.php'; ?>
            </div>
        </article>
        <!-- <img class="h-[85vh] max-w-full w-full md:w-auto object-contain"
            src="<?php echo htmlspecialchars($heroSection->media->file_path) ?>"
            alt="<?php echo htmlspecialchars($heroSection->media->alt_text) ?>"> -->
    </section>
    <!-- <?php echo $heroSection->content_html_2 ?> -->
</section>