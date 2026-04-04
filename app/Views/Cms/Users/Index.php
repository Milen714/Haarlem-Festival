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

    <?php include __DIR__ . '/Partials/success-message.php'; ?>

    <?php if (empty($users)): ?>
        <?php include __DIR__ . '/Partials/empty-state.php'; ?>
    <?php else: ?>
        <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
            <table class="w-full">
                <?php include __DIR__ . '/Partials/table-header.php'; ?>
                <tbody class="divide-y">
                    <?php foreach ($users as $user): ?>
                        <?php 
                            $user = $user; // Make user available to partial
                            include __DIR__ . '/Partials/user-row.php'; 
                        ?>
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