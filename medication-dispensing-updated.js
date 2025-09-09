// Medication Dispensing Functions
let dispensedMedications = [];

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
                    option.textContent = `${med.medicine_name} (${med.stock_quantity} available)`;
                    option.dataset.stock = med.stock_quantity;
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

function addMedicationToDispense() {
    const select = document.getElementById('medication-select');
    const quantityInput = document.getElementById('medication-quantity');
    const dosageInput = document.getElementById('medication-dosage');

    const medicineId = select.value;
    const quantity = parseInt(quantityInput.value);
    const dosage = dosageInput.value.trim();

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

    // Check if medication is already added
    const existingIndex = dispensedMedications.findIndex(med => med.medicine_id === medicineId);
    if (existingIndex !== -1) {
        alert('This medication is already added to the list');
        return;
    }

    // Add to dispensed medications array
    dispensedMedications.push({
        medicine_id: medicineId,
        medicine_name: medicineName,
        quantity: quantity,
        dosage: dosage
    });

    // Update UI
    updateDispensedMedicationsList();

    // Clear inputs
    select.value = '';
    quantityInput.value = '';
    dosageInput.value = '';

    console.log('Added medication:', dispensedMedications);
}

function updateDispensedMedicationsList() {
    const listContainer = document.getElementById('dispensed-medications-list');
    if (!listContainer) return;

    if (dispensedMedications.length === 0) {
        listContainer.innerHTML = '<p class="text-sm text-gray-500">No medications added yet</p>';
        return;
    }

    let html = '';
    dispensedMedications.forEach((med, index) => {
        html += `
            <div class="flex justify-between items-center p-2 bg-white border border-gray-200 rounded mb-2">
                <div class="flex-1">
                    <span class="font-medium text-sm">${med.medicine_name}</span>
                    <span class="text-xs text-gray-500 ml-2">Qty: ${med.quantity}</span>
                    ${med.dosage ? `<span class="text-xs text-gray-500 ml-2">Dosage: ${med.dosage}</span>` : ''}
                </div>
                <button type="button" onclick="removeDispensedMedication(${index})" class="text-red-500 hover:text-red-700 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
    });

    listContainer.innerHTML = html;
}

function removeDispensedMedication(index) {
    dispensedMedications.splice(index, 1);
    updateDispensedMedicationsList();
}

function submitDispenseMedication() {
    if (dispensedMedications.length === 0) {
        alert('Please add at least one medication to dispense');
        return;
    }

    const visitId = document.getElementById('edit-visit-id').value;
    const notes = document.getElementById('dispense-notes')?.value || '';

    const dispenseData = {
        visit_id: visitId,
        medications: dispensedMedications,
        notes: notes
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
            alert('Medications dispensed successfully!');
            dispensedMedications = [];
            updateDispensedMedicationsList();
            closeModal('editVisitModal');
            location.reload();
        } else {
            alert('Error dispensing medications: ' + data.message);
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
                            // Clear any previous dispensed medications
                            dispensedMedications = [];
                            updateDispensedMedicationsList();
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
