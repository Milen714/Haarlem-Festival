<?php 
namespace App\Views\Account;

if (isset($param)) {
        echo htmlspecialchars($param);
    }
?>
<h1 class="text-center m-5 font-serif">Reset Password</h1>
<article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">

    <div id="error-container">
    </div>
    <?php if (isset($error)): ?>
    <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form id="password-reset-form" method='POST' action='/reset-password'>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
        <article class="input_group password-group">
            <label class="input_label" for="password">New Password:</label>
            <input class="form_input" type="password" id="password" name="password" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="password-repeat">Repeat New Password:</label>
            <input class="form_input" type="password" id="password-repeat" name="repeatPassword" required>
        </article>

        <button class="button_primary" type="submit">Set New Password</button>
    </form>
</article>
<script src="/Js/PasswordStrength.js"></script>
<script src="/Js/ShowError.js"></script>
<script>
const form = document.getElementById('password-reset-form');
form.addEventListener('submit', (e) => {
    const password = document.getElementById('password').value;
    const repeatPassword = document.getElementById('password-repeat').value;

    if (!checkPasswordStrength()) {
        showError('Password does not meet the strength requirements.');
        e.preventDefault();
        return;
    }

    if (password !== repeatPassword) {
        e.preventDefault();
        showError("Passwords do not match. Please try again.");
    }
});
</script>