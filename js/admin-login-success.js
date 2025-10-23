// Admin Login Success Alert
function showAdminLoginSuccessAlert(adminData) {
    Swal.fire({
        title: 'Welcome!',
        text: `Hello ${adminData.fullName}, you have successfully logged in.`,
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#007bff'
    }).then((result) => {
        // Stay on admin dashboard when alert is closed
        if (result.isConfirmed) {
            // Just close the alert, stay on current page (admin dashboard)
            return;
        }
    });
}

// Initialize admin login success alert if needed
document.addEventListener('DOMContentLoaded', function() {
    // Check if login success flag is set
    if (window.showAdminLoginAlert && window.adminData) {
        showAdminLoginSuccessAlert(window.adminData);
    }
});
