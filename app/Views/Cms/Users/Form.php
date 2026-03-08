<?php
namespace App\Views\Cms\Users;

use App\Models\User;
use App\Models\Enums\UserRole;

/** @var User|null $user */
$user = $user ?? null;
$isEdit = $user !== null;
$pageTitle = $isEdit ? "Edit User: " . htmlspecialchars($user->email) : "Create New User";
$action = $action ?? '/cms/users/store';
?>

<section class="p-8 max-w-4xl mx-auto">
    <header class="mb-8">
        <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
            <?= $pageTitle ?>
        </h1>
        <p class="text-gray-600">
            <?= $isEdit ? 'Update account details and permissions' : 'Register a new administrative or customer account' ?>
        </p>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
        <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
        <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form id="user-form" method="POST" action="<?= htmlspecialchars($action) ?>">

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Account Credentials</h2>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="email">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required <?= $isEdit ? 'readonly class="bg-gray-50"' : '' ?>>
                <?php if($isEdit): ?>
                <p class="text-xs text-gray-500 mt-1">Email addresses cannot be changed after creation.</p>
                <?php endif; ?>
            </div>

            <div class="mb-4 password-group">
                <label class="block text-gray-700 font-semibold mb-2" for="password">
                    <?= $isEdit ? 'New Password' : 'Password' ?>
                    <?= $isEdit ? '' : '<span class="text-red-500">*</span>' ?>
                </label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    <?= $isEdit ? '' : 'required' ?>>
                <?php if($isEdit): ?>
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Personal Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="fname">First Name</label>
                    <input type="text" id="fname" name="fname" value="<?= htmlspecialchars($user->fname ?? '') ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="lname">Last Name</label>
                    <input type="text" id="lname" name="lname" value="<?= htmlspecialchars($user->lname ?? '') ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2" for="address">Address</label>
                <textarea id="address" name="address" rows="2"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($user->address ?? '') ?></textarea>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-6 mb-6 shadow-sm">
            <h2 class="text-xl font-bold mb-4 border-b pb-2 text-indigo-800">Permissions & Status</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="role">User Role <span
                            class="text-red-500">*</span></label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="CUSTOMER" <?= ($user?->role->value === 'CUSTOMER') ? 'selected' : '' ?>>Customer
                        </option>
                        <option value="EMPLOYEE" <?= ($user?->role->value === 'EMPLOYEE') ? 'selected' : '' ?>>Employee
                        </option>
                        <option value="ADMIN" <?= ($user?->role->value === 'ADMIN') ? 'selected' : '' ?>>Administrator
                        </option>
                    </select>
                </div>

                <div class="flex flex-col gap-4">
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_active" class="sr-only"
                                <?= ($user?->is_active ?? true) ? 'checked' : '' ?>>
                            <div class="w-10 h-6 bg-gray-200 rounded-full transition-colors"></div>
                            <div
                                class="dot absolute w-5 h-4 bg-white rounded-full shadow inset-y-2 left-1 top-1 transition-transform">
                            </div>
                        </div>
                        <div class="ml-1 text-gray-700 font-semibold">Account Active</div>
                    </label>

                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_verified" class="sr-only"
                                <?= ($user?->is_verified ?? false) ? 'checked' : '' ?>>
                            <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner px-1"></div>
                            <div
                                class="dot absolute w-5 h-4 bg-white rounded-full shadow inset-y-2 left-1 top-1 transition-transform">
                            </div>
                        </div>
                        <div class="ml-1 text-gray-700 font-semibold">Email Verified</div>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                <?= $isEdit ? 'Update User Account' : 'Create User Account' ?>
            </button>
            <a href="/cms/users"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold transition">
                Cancel
            </a>
        </div>
    </form>
</section>

<style>
/* Simple CSS for the toggle switches */
input:checked~.dot {
    transform: translateX(100%);
    background-color: #3B82F6;
}

input:checked~.bg-gray-200 {
    background-color: #DBEAFE;
}
</style>

<script src="/Js/PasswordStrength.js"></script>
<script src="/Js/ShowError.js"></script>
<script>
const passwordInput = document.getElementById('password');
const form = document.getElementById('user-form');
form.addEventListener('submit', (e) => {
    if (passwordInput.value && !checkPasswordStrength()) {
        e.preventDefault();
        showError('Password does not meet the strength requirements.');
    }
});
</script>