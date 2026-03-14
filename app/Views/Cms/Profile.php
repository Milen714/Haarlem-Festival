<?php
$user = $user ?? null;
?>

<section class="container mx-auto max-w-[1100px] px-4 my-24">
    
    <div class="mb-12">
        <h3 class="font-history-serif text-[1.5rem] md:text-[2rem] text-ink-900 font-bold">
            Profile Settings
        </h3>
        <div class="underline-history"></div> 
    </div>

    <div class="bg-white border border-[#CAA359] rounded-[0.5rem] p-6 md:p-10 shadow-sm max-w-3xl">
        
        <div class="flex justify-between items-center mb-8 border-b border-[#CAA359] pb-4">
            <h2 class="font-history-serif text-2xl font-bold text-ink-900">Personal Information</h2>
            
            <button type="button" id="btn-edit" class="text-[#546A21] hover:text-[#465e10] font-semibold transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </button>
        </div>

        <form id="profile-form" action="/settings/update" method="POST" class="space-y-6">
            
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-sm font-bold text-ink-700 uppercase tracking-wider mb-1">First Name</label>
                    <input type="text" name="fname" value="<?= htmlspecialchars($user->fname ?? '') ?>" disabled required
                           class="profile-input w-full px-4 py-2 text-lg text-ink-900 bg-white border border-[#CAA359] rounded-md focus:ring-2 focus:ring-[#546A21] focus:border-[#546A21] outline-none transition-all
                                  disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-ink-900">
                </div>

                <div class="w-1/2">
                    <label class="block text-sm font-bold text-ink-700 uppercase tracking-wider mb-1">Last Name</label>
                    <input type="text" name="lname" value="<?= htmlspecialchars($user->lname ?? '') ?>" disabled required
                           class="profile-input w-full px-4 py-2 text-lg text-ink-900 bg-white border border-[#CAA359] rounded-md focus:ring-2 focus:ring-[#546A21] focus:border-[#546A21] outline-none transition-all
                                  disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-ink-900">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-ink-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" disabled required
                       class="profile-input w-full px-4 py-2 text-lg text-ink-900 bg-white border border-[#CAA359] rounded-md focus:ring-2 focus:ring-[#546A21] focus:border-[#546A21] outline-none transition-all
                              disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-ink-900">
            </div>

            <div>
                <label class="block text-sm font-bold text-ink-700 uppercase tracking-wider mb-1">Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>" disabled
                       class="profile-input w-full px-4 py-2 text-lg text-ink-900 bg-white border border-[#CAA359] rounded-md focus:ring-2 focus:ring-[#546A21] focus:border-[#546A21] outline-none transition-all
                              disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-ink-900">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-ink-700 uppercase tracking-wider mb-1">Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user->address ?? '') ?>" disabled
                       class="profile-input w-full px-4 py-2 text-lg text-ink-900 bg-white border border-[#CAA359] rounded-md focus:ring-2 focus:ring-[#546A21] focus:border-[#546A21] outline-none transition-all
                              disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-ink-900">
            </div>

            <div id="action-buttons" class="hidden flex gap-4 pt-6 border-t border-[#CAA359] mt-8">
                <button type="submit" class="bg-[#546A21] hover:bg-[#465e10] text-white font-semibold py-2 px-6 rounded-md transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#546A21]">
                    Save Changes
                </button>
                <button type="button" id="btn-cancel" class="bg-[#FAEBBD] hover:bg-[#FFE598] text-ink-900 border border-[#CAA359] font-semibold py-2 px-6 rounded-md transition-colors shadow-sm">
                    Cancel
                </button>
            </div>

        </form>

        <div class="mt-12 pt-8">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-history-serif text-xl font-bold text-ink-900">Password</h3>
                    <p class="text-sm text-ink-700">Manage your password separately for security reasons.</p>
                </div>
                <a href="/forgot-password" class="px-5 py-2 bg-[#FFF0C2] border border-[#CAA359] text-ink-900 rounded-md hover:bg-[#FFE598] font-semibold transition-colors shadow-sm">
                    Change
                </a>
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
        inputs[0].focus();
    });

    btnCancel.addEventListener('click', () => {
        form.reset();
        inputs.forEach(input => input.disabled = true);
        btnEdit.classList.remove('hidden');
        actionButtons.classList.add('hidden');
    });
});
</script>