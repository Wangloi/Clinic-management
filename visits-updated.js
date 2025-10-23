// visits.js
function goToPage(page) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    window.location = url.toString();
}

function clearFilters() {
    window.location = window.location.pathname;
}

// Preserve rows per page selection when changing pages
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const rowsSelect = this.querySelector('select[name="rows"]');
            if (rowsSelect) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'rows';
                hiddenInput.value = rowsSelect.value;
                this.appendChild(hiddenInput);
            }
        });
    });
});

// Function to handle visit actions
function handleVisitAction(action, visitId) {
    switch(action) {
        case 'edit':
            editVisit(visitId);
            break;
        case 'delete':
            deleteVisit(visitId);
            break;
        case 'view':
            viewVisitDetails(visitId);
            break;
    }
}

function editVisit(visitId) {
    // Show edit modal or redirect to edit page
    console.log('Opening edit form for visit:', visitId);

    // Check if edit modal exists
    const modal = document.getElementById('editVisitModal');
    if (modal) {
        // Fetch visit data and populate form
        fetch(`visit-actions.php?action=get-data&id=${visitId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Fetched visit data:', data); // Debug log
                if (data.success) {
                    // Populate form fields
                    document.getElementById('edit-visit-id').value = data.visit_id || '';
                    document.getElementById('edit-patient-type').value = data.patient_type || '';
                    document.getElementById('edit-patient-id').value = data.patient_id || '';
                    document.getElementById('edit-reason').value = data.reason || '';
                    document.getElementById('edit-diagnosis').value = data.diagnosis || '';
                    document.getElementById('edit-treatment').value = data.treatment || '';
                    document.getElementById('edit-remarks').value = data.remarks || '';

                    // Use centralized modal manager to show modal
                    modalManager.openModal('editVisitModal');

                    // Load medications for dispensing section
                    if (typeof loadMedications === 'function') {
                        setTimeout(function() {
                            loadMedications();
                        }, 200);
                    }
                } else {
                    modalManager.showErrorMessage('Error loading visit data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching visit data:', error);
                modalManager.showErrorMessage('Error loading visit data. Please try again.');
            });
    } else {
        // Redirect to edit page if modal doesn't exist
        window.location.href = `../edit-visit.php?id=${visitId}`;
    }
}

// Function to close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Function to submit the edit form
function submitEditForm() {
    const form = document.getElementById('editVisitForm');
    if (!form) return;

    const formData = new FormData(form);
    const submitBtn = document.querySelector('#editVisitModal button[type="button"]:not(.text-gray-600)');

    // Show loading state
    const originalText = submitBtn?.textContent || '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Updating...';
    }

    fetch(form.action, {
        method: form.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalManager.showSuccessMessage(data.message || 'Visit updated successfully');
            closeModal('editVisitModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            modalManager.showErrorMessage(data.message || 'Failed to update visit');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalManager.showErrorMessage('Network error. Please try again.');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

function deleteVisit(visitId) {
    // Confirm deletion with user using SweetAlert
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this visit record? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Deleting visit:', visitId);

            // Send delete request to server
            fetch(`visit-actions.php?action=delete&id=${visitId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    modalManager.showSuccessMessage(data.message || 'Visit record deleted successfully!');
                    // Reload the page to reflect changes
                    setTimeout(() => location.reload(), 1000);
                } else {
                    modalManager.showErrorMessage(data.message || 'Failed to delete visit');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalManager.showErrorMessage('Network error. Please try again.');
            });
        }
    });
}

