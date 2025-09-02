function submitAddMedicineForm() {
    const form = document.getElementById('addMedicineForm');
    const formData = new FormData(form);
    const submitBtn = document.querySelector('#addMedicineModal button[type="button"]:not(.text-gray-600)');

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
            modalManager.showSuccessMessage(data.message || 'Medicine added successfully');
            closeModal('addMedicineModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            modalManager.showErrorMessage(data.message || 'Failed to add medicine');
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

function openEditModal(medicine) {
    // Populate the edit form with medicine data
    modalManager.populateForm('editMedicineForm', {
        id: medicine.medicine_id,
        name: medicine.medicine_name,
        description: medicine.description,
        quantity: medicine.quantity,
        unit: medicine.unit,
        expiration_date: medicine.expiration_date,
        batch_no: medicine.batch_no
    });
    modalManager.openModal('editMedicineModal');
}

function openDeleteModal(medicineId, medicineName) {
    document.getElementById('delete_medicine_name').textContent = medicineName;
    document.getElementById('deleteMedicineModal').dataset.medicineId = medicineId;
    modalManager.openModal('deleteMedicineModal');
}

function viewMedicineDetails(medicineId) {
    // In a real application, you would redirect to a medicine details page or open a details modal
    alert(`Viewing details for medicine ID: ${medicineId}`);
}

function submitEditMedicineForm() {
    const form = document.getElementById('editMedicineForm');
    const formData = new FormData(form);
    const submitBtn = document.querySelector('#editMedicineModal button[type="button"]:not(.text-gray-600)');

    // Show loading state
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> Updating...';

    fetch(form.action, {
        method: form.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalManager.showSuccessMessage(data.message || 'Medicine updated successfully');
            closeModal('editMedicineModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            modalManager.showErrorMessage(data.message || 'Failed to update medicine');
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

function confirmDeleteMedicine() {
    const medicineId = document.getElementById('deleteMedicineModal').dataset.medicineId;

    // Show loading state
    const deleteBtn = document.querySelector('#deleteMedicineModal button[type="button"]:not(.text-gray-600)');
    const originalText = deleteBtn.textContent;
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<span class="loading-spinner"></span> Deleting...';

    fetch('delete-medicine.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${medicineId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalManager.showSuccessMessage(data.message || 'Medicine deleted successfully');
            closeModal('deleteMedicineModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            modalManager.showErrorMessage(data.message || 'Failed to delete medicine');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalManager.showErrorMessage('Network error. Please try again.');
    })
    .finally(() => {
        deleteBtn.disabled = false;
        deleteBtn.textContent = originalText;
    });
}

// Initialize modal event listeners
document.addEventListener('DOMContentLoaded', function() {
    const addMedicineBtn = document.getElementById('add-medicine-btn');
    if (addMedicineBtn) {
        addMedicineBtn.addEventListener('click', function() {
            modalManager.clearForm('addMedicineForm');
            modalManager.openModal('addMedicineModal');
        });
    }
});
