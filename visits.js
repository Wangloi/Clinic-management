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
        fetch(`get-visit-data.php?id=${visitId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Populate form fields
                    document.getElementById('edit-visit-id').value = data.visit_id;
                    document.getElementById('edit-patient-type').value = data.patient_type;
                    document.getElementById('edit-patient-id').value = data.patient_id;
                    document.getElementById('edit-reason').value = data.reason;
                    document.getElementById('edit-diagnosis').value = data.diagnosis;
                    document.getElementById('edit-treatment').value = data.treatment;
                    document.getElementById('edit-remarks').value = data.remarks;
                    
                    // Show modal
                    modal.classList.remove('hidden');
                } else {
                    alert('Error loading visit data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching visit data:', error);
                alert('Error loading visit data. Please try again.');
            });
    } else {
        // Redirect to edit page if modal doesn't exist
        window.location.href = `edit-visit.php?id=${visitId}`;
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
    fetch(form.action, {
        method: form.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Visit updated successfully!');
            location.reload();
        } else {
            alert('Error updating visit: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating visit. Please try again.');
    });
}

function deleteVisit(visitId) {
    // Confirm deletion with user
    if (confirm('Are you sure you want to delete this visit record? This action cannot be undone.')) {
        console.log('Deleting visit:', visitId);
        
        // Send delete request to server
        fetch(`delete-visit.php?id=${visitId}`, {
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
                alert('Visit record deleted successfully!');
                // Reload the page to reflect changes
                location.reload();
            } else {
                alert('Error deleting visit: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting visit. Please try again.');
        });
    }
}

function viewVisitDetails(visitId) {
    // Show visit details in a modal or redirect to details page
    console.log('Viewing details for visit:', visitId);
    
    // Check if view modal exists
    const modal = document.getElementById('viewVisitModal');
    if (modal) {
        // Fetch visit details
        fetch(`get-visit-details.php?id=${visitId}`)
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
                    alert('Error loading visit details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching visit details:', error);
                alert('Error loading visit details. Please try again.');
            });
    } else {
        // Redirect to details page if modal doesn't exist
        window.location.href = `visit-details.php?id=${visitId}`;
    }
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
    window.location.href = `export-visits.php?format=${format}&${new URLSearchParams(filters).toString()}`;
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
            alert(data.message || 'Operation completed successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting form. Please try again.');
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
        alert('Please select at least one visit to perform this action.');
        return;
    }
    
    switch(action) {
        case 'bulk-delete':
            if (confirm(`Are you sure you want to delete ${visitIds.length} visit(s)?`)) {
                fetch('bulk-delete-visits.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ visit_ids: visitIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${data.deleted_count} visit(s) deleted successfully!`);
                        location.reload();
                    } else {
                        alert('Error deleting visits: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting visits. Please try again.');
                });
            }
            break;
            
        case 'bulk-export':
            const filters = {
                selected_ids: visitIds.join(','),
                search: new URLSearchParams(window.location.search).get('search') || '',
                patient_type: new URLSearchParams(window.location.search).get('patient_type') || ''
            };
            window.location.href = `export-visits.php?format=csv&${new URLSearchParams(filters).toString()}`;
            break;
    }
}
