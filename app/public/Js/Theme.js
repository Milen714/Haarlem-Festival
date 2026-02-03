const themeToggle = document.getElementById('themeToggle');

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            const newTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';

            // Save to cookie via PHP endpoint
            fetch('/setTheme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'theme=' + newTheme
            });
        });
    }