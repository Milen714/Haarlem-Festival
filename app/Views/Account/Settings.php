<?php
$user = $user ?? null;
?>

<section class="container mx-auto max-w-[1200px] px-4 mt-16 mb-32 font-montserrat">
    
    <div class="mb-12 text-center">
        <h2 class="text-[2rem] md:text-[2.5rem] font-bold text-[var(--text-home-primary)] mb-4 uppercase tracking-wide">
            Account Settings
        </h2>
        <div class="h-1 w-24 bg-[var(--home-gold-accent)] mx-auto rounded-full"></div> 
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg text-sm font-semibold">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg text-sm font-semibold">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row gap-8 items-start">

        <div class="w-full lg:w-3/5 bg-white border-t-4 border-[var(--home-gold-accent)] rounded-xl p-6 md:p-10 shadow-lg h-fit">
            
            <div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-bold text-[var(--text-home-primary)]">Personal Information</h2>
                
                <button type="button" id="btn-edit" class="text-[var(--home-gold-accent)] hover:text-[var(--text-home-high-contrast-primary)] font-bold transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </button>
            </div>

            <form id="profile-form" action="/account/settings/update" method="POST" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-1/2">
                        <label class="block text-sm font-bold text-gray-600 uppercase tracking-wider mb-2">First Name</label>
                        <input type="text" name="fname" value="<?= htmlspecialchars($user->fname ?? '') ?>" disabled required
                               class="profile-input block w-full bg-gray-100 py-2 px-3 rounded-md text-base text-black focus:ring-2 focus:ring-[var(--home-gold-accent)] focus:outline-none transition-all
                                      disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-[var(--text-home-primary)] disabled:font-semibold">
                    </div>

                    <div class="w-full sm:w-1/2">
                        <label class="block text-sm font-bold text-gray-600 uppercase tracking-wider mb-2">Last Name</label>
                        <input type="text" name="lname" value="<?= htmlspecialchars($user->lname ?? '') ?>" disabled required
                               class="profile-input block w-full bg-gray-100 py-2 px-3 rounded-md text-base text-black focus:ring-2 focus:ring-[var(--home-gold-accent)] focus:outline-none transition-all
                                      disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-[var(--text-home-primary)] disabled:font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-600 uppercase tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" disabled required
                           class="profile-input block w-full bg-gray-100 py-2 px-3 rounded-md text-base text-black focus:ring-2 focus:ring-[var(--home-gold-accent)] focus:outline-none transition-all
                                  disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-[var(--text-home-primary)] disabled:font-semibold">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-600 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>" disabled
                           class="profile-input block w-full bg-gray-100 py-2 px-3 rounded-md text-base text-black focus:ring-2 focus:ring-[var(--home-gold-accent)] focus:outline-none transition-all
                                  disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-[var(--text-home-primary)] disabled:font-semibold">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-600 uppercase tracking-wider mb-2">Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($user->address ?? '') ?>" disabled
                           class="profile-input block w-full bg-gray-100 py-2 px-3 rounded-md text-base text-black focus:ring-2 focus:ring-[var(--home-gold-accent)] focus:outline-none transition-all
                                  disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-[var(--text-home-primary)] disabled:font-semibold">
                </div>

                <div id="action-buttons" class="hidden gap-4 pt-6 border-t border-gray-200 mt-8">
                    <button type="submit" class="bg-[var(--text-home-primary)] hover:bg-[var(--text-home-high-contrast-primary)] text-white font-semibold py-2 px-8 rounded-md transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--text-home-primary)]">
                        Save Changes
                    </button>
                    <button type="button" id="btn-cancel" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-8 rounded-md transition-colors shadow-sm">
                        Cancel
                    </button>
                </div>

            </form>
        </div>

        <div class="w-full lg:w-2/5 flex flex-col gap-8">
            
            <div class="bg-white border-t-4 border-[var(--home-gold-accent)] rounded-xl p-6 shadow-lg">
                <h3 class="text-xl font-bold text-[var(--text-home-primary)] mb-2">Password</h3>
                <p class="text-sm text-gray-600 mb-6">Manage your password separately for security reasons.</p>
                
                <a href="/forgot-password" class="inline-block w-fit px-6 py-3 border-2 border-[var(--home-gold-accent)] text-[var(--home-gold-accent)] hover:bg-[var(--home-gold-accent)] hover:text-white rounded-md font-bold transition-all">
                    Change Password
                </a>
            </div>

            <div class="bg-white border-t-4 border-[var(--home-gold-accent)] rounded-xl p-6 shadow-lg">
                <h3 class="text-xl font-bold text-[var(--text-home-primary)] mb-6">Quick Actions</h3>
                
                <div class="flex items-start gap-4">
                    <a href="/personal-program" class="home_calendar_button_active flex justify-center items-center gap-2 px-6 py-3 w-fit">
                        My Personal Program
                    </a>

                    <?php if (!($user->is_verified ?? false)): ?>
                    <a href="/verify-account" class="home_calendar_button_inactive flex justify-center items-center gap-2 px-6 py-3 w-fit">
                        Validate My Account
                    </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('profile-form');
    const inputs = document.querySelectorAll('.profile-input');
    const btnEdit = document.getElementById('btn-edit');
    const btnCancel = document.getElementById('btn-cancel');
    const actionButtons = document.getElementById('action-buttons');

    btnEdit.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = false);
        btnEdit.classList.add('hidden');
        actionButtons.classList.remove('hidden');
        actionButtons.classList.add('flex');
        inputs[0].focus();
    });

    btnCancel.addEventListener('click', () => {
        form.reset();
        inputs.forEach(input => input.disabled = true);
        btnEdit.classList.remove('hidden');
        actionButtons.classList.remove('flex');
        actionButtons.classList.add('hidden');
    });
});
</script>