<?php if (!empty($error)): ?>
<div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
    <p class="font-medium">✗ <?= htmlspecialchars($error) ?></p>
</div>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
<div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
    <p class="font-medium">✓ <?= htmlspecialchars($_SESSION['success']) ?></p>
</div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
<div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
    <p class="font-medium">✗ <?= htmlspecialchars($_SESSION['error']) ?></p>
</div>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>