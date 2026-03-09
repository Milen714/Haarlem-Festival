<?php
namespace App\Views\Account;

$rawStatus = $status ?? ($_GET['status'] ?? 'success');
$normalizedStatus = strtolower((string) $rawStatus);
$isSuccess = !in_array($normalizedStatus, ['error', 'failed', 'invalid', 'expired'], true);

$pageTitle = $isSuccess ? 'Account Verified' : 'Verification Failed';
$subtitle = $isSuccess
    ? 'Your email has been verified successfully.'
    : 'We could not verify your account.';

$defaultMessage = $isSuccess
    ? 'Your account is now active. You can sign in and start exploring the Haarlem Festival platform.'
    : 'The verification link may be invalid or expired. Please request a new verification email and try again.';

$displayMessage = $message ?? $error ?? $defaultMessage;
?>

<main class="flex-grow flex items-center justify-center px-6 py-20 bg_colors_home">
    <div class="text-center w-full max-w-2xl">
        <div class="text-[var(--home-gold-accent)] text-7xl font-bold mb-4">
            <?php echo $isSuccess ? '200' : '403'; ?>
        </div>

        <h1 class="text-3xl sm:text-4xl font-semibold text_colors_home mb-3">
            <?php echo htmlspecialchars($pageTitle); ?>
        </h1>

        <h2 class="text-xl sm:text-2xl text_colors_home mb-5">
            <?php echo htmlspecialchars($subtitle); ?>
        </h2>

        <p class="text-base sm:text-lg text-gray-500 mb-10 max-w-xl mx-auto">
            <?php echo htmlspecialchars($displayMessage); ?>
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="/login"
                class="inline-flex items-center gap-2 text-white font-semibold px-6 py-3 rounded-[5px]
                       bg-[var(--text-home-primary)] hover:bg-[var(--text-home-high-contrast-primary)]
                       shadow-[-7px_-7px_0px_var(--home-gold-accent)] hover:shadow-[-4px_-4px_0px_var(--home-gold-accent)]
                       transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--home-gold-accent)]">
                Continue to Login
            </a>

            <?php if (!$isSuccess): ?>
            <a href="/signup"
                class="inline-flex items-center gap-2 text-[var(--text-home-primary)] font-semibold px-6 py-3 rounded-[5px]
                       border border-[var(--text-home-primary)] hover:bg-[var(--home-gold-accent)]/10
                       transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--home-gold-accent)]">
                Back to Signup
            </a>
            <?php endif; ?>
        </div>
    </div>
</main>