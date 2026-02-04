<?php
namespace App\Views\Home\Components;
?>

<div
    class="flex flex-col items-center justify-center p-5 w-[90%] mx-auto border-4 border-[--home-gold-accent] rounded-[20px] mb-10">
    <header class="headers_home pb-5">
        <h1 class="text-4xl font-bold">Our events are for everyone</h1>
    </header>
    <div class="flex flex-col gap-6 ">
        <!-- History Event -->
        <article class="flex flex-row w-full justify-around items-center">

            <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 border-[--home-history-accent] shadow-md"
                src="/Assets/Home/HistoryEventHome.png" alt="St. Bavo Church">

            <div class="flex flex-col w-[30%] items-center gap-4">
                <div class="flex flex-col items-center text-center">
                    <header class="headers_home pb-3 w-fit">
                        <h1 class="text-4xl font-bold my-2">Family Fun Day</h1>
                    </header>
                    <p>Join us for a day filled with exciting activities for all ages, including games, crafts, and live
                        entertainment. Perfect for families looking to create lasting memories together.</p>
                </div>
                <a class="home_history_button" href="">View Event Details</a>
            </div>
        </article>
        <!-- Yummy Event -->
        <article class="flex flex-row-reverse w-full justify-around items-center">
            <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 border-[--home-yummy-accent] shadow-md"
                src="/Assets/Home/YummyEventCardHome.png" alt="St. Bavo Church">

            <div class="flex flex-col w-[30%] items-center gap-4">
                <div class="flex flex-col items-center text-center">
                    <header class="headers_home pb-3 w-fit">
                        <h1 class="text-4xl font-bold my-2">Yummy!</h1>
                    </header>
                    <p>Sample the best of Haarlem's food scene! From local craft beer to Dutch classics and
                        international street food.</p>
                </div>
                <a class="home_yummy_button" href="">View Event Details</a>
            </div>
        </article>
        <!-- Jazz Event -->
        <article class="flex flex-row w-full justify-around items-center">
            <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 border-[--home-jazz-accent] shadow-md"
                src="/Assets/Home/JazzEventCardHome.png" alt="Groote Markt Jazz Event">

            <div class="flex flex-col w-[30%] items-center gap-4">
                <div class="flex flex-col items-center text-center">
                    <header class="headers_home pb-3 w-fit">
                        <h1 class="text-4xl font-bold my-2">Jazz!</h1>
                    </header>
                    <p>Experience smooth melodies and powerful improvisations from renowned international and local jazz
                        artists.</p>
                </div>
                <a class="home_jazz_button" href="">View Event Details</a>
            </div>
        </article>
        <!-- Dance Event -->
        <article class="flex flex-row-reverse w-full justify-around items-center">
            <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 border-[--home-dance-accent] shadow-md"
                src="/Assets/Home/DanceEventCardHome.png" alt="Groote Markt Dance Event">

            <div class="flex flex-col w-[30%] items-center gap-4">
                <div class="flex flex-col items-center text-center">
                    <header class="headers_home pb-3 w-fit">
                        <h1 class="text-4xl font-bold my-2">Dance!</h1>
                    </header>
                    <p>The ultimate festival after-party. Dance all night to the hottest electronic beats spun by top
                        national DJs.</p>
                </div>
                <a class="home_dance_button" href="">View Event Details</a>
            </div>
        </article>
        <!-- Magic@Teylers Event -->
        <article class="flex flex-row w-full justify-around items-center">
            <img class=" w-[40%] mr-5 h-auto object-cover rounded-xl border-4 border-[--home-magic-accent] shadow-md"
                src="/Assets/Home/MagicEventCardHome.png" alt="Teylers Museum Magic Event">

            <div class="flex flex-col w-[30%] items-center gap-4">
                <div class="flex flex-col items-center text-center">
                    <header class="headers_home pb-3 w-fit">
                        <h1 class="text-4xl font-bold my-2">Magic!</h1>
                    </header>
                    <p>Explore the intersection of magic and science with dazzling, live demonstrations and interactive
                        exhibits for all ages.</p>
                </div>
                <a class="home_magic_button" href="">View Event Details</a>
            </div>
        </article>


    </div>
</div>