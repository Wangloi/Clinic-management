// Medication Dispensing Functions
let dispensedMedications = [];

function loadMedications() {
    console.log('Loading medications...');
    fetch('medicine_data.php?action=get-all')
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Medications data:', data);
            const select = document.getElementById('medication-select');
            if (select && data.length > 0) {
                select.innerHTML = '<option value="">Choose a medication...</option>';
                data.forEach(med => {
                    const option = document.createElement('option');
                    option.value = med.medicine_id;
                    option.textContent = `${med.medicine_name} (${med.quantity} ${med.unit || ''} available)`;
                    option.dataset.stock = med.quantity;
                    option.dataset.name = med.medicine_name;
                    option.dataset.description = med.description;
                    option.dataset.unit = med.unit || '';
                    select.appendChild(option);
                });
                console.log('Medications loaded successfully');
            } else {
                console.log('No medications found or select element not found');
            }
        })
        .catch(error => {
            console.error('Error loading medications:', error);
            const select = document.getElementById('medication-select');
            if (select) {
                select.innerHTML = '<option value="">Error loading medications</option>';
            }
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
    const unit = selectedOption.dataset.unit;

    if (quantity > availableStock) {
        alert(`Insufficient stock. Only ${availableStock} ${unit} available.`);
        return;
    }

    // Check if medication already added
    const existingIndex = dispensedMedications.findIndex(med => med.medicine_id === medicineId);
    
    if (existingIndex !== -1) {
        // Update existing medication
        const totalQuantity = dispensedMedications[existingIndex].quantity + quantity;
        if (totalQuantity > availableStock) {
            alert(`Total quantity would exceed available stock (${availableStock} ${unit})`);
            return;
        }
        dispensedMedications[existingIndex].quantity = totalQuantity;
        dispensedMedications[existingIndex].dosage = dosage; // Update dosage
    } else {
        // Add new medication
        dispensedMedications.push({
            medicine_id: medicineId,
            medicine_name: medicineName,
            quantity: quantity,
            dosage: dosage,
            unit: unit
        });
    }

    // Clear inputs
    select.value = '';
    quantityInput.value = '';
    dosageInput.value = '';

    // Update display
    updateDispensedMedicationsList();
}

function updateDispensedMedicationsList() {
    const listContainer = document.getElementById('dispensed-medications-list');
    
    if (dispensedMedications.length === 0) {
        listContainer.innerHTML = '<p class="text-sm text-gray-500">No medications added yet</p>';
        return;
    }

    let html = '';
    dispensedMedications.forEach((med, index) => {
        html += `
            <div class="flex justify-between items-center p-2 bg-white rounded border mb-2">
                <div class="flex-1">
                    <div class="font-medium text-sm">${med.medicine_name}</div>
                    <div class="text-xs text-gray-600">Quantity: ${med.quantity} ${med.unit}</div>
                    ${med.dosage ? `<div class="text-xs text-gray-600">Dosage: ${med.dosage}</div>` : ''}
                </div>
                <button type="button" onclick="removeMedicationFromDispense(${index})" 
                        class="text-red-600 hover:text-red-800 text-sm">
                    Remove
                </button>
            </div>
        `;
    });
    
    listContainer.innerHTML = html;
}

function removeMedicationFromDispense(index) {
    dispensedMedications.splice(index, 1);
    updateDispensedMedicationsList();
}

function getDispensedMedications() {
    return dispensedMedications;
}

function clearDispensedMedications() {
    dispensedMedications = [];
    updateDispensedMedicationsList();
}

// Load medications when the page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing medication dispensing...');
    // Small delay to ensure all elements are rendered
    setTimeout(loadMedications, 100);
});

// Also load medications when the modal is opened (if applicable)
function initializeMedicationDispensing() {
    console.log('Initializing medication dispensing...');
    loadMedications();
}

