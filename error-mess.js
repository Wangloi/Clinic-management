document.addEventListener('DOMContentLoaded', function() {
    // Check if there's an error message to display
    const errorMessage = document.getElementById('error-message');
    
    if (errorMessage && errorMessage.textContent.trim() !== '') {
        // Create popup container
        const popup = document.createElement('div');
        popup.className = 'error-popup';
        popup.innerHTML = `
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <p>${errorMessage.textContent}</p>
            </div>
        `;
        
        document.body.appendChild(popup);
        
        // Close button functionality
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