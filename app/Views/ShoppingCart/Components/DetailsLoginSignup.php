<?php
namespace App\Views\ShoppingCart\Components;
use App\config\Secrets;
?>

<section class="w-full md:w-[60%] bg-white p-6 rounded-lg shadow-sm border border-gray-100">

    <section class="relative w-full pb-5 border-b border-dashed border-black">
        <form id="login-form" class="flex flex-col gap-2" method='POST' action='/login'>
            <article class="input_group">
                <label class="input_label" for="email">Email:</label>
                <input class="form_input" type="email" id="email" name="email" required>
            </article>
            <article class="input_group">
                <label class="input_label" for="password">Password:</label>
                <input class="form_input" type="password" id="password" name="password" required>
            </article>
            <button data-redirect="/payment-details" type="submit" class="checkout_login_button mb-3">Login &
                Continue</button>
        </form>
        <span class="absolute bottom-[-8.5%] left-[40%] bg-[#CECECE] px-1 py-2 rounded-lg font-semibold text-black">OR
            Register</span>
    </section>
    <?php include __DIR__ . '/../../Account/Signup.php'; ?>

</section>

<script>
const authFormsSection = document.getElementById('auth-Forms');
const loginForm = document.getElementById('login-form');
const loginEmailInput = document.getElementById('email');
const loginPasswordInput = document.getElementById('password');
const loginSubmitButton = loginForm.querySelector('button[type="submit"]');
const redirectData = loginForm.querySelector('button[type="submit"]').dataset.redirect;

loginForm.addEventListener('submit', submitLoginForm);

async function submitLoginForm(event) {
    event.preventDefault();
    loginSubmitButton.disabled = true;
    let data = {
        email: loginEmailInput.value,
        password: loginPasswordInput.value,
        redirect: redirectData
    };

    const response = async () => {
        const res = await fetch('<?php Secrets::$domain ?>/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        return result;
    };
    try {
        const result = await response();
        if (result.success) {
            window.location.href = result.redirect || '/payment-details';
            authFormsSection.classList.add('animate-fadeOut');
        } else {
            loginEmailInput.value = result.user?.email || loginEmailInput.value;
            showError(result.message || 'Login failed. Please check your credentials and try again.');
        }
    } catch (error) {
        showError('Something went wrong while logging in. Please try again.');
    } finally {
        loginSubmitButton.disabled = false;
    }
}
</script>