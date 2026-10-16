document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const phone = document.getElementById('phone').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Validation Alert: Passwords do not match.');
                return false;
            }

            if (!/^\d{10}$/.test(phone)) {
                e.preventDefault();
                alert('Validation Alert: Phone number must contain exactly 10 digits.');
                return false;
            }
        });
    }

    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', (e) => {
            const dateInput = document.getElementById('appointment_date').value;
            if (!dateInput) return;

            const selectedDate = new Date(dateInput);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                e.preventDefault();
                alert('Validation Alert: Consultation date cannot be set in the past.');
                return false;
            }
        });
    }

    const alerts = document.querySelectorAll('.alert');
    alerts.forEach((alertBox) => {
        setTimeout(() => {
            alertBox.style.transition = 'opacity 0.6s ease';
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 600);
        }, 5000);
    });
});