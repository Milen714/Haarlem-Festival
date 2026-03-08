const passwordInput = document.getElementById("password");

    function checkPasswordStrength() {
        const password = passwordInput.value;

        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[\W_]/.test(password)
        };

        document.getElementById("length").classList.toggle("valid", checks.length);
        document.getElementById("uppercase").classList.toggle("valid", checks.uppercase);
        document.getElementById("lowercase").classList.toggle("valid", checks.lowercase);
        document.getElementById("number").classList.toggle("valid", checks.number);
        document.getElementById("special").classList.toggle("valid", checks.special);

        return Object.values(checks).every(Boolean);
    }
    passwordInput.addEventListener("input", checkPasswordStrength);

    function displayPasswordStrength() {
        const passwordGroup = document.querySelector('.password-group');
        const container = document.createElement('div');
        container.classList.add('password-strength');
        container.innerHTML = `
            <p id="length" class="valid">At least 8 characters</p>
            <p id="uppercase" class="valid">Contains an uppercase letter</p>
            <p id="lowercase" class="valid">Contains a lowercase letter</p>
            <p id="number" class="valid">Contains a number</p>
            <p id="special" class="valid">Contains a special character</p>
        `;
        passwordGroup.appendChild(container);
    }
    displayPasswordStrength();