// Open dispense medication modal
async function openDispenseModal(visitId) {
    try {
        console.log('Opening dispense modal for visit:', visitId);
        
        // Close any other modals first
        if (typeof closeAllModals === 'function') {
            closeAllModals();
        }
        
        // Show the modal
        const modal = document.getElementById('dispenseMedicationModal');
        modal.classList.remove('hidden');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
        
        // Load visit information
        await loadVisitInfoForDispensing(visitId);
        
        // Load medications for the dispense modal
        loadMedicationsForDispensing();
        
        // Clear form fields
        document.getElementById('dispense-medication-select').value = '';
        document.getElementById('dispense-medication-quantity').value = '';
        
        // Store visit ID for later use
        window.currentDispenseVisitId = visitId;
        
    } catch (error) {
        console.error('Error opening dispense modal:', error);
        alert('Failed to open medication dispensing: ' + error.message);
    }
}

// Load visit information for dispensing
async function loadVisitInfoForDispensing(visitId) {
    try {
        const response = await fetch(`get-visit-details.php?visit_id=${visitId}`);
        const data = await response.json();
        
        if (data.success) {
            const visit = data.visit;
            document.getElementById('dispense-patient-name').textContent = visit.patient_name || 'N/A';
            document.getElementById('dispense-visit-date').textContent = visit.visit_date || 'N/A';
            document.getElementById('dispense-visit-reason').textContent = visit.reason || 'N/A';
            document.getElementById('dispense-visit-treatment').textContent = visit.treatment || 'N/A';
        } else {
            throw new Error(data.message || 'Failed to load visit information');
        }
    } catch (error) {
        console.error('Error loading visit info:', error);
        // Set default values on error
        document.getElementById('dispense-patient-name').textContent = 'Error loading';
        document.getElementById('dispense-visit-date').textContent = 'Error loading';
        document.getElementById('dispense-visit-reason').textContent = 'Error loading';
        document.getElementById('dispense-visit-treatment').textContent = 'Error loading';
    }
}

// Load medications for the dispense modal
function loadMedicationsForDispensing() {
    console.log('Loading medications for dispensing...');
    fetch('medicine_data.php?action=get-all')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('dispense-medication-select');
            if (select && data.length > 0) {
                select.innerHTML = '<option value="">Choose a medication...</option>';
                data.forEach(med => {
                    const option = document.createElement('option');
                    option.value = med.medicine_id;
                    option.textContent = `${med.medicine_name} (${med.quantity} ${med.unit || ''} available)`;
                    option.dataset.stock = med.quantity;
                    option.dataset.name = med.medicine_name;
                    option.dataset.description = med.description;
                    option.dataset.unit = med.unit || '';
                    select.appendChild(option);
                });
                console.log('Medications loaded for dispensing');
            }
        })
        .catch(error => {
            console.error('Error loading medications for dispensing:', error);
        });
}

// Add medication to dispense list (for the dispense modal)
function addMedicationToDispenseList() {
    const select = document.getElementById('dispense-medication-select');
    const quantityInput = document.getElementById('dispense-medication-quantity');
    const dosageInput = document.getElementById('dispense-medication-dosage');

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
    const unit = selectedOption.dataset.unit;

    if (quantity > availableStock) {
        alert(`Insufficient stock. Only ${availableStock} ${unit} available.`);
        return;
    }

    // Check if medication already added
    const existingIndex = dispensedMedications.findIndex(med => med.medicine_id === medicineId);
    
    if (existingIndex !== -1) {
        // Update existing medication
        const totalQuantity = dispensedMedications[existingIndex].quantity + quantity;
        if (totalQuantity > availableStock) {
            alert(`Total quantity would exceed available stock (${availableStock} ${unit})`);
            return;
        }
        dispensedMedications[existingIndex].quantity = totalQuantity;
        dispensedMedications[existingIndex].dosage = dosage; // Update dosage
    } else {
        // Add new medication
        dispensedMedications.push({
            medicine_id: medicineId,
            medicine_name: medicineName,
            quantity: quantity,
            dosage: dosage,
            unit: unit
        });
    }

    // Clear inputs
    select.value = '';
    quantityInput.value = '';
    dosageInput.value = '';

    // Update display
    updateDispenseMedicationsList();
}

