<?php
namespace App\Views\Home;
use App\config\Secrets;

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
<?php 
    if(isset($isSignupPage) && $isSignupPage) {
        echo '<h1 class="text-center m-5 font-serif">Signup Page</h1>';
    }
    ?>

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
        <article class="input_group password-group">
            <label class="input_label" for="password">Password:</label>
            <input class="form_input" type="password" id="password" name="password" required>
        </article>

        <button id="submit-button" class="button_primary" type="submit">Signup</button>

        <?php include __DIR__ . '/../Home/Components/Spinner.php'; ?>
    </form>
    <form id="autoLoginForm" method='POST' action='/login'>
        <input id="hidden-email" type="hidden" name="email" value="">
        <input id="hidden-password" type="hidden" name="password" value="">
        <input id="hidden-redirect" type="hidden" name="redirect" value="">
    </form>



    <script src="/Js/PasswordStrength.js" defer></script>
    <script src="/Js/ShowError.js"></script>
    <script>
    const passwordChecklist = document.querySelectorAll('.password-strength p');
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('spinner');
    const signUpForm = document.getElementById("signupForm");
    const autoLoginForm = document.getElementById("autoLoginForm");
    const hiddenEmail = document.getElementById("hidden-email");
    const hiddenPassword = document.getElementById("hidden-password");
    const hiddenRedirect = document.getElementById("hidden-redirect");


    signUpForm.addEventListener('submit', async function(event) {
        if (!checkPasswordStrength()) {
            showError('Password does not meet the strength requirements.');
            event.preventDefault();
            return;
        }

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
                if (window.location.pathname === '/payment-details') {
                    hiddenRedirect.value = '/payment-details';
                } else {
                    hiddenRedirect.value = '/';
                }

                await autoLoginAfterSignup();
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

    async function autoLoginAfterSignup() {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: hiddenEmail.value,
                password: hiddenPassword.value,
                redirect: hiddenRedirect.value
            })
        });
        const result = await response.json();
        if (result.success) {
            window.location.href = result.redirect || '/';
        } else {
            showError(result.message || 'Login failed. Please check your credentials and try again.');
        }
    }


    async function executeRecaptcha() {
        return new Promise((resolve, reject) => {
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo Secrets::$reCapchaSiteKey ?>', {
                    action: 'signup'
                }).then(function(token) {
                    resolve(token);
                    console.log('reCAPTCHA token obtained:', token);
                }).catch(reject);
            });
        });
    }
    </script>