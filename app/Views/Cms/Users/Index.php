<?php
namespace App\Views\Cms\Users;

/** @var App\Models\User[] $users */
$users = $users ?? [];
?>

<section class="p-8 max-w-7xl mx-auto">
    <header class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2" style="font-family: 'Cormorant Garamond', serif;">
                Manage Users
            </h1>
            <p class="text-gray-600">Administrative control over platform accounts</p>
        </div>
        <a href="/cms/users/create" 
           class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            + Add New User
        </a>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
        <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <div class="bg-white border rounded-lg p-12 text-center">
            <div class="text-6xl mb-4">👥</div>
            <h3 class="text-2xl font-bold mb-2">No Users Found</h3>
            <p class="text-gray-600 mb-6">Start by creating your first system user</p>
            <a href="/cms/users/create" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                + Add First User
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">User</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Role</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Status</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Joined</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold">
                                    <?= strtoupper(substr($user->fname ?? $user->email, 0, 1)) ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">
                                        <?= htmlspecialchars($user->fname . ' ' . $user->lname) ?>
                                    </p>
                                    <p class="text-sm text-gray-500 truncate"><?= htmlspecialchars($user->email) ?></p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <?php 
                                $roleClass = match($user->role->value) {
                                    'ADMIN' => 'bg-purple-100 text-purple-700',
                                    'EMPLOYEE' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $roleClass ?>">
                                <?= $user->role->value ?>
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="flex items-center gap-1.5 text-sm <?= $user->is_active ? 'text-green-600' : 'text-red-600' ?>">
                                    <span class="w-2 h-2 rounded-full <?= $user->is_active ? 'bg-green-600' : 'bg-red-600' ?>"></span>
                                    <?= $user->is_active ? 'Active' : 'Disabled' ?>
                                </span>
                                <?php if ($user->is_verified): ?>
                                    <span class="text-[10px] text-blue-500 uppercase font-bold tracking-wider">✓ Verified</span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' ?>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex gap-2 justify-end">
                                <a href="/cms/users/edit/<?= $user->id ?>" 
                                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded transition">
                                    Edit
                                </a>
                                <form method="POST" 
                                      action="/cms/users/delete/<?= $user->id ?>" 
                                      onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars($user->email) ?>?');"
                                      class="inline">
                                    <button type="submit" 
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-center text-gray-600">
            Total Users: <strong><?= count($users) ?></strong>
        </div>
    <?php endif; ?>

    <div class="mt-8">
        <a href="/cms" class="text-gray-600 hover:text-gray-900 transition">
            ← Back to Dashboard
        </a>
    </div>
</section>