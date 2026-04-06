<?php
namespace App\Views\Cms\Users\Partials;
?>
<?php if (isset($_SESSION['success'])): ?>
<div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
    <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
</div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>
