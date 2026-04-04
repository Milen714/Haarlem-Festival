<?php
namespace App\Views\Cms\Users\Partials;
/** @var App\Models\User $user */
?>
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
