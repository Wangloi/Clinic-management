// visit-modal.js - Visit-specific modal functions for Clinic Management System

async function editVisit(visitId) {
    try {
        const response = await fetch(`visit-actions.php?action=get-data&id=${visitId}`);
        const data = await response.json();
        
        if (data.success) {
            modalManager.populateForm('editVisitForm', {
                visit_id: data.visit_id,
                patient_type: data.patient_type,
                patient_id: data.patient_id,
                reason: data.reason,
                diagnosis: data.diagnosis,
                treatment: data.treatment,
                remarks: data.remarks
            });
            modalManager.openModal('editVisitModal');
        } else {
            modalManager.showErrorMessage('Error loading visit data: ' + data.message);
        }
    } catch (error) {
        console.error('Error fetching visit data:', error);
        modalManager.showErrorMessage('Error loading visit data. Please try again.');
    }
}

async function viewVisitDetails(visitId) {
    try {
        const response = await fetch(`visit-actions.php?action=get-details&id=${visitId}`);
        const data = await response.json();
        
        if (data.success) {
            // Populate view modal with data
            const fields = [
                'patient_name', 'patient_id', 'patient_type', 'visit_date',
                'reason', 'diagnosis', 'treatment', 'remarks', 'created_at'
            ];
            
            fields.forEach(field => {
                const element = document.getElementById(`view-${field}`);
                if (element) {
                    element.textContent = data[field] || 'N/A';
                }
            });

            // Populate dispensed medications
            const dispensedList = document.getElementById('view-dispensed-medications');
            if (dispensedList) {
                dispensedList.innerHTML = '';
                if (data.dispensed_medications && data.dispensed_medications.length > 0) {
                    data.dispensed_medications.forEach(med => {
                        const li = document.createElement('li');
                        li.textContent = `${med.medicine_name} - Quantity: ${med.quantity_dispensed}`;
                        dispensedList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'No medications dispensed';
                    dispensedList.appendChild(li);
                }
            }
            
            modalManager.openModal('viewVisitModal');
        } else {
            modalManager.showErrorMessage('Error loading visit details: ' + data.message);
        }
    } catch (error) {
        console.error('Error fetching visit details:', error);
        modalManager.showErrorMessage('Error loading visit details. Please try again.');
    }
}
