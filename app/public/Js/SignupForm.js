
    const passwordChecklist = document.querySelectorAll('.password-strength p');
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('spinner');
    const signUpForm = document.getElementById("signupForm");
    const autoLoginForm = document.getElementById("autoLoginForm")?? null;
    const hiddenEmail = document.getElementById("hidden-email");
    const hiddenPassword = document.getElementById("hidden-password");


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
                if (autoLoginForm) {
                    autoLoginForm.submit();
                }
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