// Update the dispensed medications list display (for dispense modal)
function updateDispenseMedicationsList() {
    const listContainer = document.getElementById('dispense-medications-list');
    
    if (dispensedMedications.length === 0) {
        listContainer.innerHTML = '<p class="text-sm text-gray-500">No medications added yet</p>';
        return;
    }

    let html = '';
    dispensedMedications.forEach((med, index) => {
        html += `
            <div class="flex justify-between items-center p-2 bg-white rounded border mb-2">
                <div class="flex-1">
                    <div class="font-medium text-sm">${med.medicine_name}</div>
                    <div class="text-xs text-gray-600">Quantity: ${med.quantity} ${med.unit}</div>
                    ${med.dosage ? `<div class="text-xs text-gray-600">Dosage: ${med.dosage}</div>` : ''}
                </div>
                <button type="button" onclick="removeMedicationFromDispenseList(${index})" 
                        class="text-red-600 hover:text-red-800 text-sm">
                    Remove
                </button>
            </div>
        `;
    });
    
    listContainer.innerHTML = html;
}

// Remove medication from dispense list
function removeMedicationFromDispenseList(index) {
    dispensedMedications.splice(index, 1);
    updateDispenseMedicationsList();
}

// Submit medication dispensing (simplified version)
async function submitDispenseMedication() {
    try {
        const select = document.getElementById('dispense-medication-select');
        const quantityInput = document.getElementById('dispense-medication-quantity');
        
        const medicineId = select.value;
        const quantity = parseInt(quantityInput.value);

        if (!medicineId) {
            Swal.fire({
                icon: 'warning',
                title: 'Selection Required',
                text: 'Please select a medication to dispense',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        if (!quantity || quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Quantity',
                text: 'Please enter a valid quantity greater than 0',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const availableStock = parseInt(selectedOption.dataset.stock);
        const medicineName = selectedOption.dataset.name;

        if (quantity > availableStock) {
            Swal.fire({
                icon: 'error',
                title: 'Insufficient Stock',
                text: `Only ${availableStock} ${selectedOption.dataset.unit || 'units'} available for ${medicineName}`,
                confirmButtonColor: '#d33'
            });
            return;
        }

        const visitId = window.currentDispenseVisitId;
        if (!visitId) {
            throw new Error('No visit ID found');
        }

        const requestData = {
            visit_id: visitId,
            medications: [{
                medicine_id: medicineId,
                quantity: quantity
            }],
            notes: ''
        };

        console.log('Submitting dispensing data:', requestData);

        // Show confirmation dialog before dispensing with higher z-index
        const confirmResult = await Swal.fire({
            title: 'Confirm Dispensing',
            html: `
                <div class="text-left">
                    <p><strong>Medicine:</strong> ${medicineName}</p>
                    <p><strong>Quantity:</strong> ${quantity} ${selectedOption.dataset.unit || 'units'}</p>
                    <p><strong>Available Stock:</strong> ${availableStock} ${selectedOption.dataset.unit || 'units'}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Dispense',
            cancelButtonText: 'Cancel',
            zIndex: 10000,
            backdrop: true,
            allowOutsideClick: false
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

        const response = await fetch('dispense-medication.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        });

        const result = await response.json();

        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: `${medicineName} (${quantity} ${selectedOption.dataset.unit || 'units'}) dispensed successfully!`,
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                closeModal('dispenseMedicationModal');
                
                // Refresh the visits table if possible
                if (typeof loadVisits === 'function') {
                    loadVisits();
                } else {
                    // Reload the page as fallback
                    window.location.reload();
                }
            });
        } else {
            throw new Error(result.message || 'Failed to dispense medications');
        }

    } catch (error) {
        console.error('Error dispensing medications:', error);
        Swal.fire({
            icon: 'error',
            title: 'Dispensing Failed',
            text: 'Failed to dispense medications: ' + error.message,
            confirmButtonColor: '#d33'
        });
    }
}
