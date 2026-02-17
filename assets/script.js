// Simple Contact Form - Frontend JS
// Basic validation and honeypot protection

document.addEventListener('DOMContentLoaded', function () {

    const forms = document.querySelectorAll('.sucf-form');

    forms.forEach(function (form) {

        form.addEventListener('submit', function (e) {

            let valid = true;
            const errors = [];

            // Honeypot
            const hp = form.querySelector('.sucf-hp');
            if (hp && hp.value.trim() !== '') {
                e.preventDefault();
                return false; // suspected bot
            }

            // Email validation
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.required) {
                const email = emailField.value.trim();
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(email)) {
                    errors.push('Please enter a valid email.');
                    valid = false;
                }
            }

            // Name validation
            const nameField = form.querySelector('input[name="sucf_name"]');
            if (nameField && nameField.required && !nameField.value.trim()) {
                errors.push('Please enter your name.');
                valid = false;
            }

            // Message validation
            const msgField = form.querySelector('textarea[name="sucf_message"]');
            if (msgField && msgField.required && !msgField.value.trim()) {
                errors.push('Please enter a message.');
                valid = false;
            }

            if (!valid) {
                alert(errors.join('\n'));
                e.preventDefault();
                return false;
            }

        });
    });

});
