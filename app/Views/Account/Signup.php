<?php
namespace App\Views\Home;
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

if (isset($param)) {
        echo htmlspecialchars($param);
    }
    
    $user = new \App\Models\User();
if (isset($userModel)) {
    $user = $userModel;
    echo $user->fname;
    echo $user->email;
}



?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $_ENV['RECAPTCHA_SITE_KEY'] ?? ''; ?>">
</script>

<h1 class="text-center m-5 font-serif">Signup Page</h1>
<article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">

    <div id="error-container">
    </div>

    <form id="signupForm" method='POST' action='/signup'>
        <article class="input_group">
            <label class="input_label" for="email">Email:</label>
            <input class="form_input" type="email" id="email" name="email"
                value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="password">Password:</label>
            <input class="form_input" type="password" id="password" name="password" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="fname">First Name:</label>
            <input class="form_input" type="text" id="fname" name="fname"
                value="<?php echo htmlspecialchars($user->fname ?? ''); ?>" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="lname">Last Name:</label>
            <input class="form_input" type="text" id="lname" name="lname"
                value="<?php echo htmlspecialchars($user->lname ?? ''); ?>" required>
        </article>
        <article class="input_group">
            <label class="input_label" for="address">Address:</label>
            <input class="form_input" type="text" id="address" name="address"
                value="<?php echo htmlspecialchars($user->address ?? ''); ?>">
        </article>
        <article class="input_group">
            <label class="input_label" for="phone">Phone:</label>
            <input class="form_input" type="tel" id="phone" name="phone"
                value="<?php echo htmlspecialchars($user->phone ?? ''); ?>">
        </article>
        <article class="input_group">
            <input type="hidden" name="recaptcha_token" id="recaptcha_token">
        </article>
        <button id="submit-button" class="button_primary" type="submit">Signup</button>

        <?php include __DIR__ . '/../Home/Components/Spinner.php'; ?>
    </form>
    <form id="autoLoginForm" method='POST' action='/login'>
        <input id="hidden-email" type="hidden" name="email" value="">
        <input id="hidden-password" type="hidden" name="password" value="">
    </form>




    <script>
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('spinner');
    const signUpForm = document.getElementById("signupForm");
    const autoLoginForm = document.getElementById("autoLoginForm");
    const hiddenEmail = document.getElementById("hidden-email");
    const hiddenPassword = document.getElementById("hidden-password");
    signUpForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        spinner.classList.remove('hidden');
        // Get reCAPTCHA token
        let recaptchaToken = '';
        try {
            recaptchaToken = await executeRecaptcha();
        } catch (e) {
            showError('reCAPTCHA failed. Please try again.');
            spinner.classList.add('hidden');
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            return;
        }
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        data['recaptcha'] = recaptchaToken;
        console.log('Form data to be sent:', data);
        try {
            const response = await fetch('/signup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                hiddenEmail.value = signUpForm.email.value;
                hiddenPassword.value = signUpForm.password.value;
                autoLoginForm.submit();
            } else {
                showError(result.message || 'Signup failed');
            }
        } catch (error) {
            showError('An error occurred: ' + error.message);
        } finally {
            spinner.classList.add('hidden');
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });

    function showError(message) {
        const errorContainer = document.getElementById('error-container');
        errorContainer.innerHTML = `
            <div class="mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
                ${message}
            </div>
        `;
    }

    async function executeRecaptcha() {
        return new Promise((resolve, reject) => {
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo $_ENV['RECAPTCHA_SITE_KEY'] ?>', {
                    action: 'captcha'
                }).then(function(token) {
                    resolve(token);
                    console.log('reCAPTCHA token obtained:', token);
                }).catch(reject);
            });
        });
    }
    </script>