<?php 
namespace App\Views\Account;

if (isset($param)) {
        echo htmlspecialchars($param);
    }
?>
<h1 class="text-center m-5 font-serif">Reset Password</h1>
<article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <form method='POST' action='/reset-password'>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
        <article class="input_group">
            <label class="input_label" for="password">New Password:</label>
            <input class="form_input" type="password" id="password" name="password" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="password">Repeat New Password:</label>
            <input class="form_input" type="password" id="password" name="repeatPassword" required>
        </article>
        <?php if (isset($error)): ?>
        <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        <button class="button_primary" type="submit">Set New Password</button>
    </form>
</article>