// Add Account Modal JavaScript

// Function to open the modal
function openAddAccountModal() {
  const modal = document.getElementById('addAccountModal');
  modal.classList.remove('hidden');
  modal.style.display = 'flex';
  // Clear form when opening
  clearAddAccountForm();
  // Initially hide contact number if role not Clinic Staff
  toggleContactNumber();
}

// Function to close the modal
function closeAddAccountModal() {
  const modal = document.getElementById('addAccountModal');
  modal.classList.add('hidden');
  modal.style.display = 'none';
  clearAddAccountForm();
}

// Function to clear the form
function clearAddAccountForm() {
  const form = document.getElementById('addAccountForm');
  if (form) {
    form.reset();
    // Reset password strength indicators
    resetPasswordStrength();
    // Clear messages
    const messageDiv = document.getElementById('addAccountMessage');
    if (messageDiv) {
      messageDiv.classList.add('hidden');
      messageDiv.innerHTML = '';
    }
  }
}

// Function to toggle contact number field based on role
function toggleContactNumber() {
  const roleSelect = document.getElementById('role');
  const contactNumberGroup = document.getElementById('contactNumberGroup');

  if (roleSelect && contactNumberGroup) {
    if (roleSelect.value === 'Clinic Staff') {
      contactNumberGroup.style.display = 'block';
    } else {
      contactNumberGroup.style.display = 'none';
      // Clear contact number when hiding
      const contactInput = document.getElementById('contact_number');
      if (contactInput) contactInput.value = '';
    }
  }
}

// Function to check password strength
function checkPasswordStrength() {
  const password = document.getElementById('password').value;
  const strengthText = document.getElementById('strength-text');

  if (!password) {
    resetPasswordStrength();
    return;
  }

  const hasUpperCase = /[A-Z]/.test(password);
  const hasLowerCase = /[a-z]/.test(password);
  const hasNumbers = /\d/.test(password);
  const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
  const isLongEnough = password.length >= 8;

  let strength = 0;
  let feedback = [];

  if (isLongEnough) {
    strength++;
  } else {
    feedback.push('At least 8 characters');
  }

  if (hasUpperCase) {
    strength++;
  } else {
    feedback.push('Uppercase letter');
  }

  if (hasLowerCase) {
    strength++;
  } else {
    feedback.push('Lowercase letter');
  }

  if (hasNumbers) {
    strength++;
  } else {
    feedback.push('Number');
  }

  if (hasSpecialChar) {
    strength++;
  } else {
    feedback.push('Special character');
  }

  // Update visual bars
  for (let i = 1; i <= 5; i++) {
    const bar = document.getElementById(`strength-bar-${i}`);
    if (bar) {
      if (i <= strength) {
        if (strength >= 4) {
          bar.className = 'h-1 w-1/5 bg-green-500 rounded';
        } else if (strength >= 3) {
          bar.className = 'h-1 w-1/5 bg-yellow-500 rounded';
        } else {
          bar.className = 'h-1 w-1/5 bg-red-500 rounded';
        }
      } else {
        bar.className = 'h-1 w-1/5 bg-gray-200 rounded';
      }
    }
  }

  // Update text
  let color = 'text-red-500';
  let text = 'Weak';

  if (strength >= 4) {
    color = 'text-green-500';
    text = 'Strong';
  } else if (strength >= 3) {
    color = 'text-yellow-500';
    text = 'Medium';
  }

  if (strengthText) {
    strengthText.className = `text-xs ${color}`;
    strengthText.innerHTML = `${text}${feedback.length > 0 ? ' - Missing: ' + feedback.join(', ') : ''}`;
  }
}

// Function to reset password strength indicators
function resetPasswordStrength() {
  for (let i = 1; i <= 5; i++) {
    const bar = document.getElementById(`strength-bar-${i}`);
    if (bar) {
      bar.className = 'h-1 w-1/5 bg-gray-200 rounded';
    }
  }
  const strengthText = document.getElementById('strength-text');
  if (strengthText) {
    strengthText.className = 'text-xs text-gray-500';
    strengthText.innerHTML = '';
  }
}

// Function to validate the form before submission
function validateAddAccountForm(event) {
  event.preventDefault();

  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirm_password').value;
  const firstName = document.getElementById('first_name').value.trim();
  const lastName = document.getElementById('last_name').value.trim();
  const email = document.getElementById('email').value.trim();
  const role = document.getElementById('role').value;

  // Basic validation
  if (!firstName || !lastName || !email || !role || !password) {
    showAddAccountMessage('Please fill in all required fields.', 'error');
    return false;
  }

  // Email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showAddAccountMessage('Please enter a valid email address.', 'error');
    return false;
  }

  // Password match validation
  if (password !== confirmPassword) {
    showAddAccountMessage('Passwords do not match.', 'error');
    return false;
  }

  // Password strength validation
  const hasUpperCase = /[A-Z]/.test(password);
  const hasLowerCase = /[a-z]/.test(password);
  const hasNumbers = /\d/.test(password);
  const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
  const isLongEnough = password.length >= 8;

  if (!hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar || !isLongEnough) {
    showAddAccountMessage('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.', 'error');
    return false;
  }

  // If all validations pass, submit the form
  submitAddAccountForm();
  return false;
}

// Function to submit the form via AJAX
function submitAddAccountForm() {
  const form = document.getElementById('addAccountForm');
  const formData = new FormData(form);
  const submitBtn = document.querySelector('#addAccountForm button[type="submit"]');

  // Disable submit button
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating Account...';
  }

  // Clear previous messages
  showAddAccountMessage('', '');

  fetch('add_account.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showAddAccountMessage(data.message, 'success');
      // Close modal and refresh page after success
      setTimeout(() => {
        closeAddAccountModal();
        location.reload();
      }, 2000);
    } else {
      showAddAccountMessage(data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAddAccountMessage('An error occurred. Please try again.', 'error');
  })
  .finally(() => {
    // Re-enable submit button
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Add Account';
    }
  });
}

// Function to show messages in the modal
function showAddAccountMessage(message, type) {
  const messageDiv = document.getElementById('addAccountMessage');
  if (messageDiv) {
    messageDiv.className = 'mt-4 text-center';
    if (type === 'error') {
      messageDiv.classList.add('text-red-600', 'bg-red-50', 'border', 'border-red-200', 'rounded-md', 'p-3');
      messageDiv.innerHTML = '<strong>Error:</strong> ' + message;
    } else if (type === 'success') {
      messageDiv.classList.add('text-green-600', 'bg-green-50', 'border', 'border-green-200', 'rounded-md', 'p-3');
      messageDiv.innerHTML = '<strong>Success:</strong> ' + message;
    }
    messageDiv.classList.remove('hidden');
  }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  // Add event listener to role select for contact number toggle
  const roleSelect = document.getElementById('role');
  if (roleSelect) {
    roleSelect.addEventListener('change', toggleContactNumber);
  }

  // Add click outside to close modal
  const modal = document.getElementById('addAccountModal');
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        closeAddAccountModal();
      }
    });
  }
});