function viewVisitDetails(visitId) {
    // Show visit details in a modal or redirect to details page
    console.log('Viewing details for visit:', visitId);

    // Check if view modal exists
    const modal = document.getElementById('viewVisitModal');
    if (modal) {
        // Fetch visit details
        fetch(`visit-actions.php?action=get-details&id=${visitId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Populate modal with visit details
                    document.getElementById('view-patient-name').textContent = data.patient_name || 'N/A';
                    document.getElementById('view-patient-id').textContent = data.patient_id || 'N/A';
                    document.getElementById('view-patient-type').textContent = data.patient_type || 'N/A';
                    document.getElementById('view-visit-date').textContent = data.visit_date || 'N/A';
                    document.getElementById('view-reason').textContent = data.reason || 'N/A';
                    document.getElementById('view-diagnosis').textContent = data.diagnosis || 'N/A';
                    document.getElementById('view-treatment').textContent = data.treatment || 'N/A';
                    document.getElementById('view-remarks').textContent = data.remarks || 'N/A';
                    document.getElementById('view-created-at').textContent = data.created_at || 'N/A';

                    // Show modal
                    modal.classList.remove('hidden');
                } else {
                    modalManager.showErrorMessage('Error loading visit details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching visit details:', error);
                modalManager.showErrorMessage('Error loading visit details. Please try again.');
            });
    } else {
        // Redirect to details page if modal doesn't exist
        window.location.href = `../visit-details.php?id=${visitId}`;
    }
}

// Function to open add visit modal
function openAddVisitModal() {
    console.log('Opening add visit modal');

    // Check if add modal exists
    const modal = document.getElementById('addVisitModal');
    if (modal) {
        console.log('Modal ID:', modal.id);
        console.log('Modal exists:', modal !== null);

        // Clear the form
        document.getElementById('addVisitForm').reset();

        // Use centralized modal manager to show modal if available, otherwise use fallback
        if (typeof modalManager !== 'undefined' && modalManager.openModal) {
            modalManager.openModal('addVisitModal');
        } else {
            // Fallback: show modal directly
            modal.classList.remove('hidden');
        }
    } else {
        console.error('Add visit modal not found');
    }
}

// Function to submit add visit form
function submitAddVisitForm() {
    const form = document.getElementById('addVisitForm');
    if (!form) return;

    const formData = new FormData(form);
    const submitBtn = document.querySelector('#addVisitModal button[type="button"]:not(.text-gray-600)');

    // Show loading state
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> Adding...';

    fetch(form.action, {
        method: form.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalManager.showSuccessMessage(data.message || 'Visit added successfully');
            closeModal('addVisitModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            modalManager.showErrorMessage(data.message || 'Failed to add visit');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalManager.showErrorMessage('Network error. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

// Filter functionality
function filterVisits() {
    const searchTerm = document.querySelector('input[name="search"]').value;
    const patientType = document.querySelector('select[name="patient_type"]').value;
    const sortBy = document.querySelector('select[name="sort"]').value;

    const url = new URL(window.location);
    url.searchParams.set('search', searchTerm);
    url.searchParams.set('patient_type', patientType);
    url.searchParams.set('sort', sortBy);
    url.searchParams.set('page', 1);

    window.location = url.toString();
}

// Export visit data
function exportVisits(format) {
    const filters = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        patient_type: new URLSearchParams(window.location.search).get('patient_type') || '',
        sort: new URLSearchParams(window.location.search).get('sort') || 'date-desc'
    };

    // Redirect to export endpoint
    window.location.href = `../export-visits.php?format=${format}&${new URLSearchParams(filters).toString()}`;
}

// Form validation for visit forms
function validateVisitForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });

    return isValid;
}

// Close modal function
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Submit visit form via AJAX
function submitVisitForm(formId, successCallback) {
    const form = document.getElementById(formId);
    if (!form || !validateVisitForm(formId)) return;

    const formData = new FormData(form);

    fetch(form.action, {
        method: form.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (successCallback) successCallback(data);
            modalManager.showSuccessMessage(data.message || 'Operation completed successfully!');
            location.reload();
        } else {
            modalManager.showErrorMessage('Error: ' + (data.message || 'Unknown error occurred'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalManager.showErrorMessage('Error submitting form. Please try again.');
    });
}

// Quick search functionality
function quickSearch() {
    const searchInput = document.getElementById('quick-search');
    if (!searchInput) return;

    const searchTerm = searchInput.value.trim();
    if (searchTerm) {
        const url = new URL(window.location);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('page', 1);
        window.location = url.toString();
    }
}

// Toggle row selection
function toggleRowSelection(checkbox, rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        if (checkbox.checked) {
            row.classList.add('bg-blue-50');
        } else {
            row.classList.remove('bg-blue-50');
        }
    }
}

// Bulk actions
function performBulkAction(action) {
    const selectedRows = document.querySelectorAll('input[type="checkbox"]:checked');
    const visitIds = Array.from(selectedRows).map(checkbox => checkbox.value);

    if (visitIds.length === 0) {
        modalManager.showErrorMessage('Please select at least one visit to perform this action.');
        return;
    }

    switch(action) {
        case 'bulk-delete':
            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete ${visitIds.length} visit(s)? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../bulk-delete-visits.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ visit_ids: visitIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', `${data.deleted_count} visit(s) deleted successfully!`, 'success');
                            location.reload();
                        } else {
                            Swal.fire('Error!', 'Error deleting visits: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Error deleting visits. Please try again.', 'error');
                    });
                }
            });
            break;

        case 'bulk-export':
            const filters = {
                selected_ids: visitIds.join(','),
                search: new URLSearchParams(window.location.search).get('search') || '',
                patient_type: new URLSearchParams(window.location.search).get('patient_type') || ''
            };
            window.location.href = `../export-visits.php?format=csv&${new URLSearchParams(filters).toString()}`;
            break;
    }
}
