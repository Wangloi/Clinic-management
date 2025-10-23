// Use centralized modal system
function openEditModal(student) {
    // Find program_id from program_name
    let programId = '';
    if (student.program_name && typeof allPrograms !== 'undefined') {
        const program = allPrograms.find(p => p.program_name === student.program_name);
        if (program) {
            programId = program.program_id;
        }
    }

    modalManager.populateForm('editStudentForm', {
        student_id: student.student_id,
        first_name: student.Student_Fname || '',
        middle_name: student.Student_Mname || '',
        last_name: student.Student_Lname || '',
        program: programId,
        section: student.section_id || '',
        department: student.department_level || ''
    });

    // Set the display input for student_id
    const displayInput = document.getElementById('edit_student_id_display');
    if (displayInput) {
        displayInput.value = student.student_id || '';
    }

    // Load sections for the selected program
    if (programId) {
        loadSections(student.section_id);
    }

    modalManager.openModal('editStudentModal');
}

function deleteStudent(studentId, studentName) {
    // Confirm deletion with user
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete student: ${studentName}. This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Deleting student:', studentId);

            // Send delete request to server
            const formData = new FormData();
            formData.append('student_id', studentId);

            fetch('delete_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || 'Student deleted successfully!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete student',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network error. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function openAddStudentModal() {
    modalManager.clearForm('addStudentForm');
    modalManager.openModal('addStudentModal');
}

function submitAddStudentForm() {
    const form = document.getElementById('addStudentForm');
    const formData = new FormData(form);
    const submitBtn = document.querySelector('#addStudentModal button[type="button"]:not(.text-gray-600)');

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
            modalManager.showSuccessMessage(data.message || 'Student added successfully');
            closeModal('addStudentModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            modalManager.showErrorMessage(data.message || 'Failed to add student');
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

// Close all modals to prevent layering issues
function closeAllModals() {
    const modals = [
        'editStudentModal',
        'addStudentModal', 
        'studentDetailsModal'
    ];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    });
    
    // Remove any backdrop blur from body
    document.body.style.overflow = 'auto';
}

// Enhanced modal close function
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Add keyboard event listener for Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAllModals();
    }
});

// Add click outside modal to close
document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        if (!modal.classList.contains('hidden') && event.target === modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });
});

