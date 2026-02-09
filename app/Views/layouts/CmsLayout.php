<?php
$theme = $_COOKIE['theme'] ?? 'light';
$darkClass = $theme === 'dark' ? 'dark' : '';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $darkClass ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $title ?? 'CMS Dashboard'; ?></title>
    <link rel="icon" type="image/svg+xml" href="/Assets/Favicon.svg">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://cdn.tiny.cloud/1/izaw3cfuhwr9o3hizpp56ktag8lop5clrfm2dilay7rrctej/tinymce/8/tinymce.min.js"
        referrerpolicy="origin" crossorigin="anonymous"></script>
    <style>
    .cms-nav-link:hover {
        background-color: #374151;
        color: #ffffff;
    }

    #cms-sidebar {
        background-color: #1E293B;
    }

    .dark #cms-sidebar {
        background-color: #0F0F0F;
    }
    </style>
</head>

<body class="min-h-screen bg-[#F1F5F9] dark:bg-[#0F172A]">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside id="cms-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full"
            style="background-color: #1E293B;">

            <!-- Sidebar Header / Logo -->
            <div class="flex items-center justify-between h-16 px-4" style="border-bottom: 1px solid #374151;">
                <a href="/cms" class="flex items-center space-x-2">
                    <img src="/Assets/Nav/FestivalLogo.svg" class="h-8" alt="Haarlem Festival Logo" />
                </a>
                <button id="closeSidebar" style="color: #9CA3AF;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <button id="sidebarMenuToggle" class="hidden" style="color: #9CA3AF;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">

                <!-- Dashboard -->
                <a href="/cms" class="cms-nav-link flex items-center px-4 py-2 rounded-lg" style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Events Section -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold uppercase tracking-wider" style="color: #6B7280;">Events</p>
                </div>

                <a href="/cms/events" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>All Events</span>
                </a>

                <a href="/cms/events/create" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add New Event</span>
                </a>
                <a href="/wysiwyg-demo" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Wysiwyg Demo</span>
                </a>

                <a href="/cms/categories" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>Categories</span>
                </a>

                <!-- Content Section -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold uppercase tracking-wider" style="color: #6B7280;">Content</p>
                </div>

                <a href="/cms/pages" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Pages</span>
                </a>

                <a href="/cms/media" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Media Library</span>
                </a>

                <!-- Users Section -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold uppercase tracking-wider" style="color: #6B7280;">Users</p>
                </div>

                <a href="/cms/users" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>All Users</span>
                </a>

                <a href="/cms/users/create" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Add New User</span>
                </a>

                <!-- Settings Section -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold uppercase tracking-wider" style="color: #6B7280;">Settings</p>
                </div>

                <a href="/cms/settings" class="cms-nav-link flex items-center px-4 py-2 rounded-lg"
                    style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>General Settings</span>
                </a>

            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4" style="border-top: 1px solid #374151;">
                <a href="/" class="cms-nav-link flex items-center px-4 py-2 rounded-lg" style="color: #9CA3AF;">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Back to Site</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden lg:ml-64">

            <!-- Top Header Bar -->
            <header class="flex items-center justify-between h-16 px-6"
                style="background-color: #ffffff; border-bottom: 1px solid #E5E7EB;">

                <!-- Left side: Menu toggle & Breadcrumb -->
                <div class="flex items-center space-x-4">
                    <button id="openSidebar"
                        class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <nav class="hidden sm:flex items-center space-x-2 text-sm">
                        <a href="/cms"
                            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white">Dashboard</a>
                        <?php if (isset($breadcrumb)): ?>
                        <?php foreach ($breadcrumb as $item): ?>
                        <span class="text-gray-400">/</span>
                        <span class="text-gray-700 dark:text-gray-200"><?php echo $item; ?></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </nav>
                </div>

                <!-- Right side: Actions -->
                <div class="flex items-center space-x-4">

                    <!-- Theme Toggle -->
                    <button id="cmsThemeToggle" type="button"
                        class="p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-[#334155] focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg id="cmsSunIcon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg id="cmsMoonIcon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- Notifications -->
                    <button
                        class="relative p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-[#334155]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Menu -->
                    <div class="relative">
                        <button id="cmsUserMenuButton"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-[#334155]">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#A7C957] text-white font-semibold">
                                <?php echo isset($_SESSION['loggedInUser']) ? strtoupper(substr($_SESSION['loggedInUser']->fname ?? 'A', 0, 1)) : 'A'; ?>
                            </span>
                            <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200">
                                <?php echo isset($_SESSION['loggedInUser']) ? ($_SESSION['loggedInUser']->fname ?? 'Admin') : 'Admin'; ?>
                            </span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div id="cmsUserDropdown"
                            class="absolute right-0 mt-2 w-48 py-2 bg-white dark:bg-[#1E293B] rounded-lg shadow-lg border border-gray-200 dark:border-[#334155] hidden z-50">
                            <a href="/cms/profile"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#334155]">
                                Profile
                            </a>
                            <a href="/cms/settings"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-[#334155]">
                                Settings
                            </a>
                            <hr class="my-2 border-gray-200 dark:border-[#334155]">
                            <form action="/logout" method="post">
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-[#334155]">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6" style="background-color: #F1F5F9;">
                <?php echo $content; ?>
            </main>

        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <script>
    // Sidebar toggle
    const sidebar = document.getElementById('cms-sidebar');
    const openSidebarBtn = document.getElementById('openSidebar');
    const closeSidebarBtn = document.getElementById('closeSidebar');
    const sidebarMenuToggle = document.getElementById('sidebarMenuToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.querySelector('.lg\\:ml-64');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        sidebarOverlay?.classList.remove('hidden');
        if (mainContent) mainContent.classList.add('lg:ml-64');
        closeSidebarBtn?.classList.remove('hidden');
        sidebarMenuToggle?.classList.add('hidden');
    }

    function closeSidebarFn() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.remove('lg:translate-x-0');
        sidebarOverlay?.classList.add('hidden');
        if (mainContent) mainContent.classList.remove('lg:ml-64');
        closeSidebarBtn?.classList.add('hidden');
        sidebarMenuToggle?.classList.remove('hidden');
    }

    openSidebarBtn?.addEventListener('click', openSidebar);
    closeSidebarBtn?.addEventListener('click', closeSidebarFn);
    sidebarMenuToggle?.addEventListener('click', openSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebarFn);

    // User dropdown toggle
    const userMenuBtn = document.getElementById('cmsUserMenuButton');
    const userDropdown = document.getElementById('cmsUserDropdown');

    userMenuBtn?.addEventListener('click', () => {
        userDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
            userDropdown?.classList.add('hidden');
        }
    });

    // Theme toggle
    const themeToggle = document.getElementById('cmsThemeToggle');
    themeToggle?.addEventListener('click', () => {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');

        if (isDark) {
            html.classList.remove('dark');
            document.cookie = 'theme=light; path=/; max-age=31536000';
        } else {
            html.classList.add('dark');
            document.cookie = 'theme=dark; path=/; max-age=31536000';
        }
    });
    </script>
</body>

</html>