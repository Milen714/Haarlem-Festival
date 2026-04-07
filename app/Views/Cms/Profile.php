<section class="p-8 max-w-4xl mx-auto">
    
    <header class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Profile Settings
            </h1>
            <p class="text-gray-600">
                Manage your account details
            </p>
        </div>
        <a href="/cms" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-center">
            ← Back to Dashboard
        </a>
    </header>

    <?php include __DIR__ . '/Components/FlashMessages.php'; ?>

    <div class="bg-white border rounded-lg p-6 mb-6">
        
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">Personal Information</h2>
            
            <button type="button" id="btn-edit" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </button>
        </div>

        <form id="profile-form" action="/cms/settings/update" method="POST">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="fname">First Name</label>
                    <input type="text" id="fname" name="fname" value="<?= htmlspecialchars($user->fname ?? '') ?>" disabled required
                           class="profile-input w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all
                                  disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-gray-900 disabled:font-medium">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="lname">Last Name</label>
                    <input type="text" id="lname" name="lname" value="<?= htmlspecialchars($user->lname ?? '') ?>" disabled required
                           class="profile-input w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all
                                  disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-gray-900 disabled:font-medium">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" disabled required
                       class="profile-input w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all
                              disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-gray-900 disabled:font-medium">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>" disabled
                       class="profile-input w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all
                              disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-gray-900 disabled:font-medium">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="address">Address</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user->address ?? '') ?>" disabled
                       class="profile-input w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all
                              disabled:bg-transparent disabled:border-transparent disabled:shadow-none disabled:px-0 disabled:text-gray-900 disabled:font-medium">
            </div>

            <div id="action-buttons" class="hidden gap-4 pt-6 border-t mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                    Save Changes
                </button>
                <button type="button" id="btn-cancel" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold transition">
                    Cancel
                </button>
            </div>

        </form>
    </div>

    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold mb-1 text-gray-800">Password</h2>
                <p class="text-sm text-gray-600">Manage your password separately for security reasons.</p>
            </div>
            <a href="/forgot-password" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold transition whitespace-nowrap">
                Change Password
            </a>
        </div>
    </div>     

</section>

<script src="/Js/ProfileEdit.js"></script>