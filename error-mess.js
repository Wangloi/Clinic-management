document.addEventListener('DOMContentLoaded', function() {
    // Error message handling
    if (typeof window.loginError !== 'undefined' && window.loginError) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: window.loginError,
            confirmButtonText: 'OK'
        });
    }
});

