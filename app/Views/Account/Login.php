<?php
namespace App\Views\Home;

?>
<h1 class="text-center m-5 font-serif">Login Page</h1>
<article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <form method='POST' action='/login'>
        <div id="error-container">
        </div>
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
        <button data-redirect="/" class="button_primary" type="submit">Login</button>
    </form>
    <article class="mt-4 text-center text-black">
        <p>Don't have an account? <a class="text-gray-600" href="/signup">Sign up here</a>.</p>
        <p>Forgot your password? <a class="text-gray-600" href="/forgot-password">Reset it here</a>.</p>
    </article>

</article>

<script src="/Js/ShowError.js"></script>
<script>
const loginForm = document.querySelector('form');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const redirectData = loginForm.querySelector('button[type="submit"]').dataset.redirect;

loginForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: emailInput.value,
                password: passwordInput.value,
                redirect: redirectData
            })
        });

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            throw new Error('Server returned an unexpected response format.');
        }

        const result = await response.json();
        if (result.success) {
            window.location.href = result.redirect || '/';
        } else {
            showError(result.message || 'Login failed');
        }
    } catch (error) {
        showError(error.message || 'Something went wrong while logging in. Please try again.');
    }
});
</script>