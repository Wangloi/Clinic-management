// modal.js - Centralized modal management for Clinic Management System

class ModalManager {
    constructor() {
        this.currentModal = null;
        this.init();
    }

    init() {
        // Close modal when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal') ||
                e.target.classList.contains('modal-tailwind') ||
                e.target.id === 'addVisitModal') {
                this.closeCurrentModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.currentModal) {
                this.closeCurrentModal();
            }
        });

        // Handle close buttons with class 'close'
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('close') || e.target.closest('.close')) {
                e.preventDefault();
                const modal = e.target.closest('.modal, [id*="Modal"]');
                if (modal) {
                    this.closeModal(modal.id);
                }
            }
        });

        // Handle click outside for Tailwind modals
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('addVisitModal');
            if (modal && !modal.classList.contains('hidden') && e.target === modal) {
                this.closeModal('addVisitModal');
            }

            // Handle click outside for medicine modals
            const medicineModals = ['addMedicineModal', 'editMedicineModal', 'deleteMedicineModal'];
            medicineModals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden') && e.target === modal) {
                    this.closeModal(modalId);
                }
            });
        });



        // Prevent form submission from closing modal until handled
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.closest('.modal') || form.closest('.modal-tailwind') || form.closest('#addVisitModal') ||
                form.closest('#addMedicineModal') || form.closest('#editMedicineModal') || form.closest('#deleteMedicineModal')) {
                e.preventDefault();
                this.handleFormSubmit(form);
            }
        });
    }

    openModal(modalId) {
        // Close any currently open modal
        this.closeCurrentModal();

        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error(`Modal with ID ${modalId} not found`);
            return false;
        }

        // Determine modal type and show accordingly
        if (modal.classList.contains('modal')) {
            modal.style.display = 'block';
            modal.style.pointerEvents = 'auto';
        } else if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.style.pointerEvents = 'auto';
        } else {
            modal.style.display = 'flex';
            modal.style.pointerEvents = 'auto';
        }

        this.currentModal = modal;

        // Add direct event listeners to close buttons
        const closeButtons = modal.querySelectorAll('.close, button[onclick*="closeModal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.closeModal(modalId);
            });
        });

        // Focus on first input if available
        setTimeout(() => {
            const firstInput = modal.querySelector('input, select, textarea, button');
            if (firstInput) firstInput.focus();
        }, 100);

        // Skip backdrop for this modal since user doesn't want gray background
        // this.addBackdrop();

        return true;
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return false;

        // Remove backdrop immediately
        this.removeBackdrop();

        // Handle different modal types
        if (modal.classList.contains('modal')) {
            modal.style.display = 'none';
            modal.style.pointerEvents = 'none';
        } else {
            // For Tailwind modals, add hidden class and disable pointer events
            modal.classList.add('hidden');
            modal.style.pointerEvents = 'none';
        }

        if (this.currentModal === modal) {
            this.currentModal = null;
        }

        return true;
    }

    closeCurrentModal() {
        if (this.currentModal) {
            this.closeModal(this.currentModal.id);
        }
    }

    addBackdrop() {
        // Remove existing backdrop if any
        this.removeBackdrop();

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop';
        backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        `;
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';
    }

    removeBackdrop() {
        // Remove all modal backdrops to be safe
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());

        // Also remove any backdrop with modal-backdrop class
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }

        document.body.style.overflow = '';
    }

    async handleFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        
        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';
            submitBtn.classList.add('modal-loading');
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccessMessage(data.message || 'Operation completed successfully!');
                this.closeCurrentModal();
                
                // Reload page or update UI as needed
                if (data.reload !== false) {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                this.showErrorMessage(data.message || 'An error occurred. Please try again.');
                this.resetFormButton(submitBtn, originalText);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.showErrorMessage('Network error. Please check your connection and try again.');
            this.resetFormButton(submitBtn, originalText);
        }
    }

    resetFormButton(button, originalText) {
        if (button) {
            button.disabled = false;
            button.textContent = originalText;
            button.classList.remove('modal-loading');
        }
    }

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }

    showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.modal-message');
        existingMessages.forEach(msg => msg.remove());

        const messageDiv = document.createElement('div');
        messageDiv.className = `modal-message ${type}-message`;
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            color: white;
            z-index: 1001;
            animation: slideInRight 0.3s ease-out;
            ${type === 'success' ? 'background-color: #4CAF50;' : 'background-color: #f44336;'}
        `;
        
        messageDiv.textContent = message;
        document.body.appendChild(messageDiv);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // Utility function to populate form data
    populateForm(formId, data) {
        const form = document.getElementById(formId);
        if (!form) return;

        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = data[key];
                } else if (input.tagName === 'SELECT') {
                    // Handle select dropdowns
                    const value = data[key] || '';
                    const option = input.querySelector(`option[value="${value}"]`);
                    if (option) {
                        option.selected = true;
                    } else {
                        // If no matching option found, set the first option as selected
                        if (input.options.length > 0) {
                            input.options[0].selected = true;
                        }
                    }
                } else {
                    input.value = data[key] || '';
                }
            }
        });
    }

    // Utility function to clear form
    clearForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.reset();
        const errorMessages = form.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
        
        const errorInputs = form.querySelectorAll('.error');
        errorInputs.forEach(input => input.classList.remove('error'));
    }
}

// Initialize modal manager
const modalManager = new ModalManager();

// Global functions for backward compatibility
function openModal(modalId) {
    return modalManager.openModal(modalId);
}

function closeModal(modalId) {
    return modalManager.closeModal(modalId);
}

function closeCurrentModal() {
    return modalManager.closeCurrentModal();
}

// Add CSS for loading spinner
const style = document.createElement('style');
style.textContent = `
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 8px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .modal-loading {
        opacity: 0.7;
        pointer-events: none;
    }
`;
document.head.appendChild(style);

// Export for module usage if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ModalManager, modalManager };
}
