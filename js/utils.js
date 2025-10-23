// Utility functions for student dashboard

// Helper functions to get display names
function getDepartmentDisplayName(departmentLevel) {
    const departmentNames = {
        'College': 'College',
        'SHS': 'Senior High School',
        'JHS': 'Junior High School',
        'Grade School': 'Grade School'
    };
    return departmentNames[departmentLevel] || departmentLevel || 'Not Assigned';
}

function getProgramDisplayName(programId) {
    if (!window.allPrograms || !programId) return 'Not provided';
    const program = window.allPrograms.find(p => p.program_id == programId);
    return program ? program.program_name : 'Not provided';
}

function getSectionDisplayName(sectionId) {
    if (!window.allSections || !sectionId) return 'Not provided';
    const section = window.allSections.find(s => s.section_id == sectionId);
    return section ? section.section_name : 'Not provided';
}

// Auto-populate student data from database
function populateStudentData() {
    console.log('populateStudentData called');
    console.log('window.studentData:', window.studentData);
    console.log('window.studentData keys:', Object.keys(window.studentData || {}));
    console.log('All studentData properties:', window.studentData);
    console.log('fname value:', window.studentData?.fname);
    console.log('mname value:', window.studentData?.mname);
    console.log('lname value:', window.studentData?.lname);

    if (window.studentData) {
        // Auto-populate name fields
        const fnameInput = document.getElementById('studentFname');
        const mnameInput = document.getElementById('studentMname');
        const lnameInput = document.getElementById('studentLname');
        const contactInput = document.getElementById('contactNumber');
        const departmentSelect = document.getElementById('studentDepartment');
        const programSelect = document.getElementById('studentProgram');
        const sectionSelect = document.getElementById('studentSection');

        console.log('Found elements:', {
            fnameInput: !!fnameInput,
            mnameInput: !!mnameInput,
            lnameInput: !!lnameInput,
            contactInput: !!contactInput
        });

        if (fnameInput) {
            // Try multiple possible field names
            const fname = window.studentData.fname || window.studentData.Student_Fname || '';
            fnameInput.value = fname;
            console.log('Set first name to:', fname);
        }
        if (mnameInput) {
            const mname = window.studentData.mname || window.studentData.Student_Mname || '';
            mnameInput.value = mname;
            console.log('Set middle name to:', mname);
        }
        if (lnameInput) {
            const lname = window.studentData.lname || window.studentData.Student_Lname || '';
            lnameInput.value = lname;
            console.log('Set last name to:', lname);
        }
        if (contactInput) contactInput.value = window.studentData.contact_number || '';

        // Auto-populate department, program, and section (now as text inputs)
        if (departmentSelect && window.studentData.department_level) {
            // Set department display name
            departmentSelect.value = getDepartmentDisplayName(window.studentData.department_level);
        }

        if (programSelect && window.studentData.program_name) {
            // Set program name directly
            programSelect.value = window.studentData.program_name;
        }

        if (sectionSelect) {
            // Try multiple possible section field names
            const sectionName = window.studentData.section_name || window.studentData.section || 'Not Assigned';
            sectionSelect.value = sectionName;
            console.log('Set section to:', sectionName, 'from data:', window.studentData.section_name, window.studentData.section);
        }

        // Also save to healthQuestData for consistency (using names, not IDs)
        healthQuestData.studentFname = window.studentData.fname || '';
        healthQuestData.studentMname = window.studentData.mname || '';
        healthQuestData.studentLname = window.studentData.lname || '';
        healthQuestData.contactNumber = window.studentData.contact_number || '';
        healthQuestData.studentDepartment = window.studentData.department_level || '';
        healthQuestData.studentProgram = window.studentData.program_name || '';
        healthQuestData.studentSection = window.studentData.section_name || '';
    }
}

