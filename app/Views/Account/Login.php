<?php
namespace App\Views\Home;

?>
<h1 class="text-center m-5 font-serif">Login Page</h1>
<article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <form method='POST' action='/login'>
        <article class="input_group">
            <label class="input_label" for="email">Email:</label>
            <input class="form_input" type="email" id="email" name="email" required>

        </article>
        <article class="input_group">
            <label class="input_label" for="password">Password:</label>
            <input class="form_input" type="password" id="password" name="password" required>

        </article>
        <?php if (isset($error)): ?>
        <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
        <div class="mb-4 p-4 bg-green-300 text-green-900 border border-green-400 rounded">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        <button class="button_primary" type="submit">Login</button>
    </form>
    <article class="mt-4 text-center text-black">
        <p>Don't have an account? <a class="text-gray-600" href="/signup">Sign up here</a>.</p>
        <p>Forgot your password? <a class="text-gray-600" href="/forgot-password">Reset it here</a>.</p>
    </article>

</article>

<?php
    