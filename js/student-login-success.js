// Student Login Success Alert
function showLoginSuccessAlert(studentData) {
    Swal.fire({
        title: 'Welcome!',
        text: `Hello ${studentData.fullName}, you have successfully logged in.`,
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#007bff'
    }).then((result) => {
        // Stay on student dashboard when alert is closed
        if (result.isConfirmed) {
            // Just close the alert, stay on current page (student dashboard)
            return;
        }
    });
}

// Initialize login success alert if needed
document.addEventListener('DOMContentLoaded', function() {
    // Check if login success flag is set
    if (window.showLoginAlert && window.studentData) {
        showLoginSuccessAlert(window.studentData);
    }
});
