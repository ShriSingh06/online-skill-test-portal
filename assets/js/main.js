document.addEventListener('DOMContentLoaded', () => {
    // Confirmation for Delete Buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                event.preventDefault();
            }
        });
    });

    // Client-side validation for student registration
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            let isValid = true;

            const fullName = document.getElementById('full_name');
            const email = document.getElementById('email');
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            // Helper to show/hide error messages
            const setError = (element, message) => {
                const errorDisplay = element.nextElementSibling; // Assuming error message is the next sibling
                if (errorDisplay && errorDisplay.classList.contains('error-message')) {
                    errorDisplay.textContent = message;
                    errorDisplay.style.display = message ? 'block' : 'none';
                }
            };

            // Reset errors
            [fullName, email, username, password, confirmPassword].forEach(field => setError(field, ''));

            // Validation logic
            if (fullName.value.trim() === '') {
                setError(fullName, 'Full Name is required.');
                isValid = false;
            }

            if (email.value.trim() === '' || !/\S+@\S+\.\S+/.test(email.value)) {
                setError(email, 'Valid Email is required.');
                isValid = false;
            }

            if (username.value.trim() === '') {
                setError(username, 'Username is required.');
                isValid = false;
            }

            if (password.value.length < 6) {
                setError(password, 'Password must be at least 6 characters.');
                isValid = false;
            }

            if (password.value !== confirmPassword.value) {
                setError(confirmPassword, 'Passwords do not match.');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Toggle button disable state for test start
    const startTestButton = document.getElementById('startTestBtn');
    if (startTestButton) {
        // Simple check to ensure it doesn't stay disabled if there are enough questions
        // The core check is on the server-side, but this gives a better UX.
        const questionCount = parseInt(startTestButton.dataset.questionCount || '0');
        const requiredQuestions = parseInt(startTestButton.dataset.requiredQuestions || '0');

        if (questionCount < requiredQuestions) {
            startTestButton.setAttribute('disabled', 'disabled');
            startTestButton.textContent = `Insufficient Questions (${questionCount}/${requiredQuestions})`;
        } else {
            startTestButton.removeAttribute('disabled');
            startTestButton.textContent = 'Start Test Now';
        }
    }
});