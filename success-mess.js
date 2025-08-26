document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorContainer = document.getElementById('errorContainer');
    
    // Check if there's an error from PHP
    if (typeof window.loginError !== 'undefined' && window.loginError) {
        showError(window.loginError);
    }
    
    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        
        // Basic client-side validation
        if (!username || !password) {
            e.preventDefault();
            showError('Please enter both username and password.');
            return false;
        }
        
        // Validate email format if using email as username
        if (!isValidEmail(username)) {
            e.preventDefault();
            showError('Please enter a valid email address.');
            return false;
        }
        
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="loading-spinner"></span> Logging in...';
    });
    
    function showError(message) {
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
        
        // Hide error after 5 seconds
        setTimeout(() => {
            errorContainer.style.display = 'none';
        }, 5000);
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});