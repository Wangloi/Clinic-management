// Use centralized modal system
function openEditModal(student) {
    modalManager.populateForm('editStudentForm', {
        student_id: student.student_id,
        first_name: student.Student_Fname || '',
        middle_name: student.Student_Mname || '',
        last_name: student.Student_Lname || '',
        program: student.program_name || '',
        section: student.section_name || '',
        department: student.department_level || ''
    });
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
    if (form) {
        // Trigger the form's submit event, which will be handled by the existing event listener
        form.dispatchEvent(new Event('submit'));
    }
}

// Confirm delete action
function confirmDelete() {
    const studentId = document.getElementById('deleteStudentModal').dataset.studentId;
    
    // In a real application, you would submit a form or make an AJAX request here
    alert(`Student with ID ${studentId} would be deleted in a real application.`);
    
    // Close the modal
    modalManager.closeModal('deleteStudentModal');
    
    // Show success message
    alert('Student deleted successfully!');
    
    // In a real application, you might want to reload the page or update the table
    // window.location.reload();
}

// View student details
function viewStudentDetails(studentId) {
    // In a real application, you would redirect to a student details page
    alert(`Viewing details for student ID: ${studentId}`);
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
            handleFormSubmit(this, 'Student updated successfully!');
        });
    }

    const addForm = document.getElementById('addStudentForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'Student added successfully!');
        });
    }

    // Add event listener for program change to load sections
    const programSelect = document.getElementById('add_program');
    if (programSelect) {
        programSelect.addEventListener('change', loadSections);
    }
});

// Handle form submissions with AJAX
function handleFormSubmit(form, successMessage) {
    const formData = new FormData(form);

    // In a real application, you would use fetch or XMLHttpRequest
    // to submit the form data asynchronously

    // Simulate AJAX request
    setTimeout(() => {
        alert(successMessage);
        const modalId = form.closest('.modal').id;
        modalManager.closeModal(modalId);

        // In a real application, you might want to reload the page or update the table
        // window.location.reload();
    }, 1000);
}

// Load programs based on selected department
function loadPrograms() {
    const departmentSelect = document.getElementById('add_department');
    const programSelect = document.getElementById('add_program');
    const sectionSelect = document.getElementById('add_section');

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
function loadSections() {
    const programSelect = document.getElementById('add_program');
    const sectionSelect = document.getElementById('add_section');

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
    }
}