// Restore form values from saved data when navigating between steps
function restoreFormValues(step) {
    // Step 1 - Personal Information & Vital Signs
    if (step === 1) {
        const fnameInput = document.getElementById('studentFname');
        const mnameInput = document.getElementById('studentMname');
        const lnameInput = document.getElementById('studentLname');
        const sexSelect = document.getElementById('studentSex');
        const programInput = document.getElementById('studentProgram');
        const sectionSelect = document.getElementById('studentSection');
        const contactInput = document.getElementById('contactNumber');
        const birthdayInput = document.getElementById('studentBirthday');
        const ageInput = document.getElementById('studentAge');
        const homeAddressInput = document.getElementById('homeAddress');
        const heightInput = document.getElementById('height');
        const weightInput = document.getElementById('weight');
        const bloodPressureInput = document.getElementById('bloodPressure');
        const heartRateInput = document.getElementById('heartRate');
        const respiratoryRateInput = document.getElementById('respiratoryRate');
        const temperatureInput = document.getElementById('temperature');

        if (fnameInput && healthQuestData.studentFname) fnameInput.value = healthQuestData.studentFname;
        if (mnameInput && healthQuestData.studentMname) mnameInput.value = healthQuestData.studentMname;
        if (lnameInput && healthQuestData.studentLname) lnameInput.value = healthQuestData.studentLname;
        if (sexSelect && healthQuestData.studentSex) sexSelect.value = healthQuestData.studentSex;
        if (programInput && healthQuestData.studentProgram) programInput.value = healthQuestData.studentProgram;
        if (sectionSelect && healthQuestData.studentSection) sectionSelect.value = healthQuestData.studentSection;
        if (contactInput && healthQuestData.contactNumber) contactInput.value = healthQuestData.contactNumber;
        if (birthdayInput && healthQuestData.studentBirthday) birthdayInput.value = healthQuestData.studentBirthday;
        if (ageInput && healthQuestData.studentAge) ageInput.value = healthQuestData.studentAge;
        if (homeAddressInput && healthQuestData.homeAddress) homeAddressInput.value = healthQuestData.homeAddress;
        if (heightInput && healthQuestData.height) heightInput.value = healthQuestData.height;
        if (weightInput && healthQuestData.weight) weightInput.value = healthQuestData.weight;
        if (bloodPressureInput && healthQuestData.bloodPressure) bloodPressureInput.value = healthQuestData.bloodPressure;
        if (heartRateInput && healthQuestData.heartRate) heartRateInput.value = healthQuestData.heartRate;
        if (respiratoryRateInput && healthQuestData.respiratoryRate) respiratoryRateInput.value = healthQuestData.respiratoryRate;
        if (temperatureInput && healthQuestData.temperature) temperatureInput.value = healthQuestData.temperature;
    }

    // Step 5 - Current Health Concerns
    if (step === 5) {
        const presentConcernsInput = document.getElementById('presentConcerns');
        const currentMedicationsVitaminsInput = document.getElementById('currentMedicationsVitamins');
        const additionalNotesInput = document.getElementById('additionalNotes');

        if (presentConcernsInput && healthQuestData.presentConcerns) presentConcernsInput.value = healthQuestData.presentConcerns;
        if (currentMedicationsVitaminsInput && healthQuestData.currentMedicationsVitamins) currentMedicationsVitaminsInput.value = healthQuestData.currentMedicationsVitamins;
        if (additionalNotesInput && healthQuestData.additionalNotes) additionalNotesInput.value = healthQuestData.additionalNotes;
    }
}

// Show/hide cervical cancer vaccine and menstruation section based on gender
function updateCervicalCancerVisibility() {
    // Try to get the sex value from the current step or saved data
    let isFemale = false;
    const sexSelect = document.getElementById('studentSex');

    if (sexSelect && sexSelect.value) {
        isFemale = sexSelect.value === 'Female';
    } else if (healthQuestData.studentSex) {
        // Use saved data if select element doesn't exist or has no value
        isFemale = healthQuestData.studentSex === 'Female';
    }

    // Handle cervical cancer vaccine section
    const cervicalSection = document.getElementById('cervicalCancerSection');
    if (cervicalSection) {
        if (isFemale) {
            cervicalSection.style.display = 'block';
        } else {
            cervicalSection.style.display = 'none';
            // Uncheck the checkbox if hidden
            const checkbox = document.getElementById('cervicalCancerVaccine');
            if (checkbox) {
                checkbox.checked = false;
            }
        }
    }

    // Handle menstruation section
    const menstruationSection = document.getElementById('menstruationSection');
    if (menstruationSection) {
        if (isFemale) {
            menstruationSection.style.display = 'block';
        } else {
            menstruationSection.style.display = 'none';
            // Clear menstruation fields if hidden
            const menarcheAge = document.getElementById('menarcheAge');
            const menstrualDays = document.getElementById('menstrualDays');
            const padsConsumed = document.getElementById('padsConsumed');
            const menstrualProblems = document.getElementById('menstrualProblems');

            if (menarcheAge) menarcheAge.value = '';
            if (menstrualDays) menstrualDays.value = '';
            if (padsConsumed) padsConsumed.value = '';
            if (menstrualProblems) menstrualProblems.value = '';
        }
    }
}

