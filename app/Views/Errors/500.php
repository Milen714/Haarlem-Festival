<?php
namespace App\Views\Errors;
$theme = $_COOKIE['theme'] ?? 'light';
$darkClass = $theme === 'dark' ? 'dark' : '';
?>


<body class="flex flex-col min-h-screen bg_colors_home">
    <main class="flex-grow flex items-center justify-center px-6 py-24">
        <div class="text-center">
            <h1 class="text-9xl font-bold text-[var(--home-gold-accent)] mb-4">500</h1>

            <h2 class="text-3xl font-semibold text_colors_home mb-2">Something Went Wrong</h2>
            <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">
                Sorry, an unexpected error occurred while processing your request. Please try again.
            </p>

            <?php if (!empty($error)): ?>
            <p class="text-sm text-red-500 mb-8 max-w-2xl mx-auto break-words">
                <?= htmlspecialchars((string)$error) ?>
            </p>
            <?php endif; ?>

            <a href="/"
                class="inline-flex items-center gap-2 text-white font-semibold px-6 py-3 rounded-[5px]
                      bg-[var(--text-home-primary)] hover:bg-[var(--text-home-high-contrast-primary)]
                      shadow-[-7px_-7px_0px_var(--home-gold-accent)] hover:shadow-[-4px_-4px_0px_var(--home-gold-accent)]
                      transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--home-gold-accent)]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </a>
        </div>
    </main>
</body>

</html>