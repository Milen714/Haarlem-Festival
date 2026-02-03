<?php

namespace App\Views\Account;
// require_once 'config.php';

if (isset($param)) {
        echo htmlspecialchars($param);
    }
?>
<h1 class="text-center m-5 font-serif">Forgot Password?</h1>
<article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <form method='POST' action='/forgot-password'>
        <article class="input_group">
            <label class="input_label" for="email">Email:</label>
            <input class="form_input" type="email" id="email" name="email" required>
        </article>
        <?php if (isset($error)): ?>
        <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        <button class="button_primary" type="submit">Reset Password</button>
    </form>


</article>