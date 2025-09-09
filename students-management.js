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

function openDeleteModal(studentId, studentName) {
    document.getElementById('delete_student_name').textContent = studentName;
    document.getElementById('deleteStudentModal').dataset.studentId = studentId;
    modalManager.openModal('deleteStudentModal');
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

// Confirm delete action
function confirmDelete() {
    const studentId = document.getElementById('deleteStudentModal').dataset.studentId;

    // Send AJAX request to delete the student
    const formData = new FormData();
    formData.append('student_id', studentId);

    fetch('delete_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalManager.showSuccessMessage(data.message || 'Student deleted successfully!');
            modalManager.closeModal('deleteStudentModal');
            // Reload the page to show the updated list
            setTimeout(() => location.reload(), 1000);
        } else {
            modalManager.showErrorMessage(data.message || 'Failed to delete student');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalManager.showErrorMessage('Network error. Please try again.');
    });
}

// View student details
function viewStudentDetails(studentId) {
    // In a real application, you would redirect to a student details page
    modalManager.showErrorMessage(`Viewing details for student ID: ${studentId} - Feature coming soon!`);
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


