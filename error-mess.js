document.addEventListener('DOMContentLoaded', function() {
    // Error message handling
    if (typeof window.loginError !== 'undefined' && window.loginError) {
        const popup = document.createElement('div');
        popup.className = 'error-popup';
        popup.innerHTML = `
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <p>${window.loginError}</p>
            </div>
        `;
        
        document.body.appendChild(popup);
        
        const closeBtn = popup.querySelector('.close-btn');
        closeBtn.addEventListener('click', function() {
            popup.remove();
        });
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            popup.remove();
        }, 5000);
    }
});