<?php 
namespace App\Views\Magic\Components;
use App\CmsModels\PageSection;
/** @var PageSection $section */
?>

<section class="magic_image_article_vertical gap-5 mb-6 w-[90%] mx-auto">
    <?php echo $section->content_html ?>
    <article class="flex flex-col md:flex-row gap-3 items-start w-full">
        <img class="w-full md:w-1/3 h-auto object-cover rounded-xl shrink-0"
            src="<?php echo $section->media->file_path ?>" alt="<?php echo $section->media->alt_text ?>">
        <section class="flex flex-col gap-3 items-start w-full min-w-0 break-words">
            <?php echo $section->content_html_2 ?>
        </section>

    </article>
</section>



<!-- <section class="flex flex-col gap-5 mb-6 w-[90%] mx-auto">
    <header class="text-center mb-4 text-3xl font-courierprime text-[var(--magic-gold-accent)]">
        <strong>
            <h2>Game 6: The Final Enigma</h2>
        </strong>
    </header>
    <article class="flex flex-col md:flex-row gap-3">
        <img class="w-full md:w-1/3 h-auto object-cover rounded-xl" src="/Assets/Magic/Game4.png" alt="">
        <section class="flex flex-col gap-3 items-start">
            <div class="flex flex-col items-start">
                <header class="text-center mb-4 text-2xl font-courierprime text-[var(--magic-gold-accent)]">
                    <strong>
                        <h3>The Assignment:</h3>
                    </strong>
                </header>
                <p class="px-4 font-robotomono">You have collected 5 Badges. Professor Teyler has one last question to
                    open his treasure chest. Question: 'I am a place for Art, and I am a place for Science. I was built
                    by Pieter Teyler to help people learn. What is the most important treasure inside me?' (Multiple
                    Choice: A. The Gold, B. The Knowledge, C. The Sandwiches)</p>
            </div>
            <div class="flex flex-col items-start">
                <header class="text-center mb-4 text-2xl font-courierprime text-[var(--magic-gold-accent)]">
                    <strong>
                        <h3>Hints for the Solution:</h3>
                    </strong>
                </header>
                <ul class="px-4 list-decimal list-inside font-robotomono">
                    <li>Think about what Dr. Feathers and Prof. Digit taught you.</li>
                    <li>Gold is expensive, but learning lasts forever.</li>
                </ul>
            </div>
            <div class="flex flex-col items-start">
                <header class="text-center mb-4 text-2xl font-courierprime text-[var(--magic-gold-accent)]">
                    <strong>
                        <h3>The Explanation (The Science/History):</h3>
                    </strong>
                </header>
                <p class="px-4 font-robotomono">Correct! The answer is Knowledge. Pieter Teyler didn't want to hide his
                    treasures; he wanted to share them so everyone could become smarter. By solving these puzzles, YOU
                    are now the treasure of the museum!</p>
            </div>
            <div class="flex flex-col items-start">
                <header class="text-center mb-4 text-2xl font-courierprime text-[var(--magic-gold-accent)]">
                    <strong>
                        <h3>Hint to Next Assignment:</h3>
                    </strong>
                </header>
                <p class="px-4 font-robotomono">MISSION COMPLETE! Show this screen to the person at the front desk to
                    collect your real-life sticker!</p>
            </div>
            <div class="flex flex-col items-start">
                <header class="text-center mb-4 text-2xl font-courierprime text-[var(--magic-gold-accent)]">
                    <strong>
                        <h3>Digital Stamp Book Status:</h3>
                    </strong>
                </header>
                <p class="px-4 font-robotomono">On Success: A huge "MASTER DETECTIVE" stamp covers the whole page with
                    confetti animation.
                </p>
            </div>
        </section>

    </article>
</section> -->