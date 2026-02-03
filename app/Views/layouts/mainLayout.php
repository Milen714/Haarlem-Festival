<?php
$theme = $_COOKIE['theme'] ?? 'light';
$darkClass = $theme === 'dark' ? 'dark' : '';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $darkClass ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $title ?? 'My App'; ?></title>
    <link rel="icon" type="image/svg+xml" href="/Assets/Favicon.svg">
    <link rel="stylesheet" href="/css/output.css">

    <script src="https://js.stripe.com/v3/"></script>

</head>


<body class="flex flex-col min-h-screen bg_colors_home">
    <!-- Navbar -->
    <!-- <ul class="flex gap-6 p-4 bg-colors border-b border-[#2C3233] ">
            <li class="flex items-center gap-2">
                <a href="" class="after:content-arrow_right after:ml-1 text_colors_home font-bold">Home</a>
            </li>
            <li class="flex items-center gap-2">
                <a href="" class="after:content-arrow_right after:ml-1 text_colors_home font-bold">Events</a>
            </li>
            <li class="flex items-center gap-2">
                <a href="" class="after:content-arrow_right after:ml-1 text_colors_home font-bold">Schedule</a>
            </li>
            <li class="flex items-center gap-2">
                <a href="" class="after:content-arrow_right after:ml-1 text_colors_home font-bold">Personal Plan</a>
            </li>

        </ul>
    </nav> -->
    <nav class="bg_colors_home  w-full z-20 top-0 start-0 border-b border-[#2C3233]">

        <div class="nav-ul-container  max-w-screen-2xl flex flex-wrap items-center justify-between mx-auto p-4">
            <!-- Logo and Brand Name -->
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="/Assets/Nav/FestivalLogo.svg" class="h-min" alt="Haarlem Festival Logo" />
            </a>
            <!-- Mobile menu button -->
            <button data-collapse-toggle="navbar-multi-level-dropdown" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-body rounded-base md:hidden hover:bg-neutral-secondary-soft hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary"
                aria-controls="navbar-multi-level-dropdown" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
                </svg>
            </button>
            <!-- Navigation links -->
            <div class="hidden w-full md:block md:w-auto" id="navbar-multi-level-dropdown">
                <ul
                    class="flex flex-col items-center font-medium p-4 md:p-0 mt-4 rounded-lg border border-[#2C3233] bg_colors_home shadow-lg md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent md:shadow-none">


                    <li class="flex items-center gap-2">
                        <a href="/"
                            class="flex items-center gap-2 after:content-arrow_right after:ml-1 py-2 px-3 font-bold <?php echo $_SERVER['REQUEST_URI'] == '/' ? 'text-blue-600 ' : 'text_colors_home' ?> rounded md:bg-transparent md:p-0"
                            aria-current="page">Home</a>
                    </li>

                    <li class="flex items-center gap-2">
                        <a href="/addBook"
                            class="flex items-center gap-2 after:content-arrow_right after:ml-1 py-2 font-bold <?php echo $_SERVER['REQUEST_URI'] == '/addBook' ? 'text-blue-600 ' : 'text_colors_home' ?> px-3 rounded hover-color md:hover:bg-transparent md:border-0 md:p-0">
                            Events</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <a href="/myListings/<?php echo isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : '' ; ?>"
                            class="flex items-center gap-2 after:content-arrow_right after:ml-1 py-2 font-bold <?php echo str_contains($_SERVER['REQUEST_URI'], '/myListings') ? 'text-blue-600 ' : 'text_colors_home' ?> px-3 rounded hover-color md:hover:bg-transparent md:border-0 md:p-0">
                            Schedule</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <a href="/myRequests/<?php echo isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : '' ; ?>"
                            class="flex items-center gap-2 after:content-arrow_right after:ml-1 py-2 font-bold <?php echo str_contains($_SERVER['REQUEST_URI'], '/myRequests') ? 'text-blue-600 ' : 'text_colors_home' ?> px-3 rounded hover-color md:hover:bg-transparent md:border-0 md:p-0">
                            Personal Plan</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <button id="themeToggle" type="button"
                            class="p-2 rounded-full hover-color focus:outline-none focus:ring-2 focus:ring-blue-500"
                            title="Toggle theme">
                            <svg id="sunIcon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg id="moonIcon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </li>


                </ul>
            </div>
            <!-- Search form -->
            <!-- User menu button -->
            <div class="flex flex-row gap-2">
                <div class="relative flex flex-row gap-2 items-center">
                    <button id="userMenuButton" type="button" aria-expanded="false"
                        class="flex items-center gap-2 after:content-arrow_right after:ml-1 before:content-accessibility_icon before:mr-1 py-2 font-bold <?php echo str_contains($_SERVER['REQUEST_URI'], '/myListings') ? 'text-blue-600 ' : 'text_colors_home' ?> px-3 rounded hover-color md:hover:bg-transparent md:border-0 md:p-0">
                        Accessibility
                        <span class="sr-only">Accessibility Toggle</span>
                    </button>
                    <!-- Dropdown -->
                    <div id="userMenuDropdown" class="absolute right-0 top-full mt-2 w-44 z-50 hidden
                       rounded-lg border border-[#2C3233]
                       bg-[#F2F0EF] dark:bg-[#0F0F0F] shadow-lg">
                        <ul class="py-1 text-sm text-black dark:text-white" aria-labelledby="userMenuButton">
                            <li>
                                <a href="/settings" class="block px-4 py-2 hover-color rounded-md">
                                    Settings
                                </a>
                            </li>
                            <li>
                                <form action="/logout" method="post">
                                    <button type="submit" class="w-full text-left px-4 py-2 hover-color rounded-md">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <select name="language" id="language-select">
                    <selectedcontent></selectedcontent>
                    <option default value="EN"><img src="/Assets/Nav/EnglishIcon.png" alt="EnglishIcon"> English
                    </option>
                    <option value="NL"><img src="/Assets/Nav/DutchIcon.png" alt="DutchIcon"> Dutch</option>
                    <option value="DE"><img src="/Assets/Nav/GermanIcon.png" alt="GermanIcon"> German</option>
                    <option value="FR"><img src="/Assets/Nav/FrenchIcon.png" alt="FrenchIcon"> French</option>
                </select>

            </div>
            <div class="relative flex flex-row gap-2 items-center">
                <button id="userMenuButton" type="button" aria-expanded="false"
                    class="inline-flex items-center text-colors font-semibold p-2 rounded-full
                   bg-[#CBCBCB] dark:bg-[#222222] hover:bg-[#b5b5b5] dark:hover:bg-[#3a3a3a] focus:outline-none focus:ring-2 focus:ring-brand">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-muted">
                        L
                        <!-- <?= strtoupper(substr($_SESSION['loggedInUser']->fname, 0, 1) . substr($_SESSION['loggedInUser']->lname, 0, 1)); ?> -->
                    </span>
                    <span class="sr-only">Toggle user menu</span>
                </button>
                <!-- Dropdown -->
                <div id="userMenuDropdown" class="absolute right-0 top-full mt-2 w-44 z-50 hidden
                   rounded-lg border border-[#2C3233]
                   bg-[#F2F0EF] dark:bg-[#0F0F0F] shadow-lg">
                    <ul class="py-1 text-sm text-black dark:text-white" aria-labelledby="userMenuButton">
                        <li>
                            <a href="/settings" class="block px-4 py-2 hover-color rounded-md">
                                Settings
                            </a>
                        </li>
                        <li>
                            <form action="/logout" method="post">
                                <button type="submit" class="w-full text-left px-4 py-2 hover-color rounded-md">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                <span class="text-colors font-medium whitespace-nowrap">
                    <span id="userTokens"></span> Credits
                </span>
            </div>

        </div>



        </div>

    </nav>

    <main class="flex-1">
        <?php echo $content; ?>
    </main>

    <footer class="flex flex-col gap-4 px-4 py-6 bg_colors_home">
        <div class="flex flex-row justify-around flex-wrap gap-8 border-b-2  border-[#B18132] pb-4">
            <article class="flex flex-col">
                <img src="/Assets/Nav/FestivalLogo.svg" alt="FestivalLogo" class="w-32">
                <p class="text-[#000000CC] w-44">Experience the vibrant culture and history of Haarlem through our
                    exciting
                    festival events.</p>
            </article>
            <article class="flex flex-col">
                <h3 class="font-bold text-xl mb-2 text-[#B18132]">Quick Links</h3>
                <ul>
                    <li><a href="" class="hover:underline font-bold text-base text-[#000000CC]">Home</a></li>
                    <li><a href="" class="hover:underline font-bold text-base text-[#000000CC]">Events</a></li>
                    <li><a href="" class="hover:underline font-bold text-base text-[#000000CC]">Schedule</a></li>
                    <li><a href="" class="hover:underline font-bold text-base text-[#000000CC]">Personal Plan</a></li>
                </ul>
            </article>
            <article class="flex flex-col">
                <h3 class="font-bold text-xl mb-2 text-[#B18132]">Contact Us</h3>
                <div class="flex flex-col">
                    <span class="text-[#000000CC] before:content-footer_location before:mr-2">Haarlem,
                        Netherlands</span>
                    <span class="text-[#000000CC] before:content-footer_phone before:mr-2">+31 (0)23 123 4567</span>
                    <span class="text-[#000000CC] before:content-footer_mail before:mr-2">info@haarlemfestival.nl</span>
                </div>
            </article>
            <article class="flex flex-col">
                <h3 class="font-bold text-xl mb-2 text-[#B18132]">Follow Us</h3>
                <div class="flex flex-row gap-4">
                    <a href="" class="hover:underline font-bold text-base text-[#000000CC]"><img
                            src="/Assets/Nav/FooterFacebook.svg" alt="Facebook"></a>
                    <a href="" class="hover:underline font-bold text-base text-[#000000CC]"><img
                            src="/Assets/Nav/FooterTwitter.svg" alt="Twitter"></a>
                    <a href="" class="hover:underline font-bold text-base text-[#000000CC]"><img
                            src="/Assets/Nav/FooterInstagram.svg" alt="Instagram"></a>
                </div>
                <p>Stay updated with the latest festival news and events!</p>
            </article>
        </div>

        <article class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <p class="text-sm text-[#00000099]">&copy; 2025 Haarlem Festival. All rights reserved. </p>
            <ul class="flex flex-row gap-2">
                <li><a href="" class="hover:underline text-sm text-[#00000099]">Privacy Policy</a></li>
                <li><a href="" class="hover:underline text-sm text-[#00000099]">Terms of Service</a></li>
                <li><a href="" class="hover:underline text-sm text-[#00000099]">Accessibility</a></li>
            </ul>
        </article>
    </footer>
    <script src="/Js/Navbar.js"></script>
    <script src="/Js/Theme.js"></script>
</body>

</html>