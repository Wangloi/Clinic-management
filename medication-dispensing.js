// Medication Dispensing Functions

function loadMedications() {
    fetch('medicine_data.php?action=get-all')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('medication-select');
            if (select && data.length > 0) {
                select.innerHTML = '<option value="">Choose a medication...</option>';
                data.forEach(med => {
                    const option = document.createElement('option');
                    option.value = med.medicine_id;
                    option.textContent = `${med.medicine_name} (${med.quantity} available)`;
                    option.dataset.stock = med.quantity;
                    option.dataset.name = med.medicine_name;
                    option.dataset.description = med.description;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading medications:', error);
        });
}

function submitDispenseMedication() {
    const select = document.getElementById('medication-select');
    const quantityInput = document.getElementById('medication-quantity');

    const medicineId = select.value;
    const quantity = parseInt(quantityInput.value);

    if (!medicineId) {
        alert('Please select a medication');
        return;
    }

    if (!quantity || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }

    const selectedOption = select.options[select.selectedIndex];
    const availableStock = parseInt(selectedOption.dataset.stock);
    const medicineName = selectedOption.dataset.name;

    if (quantity > availableStock) {
        alert(`Insufficient stock. Only ${availableStock} units available.`);
        return;
    }

    const visitId = document.getElementById('edit-visit-id').value;

    const dispenseData = {
        visit_id: visitId,
        medications: [{
            medicine_id: medicineId,
            medicine_name: medicineName,
            quantity: quantity
        }]
    };

    console.log('Submitting dispense data:', dispenseData);

    fetch('dispense-medication.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dispenseData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Medication dispensed successfully!');
            // Clear inputs
            select.value = '';
            quantityInput.value = '';
            closeModal('editVisitModal');
            location.reload();
        } else {
            alert('Error dispensing medication: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

// Initialize medication functionality when edit modal is opened
document.addEventListener('DOMContentLoaded', function() {
    // Load medications when edit modal is shown
    const editModal = document.getElementById('editVisitModal');
    if (editModal) {
        // Listen for when the modal becomes visible
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (!editModal.classList.contains('hidden')) {
                        // Modal is now visible, load medications
                        setTimeout(function() {
                            loadMedications();
                        }, 100);
                    }
                }
            });
        });

        observer.observe(editModal, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
});