// Toggle allergies remarks section
function toggleAllergiesRemarks() {
    toggleRemarks('allergies');
}

// Generic function to toggle YES/NO remarks sections
function toggleRemarks(category) {
    const yesRadio = document.getElementById(category + 'Yes');
    const remarksSection = document.getElementById(category + 'RemarksSection');

    if (yesRadio && remarksSection) {
        if (yesRadio.checked) {
            remarksSection.style.display = 'block';
        } else {
            remarksSection.style.display = 'none';
            // Clear the remarks if NO is selected
            const remarksTextarea = document.getElementById(category + 'Remarks');
            if (remarksTextarea) {
                remarksTextarea.value = '';
            }
        }
    }
}

// Helper function to generate YES/NO sections
function generateYesNoSection(id, title, question, placeholder, bgColor = '#f0f8ff', borderColor = '#b3d9ff') {
    return `
        <div style="background: ${bgColor}; padding: 15px; border-radius: 8px; border: 1px solid ${borderColor}; margin-bottom: 15px;">
            <h5 style="margin-bottom: 15px; color: #333; font-weight: 600;">${title}</h5>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #555;">${question}</label>
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="has${id}" value="YES" id="${id}Yes" style="margin-right: 8px;" onchange="toggleRemarks('${id}')">
                        <span style="font-weight: 600; color: #d32f2f;">YES</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="has${id}" value="NO" id="${id}No" style="margin-right: 8px;" onchange="toggleRemarks('${id}')">
                        <span style="font-weight: 600; color: #388e3c;">NO</span>
                    </label>
                </div>
                <div id="${id}RemarksSection" style="display: none;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Please provide details:</label>
                    <textarea id="${id}Remarks" placeholder="${placeholder}" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                </div>
            </div>
        </div>
    `;
}

// Toggle other vaccines text area
function toggleOtherVaccinesText() {
    const checkbox = document.getElementById('otherVaccines');
    const section = document.getElementById('otherVaccinesSection');
    const textarea = document.getElementById('otherVaccinesText');

    if (checkbox && section) {
        if (checkbox.checked) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
            if (textarea) {
                textarea.value = '';
            }
        }
    }
}

// Load programs based on selected department
function loadProgramsForStudent() {
    const departmentSelect = document.getElementById('studentDepartment');
    const programSelect = document.getElementById('studentProgram');
    const sectionSelect = document.getElementById('studentSection');

    if (!departmentSelect || !programSelect || !window.allPrograms) return;

    const selectedDepartment = departmentSelect.value;

    // Clear program and section dropdowns
    programSelect.innerHTML = '<option value="">Select Program</option>';
    sectionSelect.innerHTML = '<option value="">Select Section</option>';

    if (selectedDepartment) {
        // Filter programs by department
        const filteredPrograms = window.allPrograms.filter(program =>
            program.department_level === selectedDepartment
        );

        // Populate program dropdown
        filteredPrograms.forEach(program => {
            const option = document.createElement('option');
            option.value = program.program_id;
            option.textContent = program.program_name;
            programSelect.appendChild(option);
        });
    }
}

// Load sections based on selected program
function loadSectionsForStudent() {
    const programSelect = document.getElementById('studentProgram');
    const sectionSelect = document.getElementById('studentSection');

    if (!programSelect || !sectionSelect || !window.allSections) return;

    const selectedProgramId = programSelect.value;

    // Clear section dropdown
    sectionSelect.innerHTML = '<option value="">Select Section</option>';

    if (selectedProgramId) {
        // Filter sections by program
        const filteredSections = window.allSections.filter(section =>
            section.program_id == selectedProgramId
        );

        // Populate section dropdown
        filteredSections.forEach(section => {
            const option = document.createElement('option');
            option.value = section.section_id;
            option.textContent = section.section_name;
            sectionSelect.appendChild(option);
        });
    }
}

// Toggle dropdown function
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}