// View student details with health records
async function viewStudentDetails(studentId) {
    try {
        // Close any other open modals first
        closeAllModals();
        
        // Show the modal and loading state
        const modal = document.getElementById('studentDetailsModal');
        const loading = document.getElementById('studentDetailsLoading');
        const content = document.getElementById('studentDetailsContent');
        
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        
        // Fetch student data and health records
        const response = await fetch(`admin-health-records-api.php?action=get_student_health_record&student_id=${studentId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load student details');
        }
        
        // Populate student information
        const student = data.student;
        document.getElementById('studentFullName').textContent = student.name || 'N/A';
        document.getElementById('studentIdDisplay').textContent = student.id || 'N/A';
        document.getElementById('studentContact').textContent = student.contact_number || 'N/A';
        document.getElementById('studentSection').textContent = student.section_name || 'N/A';
        document.getElementById('studentProgram').textContent = student.program_name || 'N/A';
        document.getElementById('studentDepartment').textContent = getDepartmentDisplayName(student.department_level) || 'N/A';
        
        // Handle health records
        const healthRecord = data.health_record;
        const noHealthRecord = document.getElementById('noHealthRecord');
        const healthRecordContent = document.getElementById('healthRecordContent');
        
        if (!healthRecord.exists) {
            // Show no health record state
            noHealthRecord.classList.remove('hidden');
            healthRecordContent.classList.add('hidden');
        } else {
            // Show health record content
            noHealthRecord.classList.add('hidden');
            healthRecordContent.classList.remove('hidden');
            
            // Populate health information
            document.getElementById('healthSex').textContent = healthRecord.student_sex || 'N/A';
            document.getElementById('healthBirthday').textContent = formatDate(healthRecord.student_birthday) || 'N/A';
            document.getElementById('healthAge').textContent = healthRecord.student_age || 'N/A';
            document.getElementById('healthEducationLevel').textContent = healthRecord.education_level || 'N/A';
            document.getElementById('healthAddress').textContent = healthRecord.home_address || 'N/A';
            
            // Vital signs
            document.getElementById('healthHeight').textContent = healthRecord.height ? `${healthRecord.height} cm` : 'N/A';
            document.getElementById('healthWeight').textContent = healthRecord.weight ? `${healthRecord.weight} kg` : 'N/A';
            document.getElementById('healthBP').textContent = healthRecord.blood_pressure || 'N/A';
            document.getElementById('healthHR').textContent = healthRecord.heart_rate ? `${healthRecord.heart_rate} bpm` : 'N/A';
            document.getElementById('healthTemp').textContent = healthRecord.temperature ? `${healthRecord.temperature}°C` : 'N/A';
            
            // Health conditions
            const allergiesInfo = document.getElementById('allergiesInfo');
            const asthmaInfo = document.getElementById('asthmaInfo');
            const healthProblemsInfo = document.getElementById('healthProblemsInfo');
            const noHealthConditions = document.getElementById('noHealthConditions');
            
            console.log('Health record data:', healthRecord);
            console.log('Checking health conditions...');
            
            let hasConditions = false;
            
            // Check allergies
            if (healthRecord.has_allergies === 'YES' || healthRecord.has_allergies === 'yes' || healthRecord.has_allergies === '1') {
                allergiesInfo.classList.remove('hidden');
                document.getElementById('allergiesRemarks').textContent = healthRecord.allergies_remarks || 'No details provided';
                hasConditions = true;
            } else {
                allergiesInfo.classList.add('hidden');
            }
            
            // Check asthma
            if (healthRecord.has_asthma === 'YES' || healthRecord.has_asthma === 'yes' || healthRecord.has_asthma === '1') {
                asthmaInfo.classList.remove('hidden');
                document.getElementById('asthmaRemarks').textContent = healthRecord.asthma_remarks || 'No details provided';
                hasConditions = true;
            } else {
                asthmaInfo.classList.add('hidden');
            }
            
            // Check health problems (note: database field is has_healthproblem, not has_health_problems)
            if (healthRecord.has_healthproblem === 'YES' || healthRecord.has_health_problems === 'YES' || 
                healthRecord.has_healthproblem === 'yes' || healthRecord.has_health_problems === 'yes' || 
                healthRecord.has_healthproblem === '1' || healthRecord.has_health_problems === '1') {
                healthProblemsInfo.classList.remove('hidden');
                document.getElementById('healthProblemsRemarks').textContent = 
                    healthRecord.healthproblem_remarks || healthRecord.health_problems_remarks || 'No details provided';
                hasConditions = true;
            } else {
                healthProblemsInfo.classList.add('hidden');
            }
            
            // Check other health conditions
            const otherConditions = [
                'has_medicines', 'has_vaccines', 'has_foods', 'has_other', 'has_earinfection', 
                'has_potty', 'has_uti', 'has_chickenpox', 'has_dengue', 'has_anemia', 
                'has_gastritis', 'has_pneumonia', 'has_obesity', 'has_covid19', 'has_otherconditions'
            ];
            
            otherConditions.forEach(condition => {
                if (healthRecord[condition] === 'YES' || healthRecord[condition] === 'yes' || healthRecord[condition] === '1') {
                    hasConditions = true;
                }
            });
            
            // Show/hide no conditions message
            console.log('Has conditions:', hasConditions);
            if (hasConditions) {
                noHealthConditions.classList.add('hidden');
                console.log('Hiding "no conditions" message');
            } else {
                noHealthConditions.classList.remove('hidden');
                console.log('Showing "no conditions" message');
            }
            
            // Medications and notes
            document.getElementById('healthMedications').textContent = healthRecord.current_medications_vitamins || 'None reported';
            document.getElementById('healthNotes').textContent = healthRecord.additional_notes || 'No additional notes';
            
            // Record information
            document.getElementById('healthSubmitted').textContent = formatDateTime(healthRecord.submitted_at) || 'N/A';
            document.getElementById('healthStatus').textContent = healthRecord.submitted_at ? 'Completed' : 'In Progress';
        }
        
        // Hide loading and show content
        loading.classList.add('hidden');
        content.classList.remove('hidden');
        
    } catch (error) {
        console.error('Error loading student details:', error);
        modalManager.showErrorMessage('Failed to load student details: ' + error.message);
        
        // Hide the modal on error
        const modal = document.getElementById('studentDetailsModal');
        modal.classList.add('hidden');
    }
}

// Helper function to get department display name (matches admin students table)
function getDepartmentDisplayName(departmentLevel) {
    switch (departmentLevel) {
        case 'College':
            return 'College';
        case 'SHS':
            return 'Senior High School';
        case 'JHS':
            return 'Junior High School';
        case 'Grade School':
            return 'Grade School';
        default:
            return 'N/A';
    }
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return null;
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Helper function to format date and time
function formatDateTime(dateString) {
    if (!dateString) return null;
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Pagination functions
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

    // Handle form submissions with AJAX for better UX
    const editForm = document.getElementById('editStudentForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            modalManager.handleFormSubmit(this);
        });
    }

    const addForm = document.getElementById('addStudentForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            modalManager.handleFormSubmit(this);
        });
    }

    // Add event listeners for program change to load sections
    const addProgramSelect = document.getElementById('add_program');
    if (addProgramSelect) {
        addProgramSelect.addEventListener('change', loadSections);
    }
    const editProgramSelect = document.getElementById('edit_program');
    if (editProgramSelect) {
        editProgramSelect.addEventListener('change', function() {
            const currentSection = document.getElementById('edit_section').value;
            loadSections(currentSection);
        });
    }

    // Add event listeners for department change to load programs
    const addDepartmentSelect = document.getElementById('add_department');
    if (addDepartmentSelect) {
        addDepartmentSelect.addEventListener('change', loadPrograms);
    }
    const editDepartmentSelect = document.getElementById('edit_department');
    if (editDepartmentSelect) {
        editDepartmentSelect.addEventListener('change', function() {
            loadPrograms();
        });
    }
});



// Load programs based on selected department
function loadPrograms() {
    let departmentSelect = document.getElementById('add_department');
    let programSelect = document.getElementById('add_program');
    let sectionSelect = document.getElementById('add_section');

    // If add elements not found, try edit elements
    if (!departmentSelect) {
        departmentSelect = document.getElementById('edit_department');
        programSelect = document.getElementById('edit_program');
        sectionSelect = document.getElementById('edit_section');
    }

    const selectedDepartment = departmentSelect.value;

    // Clear current program options
    programSelect.innerHTML = '<option value="">Select Program</option>';

    // Clear section options
    sectionSelect.innerHTML = '<option value="">Select Section</option>';

    let programsToShow = [];

    if (selectedDepartment && typeof allPrograms !== 'undefined') {
        // Filter programs by selected department
        programsToShow = allPrograms.filter(program =>
            program.department_level === selectedDepartment
        );
    } else if (typeof allPrograms !== 'undefined') {
        // Show all programs if no department selected
        programsToShow = allPrograms;
    }

    // Add programs to select
    programsToShow.forEach(program => {
        const option = document.createElement('option');
        option.value = program.program_id;
        option.textContent = program.program_name;
        programSelect.appendChild(option);
    });
}

// Load sections based on selected program
function loadSections(selectedSectionId = null) {
    const programSelect = document.getElementById('add_program');
    const sectionSelect = document.getElementById('add_section');

    // For edit modal, if programSelect is not found, try edit_program
    if (!programSelect) {
        programSelect = document.getElementById('edit_program');
    }
    if (!sectionSelect) {
        sectionSelect = document.getElementById('edit_section');
    }

    const selectedProgramId = programSelect.value;

    // Clear current section options
    sectionSelect.innerHTML = '<option value="">Select Section</option>';

    if (selectedProgramId && typeof allSections !== 'undefined') {
        // Filter sections by selected program
        const filteredSections = allSections.filter(section =>
            section.program_id == selectedProgramId
        );

        // Add filtered sections to select
        filteredSections.forEach(section => {
            const option = document.createElement('option');
            option.value = section.section_id;
            option.textContent = section.section_name;
            sectionSelect.appendChild(option);
        });

        // If a selectedSectionId is provided and exists in the new options, set it as selected
        if (selectedSectionId && filteredSections.some(section => section.section_id == selectedSectionId)) {
            sectionSelect.value = selectedSectionId;
        }
    }
}


