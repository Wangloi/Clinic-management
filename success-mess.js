document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const studentLoginForm = document.getElementById('studentLoginForm');

    // Check if there's an error from PHP
    if (typeof window.loginError !== 'undefined' && window.loginError) {
        Swal.fire({
            icon: 'error',
            title: 'Login Error',
            text: window.loginError,
            confirmButtonText: 'OK'
        });
    }

    // Handle admin form submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            // Basic client-side validation
            if (!username || !password) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please enter both username and password.',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Validate email format if using email as username
            if (!isValidEmail(username)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please enter a valid email address.',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Show loading SweetAlert
            Swal.fire({
                title: 'Logging in...',
                text: 'Please wait while we verify your credentials.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // The form will submit normally, PHP will handle the login
        });
    }

    // Handle student form submission
    if (studentLoginForm) {
        studentLoginForm.addEventListener('submit', function(e) {
            const studentId = document.getElementById('student_id').value.trim();

            // Basic client-side validation
            if (!studentId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please enter your student ID.',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Show loading SweetAlert
            Swal.fire({
                title: 'Logging in...',
                text: 'Please wait while we verify your student ID.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // The form will submit normally, PHP will handle the login
        });
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
