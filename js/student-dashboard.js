// Navigation functionality
document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href.startsWith('#')) {
            e.preventDefault();

            // Remove active class from all nav links
            document.querySelectorAll('nav a').forEach(navLink => {
                navLink.classList.remove('active');
            });

            // Add active class to clicked link
            this.classList.add('active');

            // Show/hide content sections
            const target = document.querySelector(href);
            document.querySelectorAll('#home, #goals, #about').forEach(div => {
                div.classList.add('hidden');
                if (div.id === 'home') {
                    div.classList.remove('flex');
                }
            });
            
            target.classList.remove('hidden');
            if (target.id === 'home') {
                target.classList.add('flex');
            }
        }
        // If href does not start with '#', allow default behavior (e.g., logout.php)
    });
});

// Initialize health questionnaire data
let currentStep = 1;
let healthQuestData = {};
let educationLevel = 'basic'; // 'basic' for Grade/JHS/SHS, 'college' for College

// Backup storage for critical data
let backupData = {};

// Add global event delegation for all form inputs
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'studentBirthday') {
        console.log('Birthday changed via event delegation:', e.target.value);
        healthQuestData.studentBirthday = e.target.value;
        healthQuestData.student_birthday = e.target.value;
        // Backup storage
        backupData.birthday = e.target.value;
        localStorage.setItem('healthQuest_birthday', e.target.value);
        console.log('🔒 Backed up birthday:', e.target.value);
        // Small delay to ensure value is set
        setTimeout(() => {
            calculateAge(e.target.value);
        }, 10);
    }
    
    // Save other form fields immediately
    if (e.target && e.target.id === 'contactNumber') {
        console.log('Contact number changed:', e.target.value);
        healthQuestData.contactNumber = e.target.value;
        // Backup storage
        backupData.contactNumber = e.target.value;
        localStorage.setItem('healthQuest_contact', e.target.value);
        console.log('🔒 Backed up contact:', e.target.value);
    }
    
    if (e.target && e.target.id === 'homeAddress') {
        console.log('Home address changed:', e.target.value);
        healthQuestData.homeAddress = e.target.value;
        healthQuestData.home_address = e.target.value;
        // Backup storage
        backupData.homeAddress = e.target.value;
        localStorage.setItem('healthQuest_address', e.target.value);
        console.log('🔒 Backed up address:', e.target.value);
    }
    
    if (e.target && e.target.id === 'studentSex') {
        console.log('Sex changed:', e.target.value);
        healthQuestData.studentSex = e.target.value;
        healthQuestData.student_sex = e.target.value;
        // Backup storage
        backupData.sex = e.target.value;
        localStorage.setItem('healthQuest_sex', e.target.value);
        console.log('🔒 Backed up sex:', e.target.value);
    }
});

document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'studentBirthday') {
        console.log('Birthday input via event delegation:', e.target.value);
        healthQuestData.studentBirthday = e.target.value;
        healthQuestData.student_birthday = e.target.value;
        // Small delay to ensure value is set
        setTimeout(() => {
            calculateAge(e.target.value);
        }, 10);
    }
    
    // Save other form fields immediately on input
    if (e.target && e.target.id === 'contactNumber') {
        healthQuestData.contactNumber = e.target.value;
    }
    
    if (e.target && e.target.id === 'homeAddress') {
        healthQuestData.homeAddress = e.target.value;
        healthQuestData.home_address = e.target.value;
    }
});

// Simple and reliable age calculation
function calculateAge(birthdayValue) {
    // Get the birthday value from parameter or input field
    if (!birthdayValue) {
        const birthdayInput = document.getElementById('studentBirthday');
        if (birthdayInput && birthdayInput.value) {
            birthdayValue = birthdayInput.value;
        } else {
            return; // No birthday to calculate from
        }
    }
    
    // Get the age input field
    const ageInput = document.getElementById('studentAge');
    if (!ageInput) {
        return; // No age field to update
    }
    
    // Calculate age
    const birthday = new Date(birthdayValue);
    const today = new Date();
    
    // Basic validation
    if (isNaN(birthday.getTime()) || birthday > today) {
        ageInput.value = '';
        return;
    }
    
    // Calculate age
    let age = today.getFullYear() - birthday.getFullYear();
    const monthDiff = today.getMonth() - birthday.getMonth();
    
    // Adjust if birthday hasn't occurred this year
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
        age--;
    }
    
    // Set the age in the input field
    if (age >= 0 && age <= 150) {
        ageInput.value = age;
        healthQuestData.studentAge = age;
        
        // Visual feedback
        ageInput.style.backgroundColor = '#e8f5e8';
        setTimeout(() => {
            ageInput.style.backgroundColor = '';
        }, 500);
    }
}

// Make calculateAge globally accessible for testing
window.calculateAge = calculateAge;

// Test function for manual testing
window.testAgeCalculation = function(testBirthday) {
    console.log('=== MANUAL AGE TEST ===');
    console.log('Test birthday:', testBirthday || '2000-01-01');
    
    const birthdayInput = document.getElementById('studentBirthday');
    const ageInput = document.getElementById('studentAge');
    
    if (birthdayInput) {
        birthdayInput.value = testBirthday || '2000-01-01';
        console.log('Set birthday input to:', birthdayInput.value);
    }
    
    calculateAge(testBirthday || '2000-01-01');
    
    if (ageInput) {
        console.log('Final age input value:', ageInput.value);
    }
    
    console.log('=== END TEST ===');
};

// Simple function to directly set age for testing
window.setAge = function(age) {
    const ageInput = document.getElementById('studentAge');
    if (ageInput) {
        console.log('Setting age directly to:', age);
        ageInput.value = age;
        ageInput.style.backgroundColor = 'yellow';
        console.log('Age input value is now:', ageInput.value);
        setTimeout(() => {
            ageInput.style.backgroundColor = '';
        }, 1000);
    } else {
        console.log('Age input not found!');
    }
};

// Test function to check current form data
window.checkFormData = function() {
    console.log('=== FORM DATA CHECK ===');
    
    const contactInput = document.getElementById('contactNumber');
    const birthdayInput = document.getElementById('studentBirthday');
    const homeAddressInput = document.getElementById('homeAddress');
    const sexInput = document.getElementById('studentSex');
    
    console.log('Contact input:', contactInput ? contactInput.value : 'NOT FOUND');
    console.log('Birthday input:', birthdayInput ? birthdayInput.value : 'NOT FOUND');
    console.log('Home address input:', homeAddressInput ? homeAddressInput.value : 'NOT FOUND');
    console.log('Sex input:', sexInput ? sexInput.value : 'NOT FOUND');
    
    console.log('Current healthQuestData:', {
        contactNumber: healthQuestData.contactNumber,
        studentBirthday: healthQuestData.studentBirthday,
        homeAddress: healthQuestData.homeAddress,
        studentSex: healthQuestData.studentSex
    });
    
    console.log('=== END CHECK ===');
};

// Function to manually save current form data
window.saveCurrentData = function() {
    console.log('=== MANUAL SAVE ===');
    
    const contactInput = document.getElementById('contactNumber');
    const birthdayInput = document.getElementById('studentBirthday');
    const homeAddressInput = document.getElementById('homeAddress');
    const sexInput = document.getElementById('studentSex');
    
    if (contactInput) {
        healthQuestData.contactNumber = contactInput.value;
        console.log('Manually saved contact:', contactInput.value);
    }
    if (birthdayInput) {
        healthQuestData.studentBirthday = birthdayInput.value;
        healthQuestData.student_birthday = birthdayInput.value;
        console.log('Manually saved birthday:', birthdayInput.value);
    }
    if (homeAddressInput) {
        healthQuestData.homeAddress = homeAddressInput.value;
        healthQuestData.home_address = homeAddressInput.value;
        console.log('Manually saved home address:', homeAddressInput.value);
    }
    if (sexInput) {
        healthQuestData.studentSex = sexInput.value;
        healthQuestData.student_sex = sexInput.value;
        console.log('Manually saved sex:', sexInput.value);
    }
    
    console.log('Updated healthQuestData:', healthQuestData);
    console.log('=== END MANUAL SAVE ===');
};

// Function to load saved data into form fields
function loadSavedDataIntoFields() {
    console.log('=== LOADING SAVED DATA ===');
    console.log('Current healthQuestData:', healthQuestData);
    
    // Load basic personal info
    const contactInput = document.getElementById('contactNumber');
    const birthdayInput = document.getElementById('studentBirthday');
    const homeAddressInput = document.getElementById('homeAddress');
    const sexInput = document.getElementById('studentSex');
    const ageInput = document.getElementById('studentAge');
    
    console.log('Found elements:', {
        contactInput: !!contactInput,
        birthdayInput: !!birthdayInput,
        homeAddressInput: !!homeAddressInput,
        sexInput: !!sexInput,
        ageInput: !!ageInput
    });
    
    // Force load contact number
    if (contactInput) {
        const contactValue = healthQuestData.contactNumber || '';
        contactInput.value = contactValue;
        console.log('Set contact number to:', contactValue);
    }
    
    // Force load birthday
    if (birthdayInput) {
        const birthdayValue = healthQuestData.studentBirthday || '';
        birthdayInput.value = birthdayValue;
        console.log('Set birthday to:', birthdayValue);
        if (birthdayValue) {
            calculateAge(birthdayValue);
        }
    }
    
    // Force load home address
    if (homeAddressInput) {
        const addressValue = healthQuestData.homeAddress || '';
        homeAddressInput.value = addressValue;
        console.log('Set home address to:', addressValue);
        console.log('Home address element type:', homeAddressInput.tagName);
    }
    
    // Force load sex
    if (sexInput) {
        const sexValue = healthQuestData.studentSex || '';
        sexInput.value = sexValue;
        console.log('Set sex to:', sexValue);
    }
    
    // Force load age
    if (ageInput) {
        const ageValue = healthQuestData.studentAge || '';
        ageInput.value = ageValue;
        console.log('Set age to:', ageValue);
    }
    
    // Load vital signs
    const heightInput = document.getElementById('height');
    const weightInput = document.getElementById('weight');
    const bloodPressureInput = document.getElementById('bloodPressure');
    const heartRateInput = document.getElementById('heartRate');
    const respiratoryRateInput = document.getElementById('respiratoryRate');
    const temperatureInput = document.getElementById('temperature');
    
    if (heightInput && healthQuestData.height) heightInput.value = healthQuestData.height;
    if (weightInput && healthQuestData.weight) weightInput.value = healthQuestData.weight;
    if (bloodPressureInput && healthQuestData.bloodPressure) bloodPressureInput.value = healthQuestData.bloodPressure;
    if (heartRateInput && healthQuestData.heartRate) heartRateInput.value = healthQuestData.heartRate;
    if (respiratoryRateInput && healthQuestData.respiratoryRate) respiratoryRateInput.value = healthQuestData.respiratoryRate;
    if (temperatureInput && healthQuestData.temperature) temperatureInput.value = healthQuestData.temperature;
    
    console.log('=== FINISHED LOADING SAVED DATA ===');
}

// Manual function to force reload data into current visible fields
window.forceReloadData = function() {
    console.log('=== FORCE RELOAD DATA ===');
    
    // Get current visible elements
    const contactInput = document.getElementById('contactNumber');
    const birthdayInput = document.getElementById('studentBirthday');
    const homeAddressInput = document.getElementById('homeAddress');
    const sexInput = document.getElementById('studentSex');
    
    console.log('Current healthQuestData:', healthQuestData);
    console.log('Visible elements:', {
        contact: !!contactInput,
        birthday: !!birthdayInput,
        homeAddress: !!homeAddressInput,
        sex: !!sexInput
    });
    
    // Force set values
    if (contactInput && healthQuestData.contactNumber) {
        contactInput.value = healthQuestData.contactNumber;
        contactInput.style.backgroundColor = 'lightblue';
        setTimeout(() => contactInput.style.backgroundColor = '', 1000);
        console.log('✅ Set contact:', healthQuestData.contactNumber);
    }
    
    if (birthdayInput && healthQuestData.studentBirthday) {
        birthdayInput.value = healthQuestData.studentBirthday;
        birthdayInput.style.backgroundColor = 'lightgreen';
        setTimeout(() => birthdayInput.style.backgroundColor = '', 1000);
        console.log('✅ Set birthday:', healthQuestData.studentBirthday);
    }
    
    if (homeAddressInput && healthQuestData.homeAddress) {
        homeAddressInput.value = healthQuestData.homeAddress;
        homeAddressInput.style.backgroundColor = 'lightyellow';
        setTimeout(() => homeAddressInput.style.backgroundColor = '', 1000);
        console.log('✅ Set home address:', healthQuestData.homeAddress);
    }
    
    if (sexInput && healthQuestData.studentSex) {
        sexInput.value = healthQuestData.studentSex;
        sexInput.style.backgroundColor = 'lightpink';
        setTimeout(() => sexInput.style.backgroundColor = '', 1000);
        console.log('✅ Set sex:', healthQuestData.studentSex);
    }
    
    console.log('=== END FORCE RELOAD ===');
};

// Function to restore data from backup
window.restoreFromBackup = function() {
    console.log('=== RESTORING FROM BACKUP ===');
    console.log('Backup data:', backupData);
    console.log('LocalStorage data:', {
        contact: localStorage.getItem('healthQuest_contact'),
        birthday: localStorage.getItem('healthQuest_birthday'),
        address: localStorage.getItem('healthQuest_address'),
        sex: localStorage.getItem('healthQuest_sex')
    });
    
    // Restore from backup or localStorage
    if (backupData.contactNumber || localStorage.getItem('healthQuest_contact')) {
        healthQuestData.contactNumber = backupData.contactNumber || localStorage.getItem('healthQuest_contact');
        console.log('✅ Restored contact:', healthQuestData.contactNumber);
    }
    
    if (backupData.birthday || localStorage.getItem('healthQuest_birthday')) {
        healthQuestData.studentBirthday = backupData.birthday || localStorage.getItem('healthQuest_birthday');
        healthQuestData.student_birthday = healthQuestData.studentBirthday;
        console.log('✅ Restored birthday:', healthQuestData.studentBirthday);
    }
    
    if (backupData.homeAddress || localStorage.getItem('healthQuest_address')) {
        healthQuestData.homeAddress = backupData.homeAddress || localStorage.getItem('healthQuest_address');
        healthQuestData.home_address = healthQuestData.homeAddress;
        console.log('✅ Restored address:', healthQuestData.homeAddress);
    }
    
    if (backupData.sex || localStorage.getItem('healthQuest_sex')) {
        healthQuestData.studentSex = backupData.sex || localStorage.getItem('healthQuest_sex');
        healthQuestData.student_sex = healthQuestData.studentSex;
        console.log('✅ Restored sex:', healthQuestData.studentSex);
    }
    
    console.log('Updated healthQuestData:', healthQuestData);
    console.log('=== END RESTORE ===');
};

// Function to restore all health data from localStorage
window.restoreAllHealthData = function() {
    console.log('=== RESTORING ALL HEALTH DATA ===');
    
    // Restore basic personal info
    restoreFromBackup();
    
    // Restore health conditions
    const conditions = ['allergies', 'medicines', 'vaccines', 'foods', 'other', 'asthma', 'healthproblem',
                       'earinfection', 'potty', 'uti', 'chickenpox', 'dengue', 'anemia', 'gastritis',
                       'pneumonia', 'obesity', 'covid19', 'otherconditions'];
    
    conditions.forEach(condition => {
        const hasCondition = localStorage.getItem('healthQuest_has_' + condition);
        const remarks = localStorage.getItem('healthQuest_' + condition + '_remarks');
        
        if (hasCondition) {
            healthQuestData['has_' + condition] = hasCondition;
            healthQuestData[condition + '_remarks'] = remarks || '';
            console.log('✅ Restored health condition:', condition, '=', hasCondition);
        }
    });
    
    console.log('Final healthQuestData:', healthQuestData);
    console.log('=== END RESTORE ALL ===');
};

// Close health questionnaire function
function closeHealthQuest() {
    Swal.fire({
        title: 'Close Health Questionnaire?',
        text: 'Are you sure you want to close the health questionnaire? Your progress will be lost.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, close it',
        cancelButtonText: 'Continue filling'
    }).then((result) => {
        if (result.isConfirmed) {
            // Reset data and close
            currentStep = 1;
            healthQuestData = {};
            Swal.close();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Continue filling
            showHealthQuestStep(currentStep);
        }
    });
}

function showMedicalHistory() {
    // Use basic education questionnaire for ALL students
    let departmentName = 'Not Assigned';
    
    if (window.studentData && window.studentData.department_level) {
        const dept = window.studentData.department_level;
        if (dept === 'College') {
            departmentName = 'College';
        } else if (dept === 'SHS') {
            departmentName = 'Senior High School';
        } else if (dept === 'JHS') {
            departmentName = 'Junior High School';
        } else if (dept === 'Grade School') {
            departmentName = 'Grade School';
        }
    }
    
    console.log('Using basic education questionnaire for all students. Department:', departmentName);
    
    // Show confirmation - all students use the same questionnaire
    Swal.fire({
        title: 'Health Questionnaire',
        html: `
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 64px; margin-bottom: 20px;">📋</div>
                <h3 style="margin: 0 0 10px 0; color: #333;">
                    Health Assessment Form
                </h3>
                <p style="margin: 0 0 20px 0; color: #666; font-size: 16px;">
                    Department: <strong>${departmentName}</strong>
                </p>
                <p style="margin: 0; color: #888; font-size: 14px;">
                    Click "Start" to begin your health questionnaire
                </p>
            </div>
        `,
        confirmButtonText: 'Start',
        allowOutsideClick: false,
        width: '500px',
        confirmButtonColor: '#4285f4'
    }).then((result) => {
        if (result.isConfirmed) {
            // Always use basic education questionnaire for all students
            educationLevel = 'basic';
            showHealthQuestStep(1);
        }
    });
}

function selectEducationLevel(level, clickedElement) {
    // Visual feedback
    document.querySelectorAll('.education-card').forEach(card => {
        card.style.border = '2px solid #e0e0e0';
    });
    clickedElement.style.border = '2px solid #4285f4';
    
    educationLevel = level;
    healthQuestData.educationLevel = level;
    
    // Start questionnaire after short delay
    setTimeout(() => {
        Swal.close();
        // Reset form data
        currentStep = 1;
        healthQuestData = { educationLevel: level };
        
        // Show the health quest form starting from step 1
        showHealthQuestStep(1);
    }, 500);
}

function showHealthQuestStep(step) {
    currentStep = step;
    
    // Different step titles based on education level
    const stepTitles = educationLevel === 'college' ? [
        'Personal Info & Vital Signs',
        'Health History & Lifestyle', 
        'Medical History & Conditions',
        'Mental Health & Wellness',
        'Current Health Concerns',
        'Review & Submit'
    ] : [
        'Personal Info & Vital Signs', 
        'Health History General',
        'Hospitalization History',
        'Immunization & Health Info',
        'Current Health Concerns',
        'Review & Submit'
    ];
    
    // Create progress indicator
    let progressHTML = '<div style="margin-bottom: 30px;">';
    progressHTML += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">';
    progressHTML += '<h3 style="text-align: center; margin: 0; color: #333; flex: 1;">Your health quest begins here</h3>';
    progressHTML += '<button onclick="closeHealthQuest()" style="background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold;" title="Close Health Quest">&times;</button>';
    progressHTML += '</div>';
    progressHTML += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">';
    
    for (let i = 1; i <= 6; i++) {
        const isActive = i === step;
        const isCompleted = i < step;
        const circleColor = isActive ? '#4285f4' : (isCompleted ? '#34a853' : '#e0e0e0');
        const textColor = isActive ? '#4285f4' : (isCompleted ? '#34a853' : '#666');
        
        progressHTML += `
            <div style="text-align: center; flex: 1;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: ${circleColor}; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: bold;">
                    ${isCompleted ? '✓' : i}
                </div>
                <div style="font-size: 12px; color: ${textColor}; font-weight: ${isActive ? 'bold' : 'normal'};">
                    ${stepTitles[i-1]}
                </div>
            </div>
        `;
        
        if (i < 6) {
            progressHTML += `<div style="flex: 0.5; height: 2px; background: ${i < step ? '#34a853' : '#e0e0e0'}; margin: 0 10px;"></div>`;
        }
    }
    
    progressHTML += '</div></div>';
    
    // Get step content
    const stepContent = getStepContent(step);
    
    Swal.fire({
        title: '',
        html: progressHTML + stepContent,
        showCancelButton: step > 1,
        confirmButtonText: step === 6 ? 'Submit' : 'Next',
        cancelButtonText: 'Previous',
        confirmButtonColor: '#4285f4',
        width: '700px',
        allowOutsideClick: false,
        didOpen: () => {
            // Auto-populate and restore form values
            setTimeout(() => {
                console.log('didOpen callback - step:', step);
                
                // Auto-populate student data from database (step 1 only)
                if (step === 1) {
                    console.log('Calling populateStudentData for step 1');
                    populateStudentData();
                }
                
                // Restore form values from saved data
                restoreFormValues(step);
                
                // Call updateCervicalCancerVisibility when step 4 is shown (for basic education)
                if (step === 4 && educationLevel === 'basic') {
                    updateCervicalCancerVisibility();
                }
                
                // Also call calculateAge if birthday field exists
                if (document.getElementById('studentBirthday')) {
                    calculateAge();
                }
                
                // Load saved data into form fields
                setTimeout(() => {
                    loadSavedDataIntoFields();
                }, 50);
                
                // Add event listener for birthday input changes
                const birthdayInput = document.getElementById('studentBirthday');
                if (birthdayInput) {
                    console.log('Adding event listeners to birthday input');
                    // Remove existing listeners first to avoid duplicates
                    birthdayInput.removeEventListener('input', calculateAge);
                    birthdayInput.removeEventListener('change', calculateAge);
                    // Add new listeners
                    birthdayInput.addEventListener('input', calculateAge);
                    birthdayInput.addEventListener('change', calculateAge);
                    
                    // Test if the field already has a value
                    if (birthdayInput.value) {
                        console.log('Birthday input already has value:', birthdayInput.value);
                        calculateAge();
                    }
                } else {
                    console.log('Birthday input not found when trying to add event listeners');
                }
            }, 100);
        },
        preConfirm: () => {
            return validateAndSaveStep(step);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (step === 6) {
                // Submit form
                submitHealthQuest();
            } else {
                // Go to next step
                showHealthQuestStep(step + 1);
            }
        } else if (result.dismiss === Swal.DismissReason.cancel && step > 1) {
            // Go to previous step
            showHealthQuestStep(step - 1);
        }
    });
}

function getStepContent(step) {
    // Return different content based on education level
    if (educationLevel === 'college') {
        return getCollegeStepContent(step);
    } else {
        return getBasicStepContent(step);
    }
}

function getBasicStepContent(step) {
    switch(step) {
        case 1:
            return `
                <div class="text-left p-5">
                    <h4 class="mb-5 text-gray-800 text-xl font-semibold">Personal Information & Vital Signs</h4>
                    
                    <!-- Personal Information Section -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h5 class="mb-4 text-gray-600 font-semibold">Personal Information</h5>
                        
                        <!-- Responsive Name Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="studentFname" class="block mb-1 font-semibold text-gray-600 text-sm">First Name:</label>
                                <input type="text" id="studentFname" readonly disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                            <div>
                                <label for="studentMname" class="block mb-1 font-semibold text-gray-600 text-sm">Middle Name:</label>
                                <input type="text" id="studentMname" readonly disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                            <div>
                                <label for="studentLname" class="block mb-1 font-semibold text-gray-600 text-sm">Last Name:</label>
                                <input type="text" id="studentLname" readonly disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                        </div>
                        
                        <!-- Responsive Academic Info -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label for="studentSex" class="block mb-1 font-semibold text-gray-600 text-sm">Sex:</label>
                                <select id="studentSex" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="updateCervicalCancerVisibility()">
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label for="studentDepartment" class="block mb-1 font-semibold text-gray-600 text-sm">Department:</label>
                                <input type="text" id="studentDepartment" readonly disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                            <div>
                                <label for="studentProgram" class="block mb-1 font-semibold text-gray-600 text-sm">Program:</label>
                                <input type="text" id="studentProgram" readonly disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                            <div>
                                <label for="studentSection" class="block mb-1 font-semibold text-gray-600 text-sm">Section:</label>
                                <input type="text" id="studentSection" readonly disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                        </div>
                        
                        <!-- Responsive Contact & Birthday -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="contactNumber" class="block mb-1 font-semibold text-gray-600 text-sm">Contact Number:</label>
                                <input type="tel" id="contactNumber" placeholder="e.g., 09123456789" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="studentBirthday" class="block mb-1 font-semibold text-gray-600 text-sm">Birthday:</label>
                                <input type="date" id="studentBirthday" onchange="
                                    const ageInput = document.getElementById('studentAge');
                                    if (!this.value) {
                                        ageInput.value = '';
                                        return;
                                    }
                                    const birthday = new Date(this.value);
                                    const today = new Date();
                                    let age = today.getFullYear() - birthday.getFullYear();
                                    const monthDiff = today.getMonth() - birthday.getMonth();
                                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
                                        age--;
                                    }
                                    if (age >= 0 && age <= 150) {
                                        ageInput.value = age;
                                        ageInput.style.backgroundColor = 'lightgreen';
                                        setTimeout(() => ageInput.style.backgroundColor = '', 1000);
                                    } else {
                                        ageInput.value = '';
                                    }
                                " class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="max-w-xs">
                                <label for="studentAge" class="block mb-1 font-semibold text-gray-600 text-sm">Age:</label>
                                <input type="number" id="studentAge" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                            </div>
                        </div>
                        
                        <div>
                            <label for="homeAddress" class="block mb-1 font-semibold text-gray-600 text-sm">Home Address:</label>
                            <textarea id="homeAddress" placeholder="Enter your complete home address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-y focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                    
                    <!-- Vital Signs Section -->
                    <div>
                        <h5 class="mb-4 text-gray-600 font-semibold">Vital Signs & Physical Data</h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <input type="number" id="height" placeholder="Height (cm)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <input type="number" id="weight" placeholder="Weight (kg)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <input type="text" id="bloodPressure" placeholder="Blood Pressure (e.g., 120/80)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <input type="number" id="heartRate" placeholder="Heart Rate (bpm)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <input type="number" id="respiratoryRate" placeholder="Respiratory Rate (breaths/min)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <input type="number" id="temperature" placeholder="Temperature (°C)" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            `;
        case 2:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Health History General</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        <!-- Health Conditions YES/NO Sections -->
                        ${generateYesNoSection('allergies', 'Allergies', 'Do you have any known allergies?', 'List all known allergies (medications, foods, environmental, etc.) and describe any reactions')}
                        
                        ${generateYesNoSection('medicines', 'Medicine Allergies', 'Are you allergic to any medicines?', 'List specific medicines you are allergic to and describe reactions')}
                        
                        ${generateYesNoSection('vaccines', 'Vaccine Allergies', 'Are you allergic to any vaccines?', 'List specific vaccines you are allergic to and describe reactions')}
                        
                        ${generateYesNoSection('foods', 'Food Allergies', 'Are you allergic to any foods?', 'List specific foods you are allergic to and describe reactions')}
                        
                        ${generateYesNoSection('other', 'Other Allergies', 'Do you have any other allergies?', 'List other allergies (environmental, chemical, materials, etc.) and describe reactions')}
                        
                        ${generateYesNoSection('asthma', 'Asthma', 'Do you have asthma?', 'Describe your asthma condition, triggers, medications, and frequency of attacks')}
                        
                        ${generateYesNoSection('healthproblem', 'Health Problems', 'Do you have any ongoing health problems?', 'Describe any chronic or ongoing health conditions you have')}
                        
                        ${generateYesNoSection('earinfection', 'Frequent Ear Infections', 'Do you have frequent ear infections?', 'Describe frequency, symptoms, and any treatments you have received')}
                        
                        ${generateYesNoSection('potty', 'Problems Going to Potty', 'Do you have problems going to the bathroom?', 'Describe any difficulties with urination or bowel movements')}
                        
                        ${generateYesNoSection('uti', 'Urinary Tract Infections', 'Do you have urinary tract infections?', 'Describe frequency, symptoms, and any treatments you have received')}
                        
                        ${generateYesNoSection('chickenpox', 'Chicken Pox', 'Have you had chicken pox?', 'Describe when you had it, symptoms, and any complications')}
                        
                        ${generateYesNoSection('dengue', 'Dengue', 'Have you had dengue fever?', 'Describe when you had it, symptoms, and any complications')}
                        
                        ${generateYesNoSection('anemia', 'Anemia', 'Do you have anemia?', 'Describe type of anemia, symptoms, and any treatments')}
                        
                        ${generateYesNoSection('gastritis', 'Acute Gastritis', 'Have you had acute gastritis?', 'Describe symptoms, triggers, and any treatments received')}
                        
                        ${generateYesNoSection('pneumonia', 'Pneumonia/Lung Problems', 'Have you had pneumonia or lung problems?', 'Describe the condition, symptoms, and any treatments received')}
                        
                        ${generateYesNoSection('obesity', 'Obesity', 'Do you have obesity or weight problems?', 'Describe your weight concerns and any treatments or diet plans')}
                        
                        ${generateYesNoSection('covid19', 'COVID-19', 'Have you had COVID-19?', 'Describe when you had it, symptoms, and any long-term effects')}
                        
                        ${generateYesNoSection('otherconditions', 'Other Health Conditions', 'Do you have any other health conditions?', 'Describe any other medical conditions not mentioned above')}
                        
                    </div>
                </div>
            `;
        case 3:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Hospitalization History</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        <!-- Hospitalization/Operation Section -->
                        <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border: 1px solid #b3d9ff; margin-top: 20px;">
                            <h5 style="margin-bottom: 15px; color: #333; font-weight: 600;">Hospitalization/Operation History</h5>
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #555;">Have you been hospitalized or had any operations?</label>
                                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="radio" name="hasHospitalization" value="YES" id="hospitalizationYes" style="margin-right: 8px;" onchange="toggleRemarks('hospitalization')">
                                        <span style="font-weight: 600; color: #d32f2f;">YES</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="radio" name="hasHospitalization" value="NO" id="hospitalizationNo" style="margin-right: 8px;" onchange="toggleRemarks('hospitalization')">
                                        <span style="font-weight: 600; color: #388e3c;">NO</span>
                                    </label>
                                </div>
                                <div id="hospitalizationRemarksSection" style="display: none;">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                        <div>
                                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Date of Hospitalization/Operation:</label>
                                            <input type="date" id="hospitalizationDate" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Name of Hospital:</label>
                                            <input type="text" id="hospitalName" placeholder="Enter hospital name" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                        </div>
                                    </div>
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Details of hospitalization/operation:</label>
                                    <textarea id="hospitalizationRemarks" placeholder="Describe the reason for hospitalization, type of operation, complications, and outcome" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        case 4:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Immunization & Health Information</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        <p style="color: #666; margin-bottom: 20px; font-style: italic;">Please check all vaccines you have received:</p>
                        
                        <!-- Pneumonia Vaccine -->
                        <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border: 1px solid #b3d9ff;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                <input type="checkbox" id="pneumoniaVaccine" style="margin-right: 12px; transform: scale(1.2);">
                                Pneumonia Vaccine
                            </label>
                        </div>
                        
                        <!-- Flu Vaccine -->
                        <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border: 1px solid #b3d9ff;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                <input type="checkbox" id="fluVaccine" style="margin-right: 12px; transform: scale(1.2);">
                                Flu Vaccine (Influenza)
                            </label>
                        </div>
                        
                        <!-- Measles Vaccine -->
                        <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border: 1px solid #b3d9ff;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                <input type="checkbox" id="measlesVaccine" style="margin-right: 12px; transform: scale(1.2);">
                                Measles Vaccine (MMR)
                            </label>
                        </div>
                        
                        <!-- Hepatitis B Vaccine -->
                        <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border: 1px solid #b3d9ff;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                <input type="checkbox" id="hepBVaccine" style="margin-right: 12px; transform: scale(1.2);">
                                Hepatitis B Vaccine
                            </label>
                        </div>
                        
                        <!-- Cervical Cancer Vaccine (Female Only) -->
                        <div id="cervicalCancerSection" style="background: #fff0f5; padding: 15px; border-radius: 8px; border: 1px solid #ffb6c1; display: none;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                <input type="checkbox" id="cervicalCancerVaccine" style="margin-right: 12px; transform: scale(1.2);">
                                Cervical Cancer Vaccine (HPV)
                            </label>
                            <p style="color: #666; font-size: 12px; margin: 8px 0 0 32px; font-style: italic;">For females only</p>
                        </div>
                        
                        <!-- COVID-19 Vaccines -->
                        <div style="background: #f0fff0; padding: 15px; border-radius: 8px; border: 1px solid #90ee90;">
                            <h5 style="margin-bottom: 15px; color: #333; font-weight: 600;">COVID-19 Vaccines</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                    <input type="checkbox" id="covid1stDose" style="margin-right: 8px; transform: scale(1.1);">
                                    1st Dose
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                    <input type="checkbox" id="covid2ndDose" style="margin-right: 8px; transform: scale(1.1);">
                                    2nd Dose
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333;">
                                    <input type="checkbox" id="covidBooster" style="margin-right: 8px; transform: scale(1.1);">
                                    Booster
                                </label>
                            </div>
                        </div>
                        
                        <!-- Other Vaccines -->
                        <div style="background: #fffacd; padding: 15px; border-radius: 8px; border: 1px solid #f0e68c;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #333; margin-bottom: 10px;">
                                <input type="checkbox" id="otherVaccines" style="margin-right: 12px; transform: scale(1.2);" onchange="toggleOtherVaccinesText()">
                                Other Vaccines
                            </label>
                            <div id="otherVaccinesSection" style="display: none; margin-top: 10px;">
                                <textarea id="otherVaccinesText" placeholder="Please specify other vaccines you have received" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                            </div>
                        </div>
                        
                        <!-- Menstruation Information (Female Only) -->
                        <div id="menstruationSection" style="background: #fff0f5; padding: 20px; border-radius: 8px; border: 1px solid #ffb6c1; display: none;">
                            <h5 style="margin-bottom: 20px; color: #333; font-weight: 600;">Menstruation Information</h5>
                            <p style="color: #666; font-size: 12px; margin-bottom: 15px; font-style: italic;">For females only - This information helps us provide better healthcare</p>
                            
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label for="menarcheAge" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Age of Menarche (First Period):</label>
                                    <input type="number" id="menarcheAge" placeholder="Age in years" min="8" max="18" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                </div>
                                <div>
                                    <label for="menstrualDays" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Number of Days per Cycle:</label>
                                    <input type="number" id="menstrualDays" placeholder="Days (e.g., 5)" min="1" max="10" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                </div>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <label for="padsConsumed" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Pads Consumed per Day (during menstruation):</label>
                                <select id="padsConsumed" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                    <option value="">Select number of pads</option>
                                    <option value="1-2">1-2 pads per day</option>
                                    <option value="3-4">3-4 pads per day</option>
                                    <option value="5-6">5-6 pads per day</option>
                                    <option value="7-8">7-8 pads per day</option>
                                    <option value="9+">9 or more pads per day</option>
                                </select>
                            </div>

                            <div>
                                <label for="menstrualProblems" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Other Problems Related to Menstruation:</label>
                                <textarea id="menstrualProblems" placeholder="Describe any problems such as severe cramps, irregular periods, heavy bleeding, mood changes, or other concerns" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        case 5:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Current Health Concerns</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                        <div>
                            <label for="presentConcerns" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Present Health Concerns or Symptoms:</label>
                            <textarea id="presentConcerns" placeholder="Describe any current health concerns, symptoms, or reasons for this visit (e.g., headaches, stomach pain, fatigue, stress, etc.)" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        
                        <div>
                            <label for="currentMedicationsVitamins" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Current Medications & Vitamins:</label>
                            <textarea id="currentMedicationsVitamins" placeholder="List all medications, vitamins, supplements, or herbal remedies you are currently taking (include dosage if known)" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        
                        <div>
                            <label for="additionalNotes" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Additional Notes:</label>
                            <textarea id="additionalNotes" placeholder="Any other information you think is important for your healthcare provider to know" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>
            `;
        case 6:
            return `
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 48px; margin-bottom: 20px;">✅</div>
                    <h3 style="color: #333; margin-bottom: 15px;">Review Your Information</h3>
                    <p style="color: #666; margin-bottom: 20px;">Please review all the information you've provided before submitting your health questionnaire.</p>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: left; margin-bottom: 20px;">
                        <h5 style="margin-bottom: 10px; color: #333; font-weight: 600;">Personal Information:</h5>
                        <p><strong>First Name:</strong> ${healthQuestData.studentFname || 'Not provided'}</p>
                        <p><strong>Middle Name:</strong> ${healthQuestData.studentMname || 'Not provided'}</p>
                        <p><strong>Last Name:</strong> ${healthQuestData.studentLname || 'Not provided'}</p>
                        <p><strong>Sex:</strong> ${healthQuestData.studentSex || 'Not provided'}</p>
                        <p><strong>Department:</strong> ${healthQuestData.studentDepartment || 'Not provided'}</p>
                        <p><strong>Program:</strong> ${healthQuestData.studentProgram || 'Not provided'}</p>
                        <p><strong>Section:</strong> ${healthQuestData.studentSection || 'Not provided'}</p>
                        <p><strong>Contact Number:</strong> ${healthQuestData.contactNumber || backupData.contactNumber || localStorage.getItem('healthQuest_contact') || 'DEBUG: ' + JSON.stringify(healthQuestData.contactNumber) || 'Not provided'}</p>
                        <p><strong>Birthday:</strong> ${healthQuestData.studentBirthday || backupData.birthday || localStorage.getItem('healthQuest_birthday') || 'DEBUG: ' + JSON.stringify(healthQuestData.studentBirthday) || 'Not provided'}</p>
                        <p><strong>Age:</strong> ${healthQuestData.studentAge || 'Not provided'} years old</p>
                        <p><strong>Home Address:</strong> ${healthQuestData.homeAddress || backupData.homeAddress || localStorage.getItem('healthQuest_address') || 'DEBUG: ' + JSON.stringify(healthQuestData.homeAddress) || 'Not provided'}</p>
                        
                        <script>
                            console.log('=== BASIC EDUCATION SUMMARY DEBUG ===');
                            console.log('Full healthQuestData:', healthQuestData);
                            console.log('contactNumber value:', healthQuestData.contactNumber);
                            console.log('studentBirthday value:', healthQuestData.studentBirthday);  
                            console.log('homeAddress value:', healthQuestData.homeAddress);
                            
                            // Auto-restore from backup if data is missing
                            let restored = false;
                            if (!healthQuestData.contactNumber && (backupData.contactNumber || localStorage.getItem('healthQuest_contact'))) {
                                healthQuestData.contactNumber = backupData.contactNumber || localStorage.getItem('healthQuest_contact');
                                console.log('🔄 Auto-restored contact:', healthQuestData.contactNumber);
                                restored = true;
                            }
                            if (!healthQuestData.studentBirthday && (backupData.birthday || localStorage.getItem('healthQuest_birthday'))) {
                                healthQuestData.studentBirthday = backupData.birthday || localStorage.getItem('healthQuest_birthday');
                                console.log('🔄 Auto-restored birthday:', healthQuestData.studentBirthday);
                                restored = true;
                            }
                            if (!healthQuestData.homeAddress && (backupData.homeAddress || localStorage.getItem('healthQuest_address'))) {
                                healthQuestData.homeAddress = backupData.homeAddress || localStorage.getItem('healthQuest_address');
                                console.log('🔄 Auto-restored address:', healthQuestData.homeAddress);
                                restored = true;
                            }
                            
                            // Also check and restore health data
                            const conditions = ['allergies', 'medicines', 'vaccines', 'foods'];
                            conditions.forEach(condition => {
                                const hasCondition = localStorage.getItem('healthQuest_has_' + condition);
                                if (hasCondition && !healthQuestData['has_' + condition]) {
                                    healthQuestData['has_' + condition] = hasCondition;
                                    healthQuestData[condition + '_remarks'] = localStorage.getItem('healthQuest_' + condition + '_remarks') || '';
                                    console.log('🔄 Auto-restored health condition:', condition, '=', hasCondition);
                                    restored = true;
                                }
                            });
                            
                            if (restored) {
                                console.log('🔄 Data restored, refreshing display...');
                                // Force refresh the summary display
                                setTimeout(() => {
                                    location.reload();
                                }, 100);
                            }
                            
                            console.log('=== END BASIC SUMMARY DEBUG ===');
                        </script>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Vital Signs:</h5>
                        <p><strong>Height:</strong> ${healthQuestData.height || 'Not provided'} cm</p>
                        <p><strong>Weight:</strong> ${healthQuestData.weight || 'Not provided'} kg</p>
                        <p><strong>Blood Pressure:</strong> ${healthQuestData.bloodPressure || 'Not provided'}</p>
                        <p><strong>Heart Rate:</strong> ${healthQuestData.heartRate || 'Not provided'} bpm</p>
                        <p><strong>Respiratory Rate:</strong> ${healthQuestData.respiratoryRate || 'Not provided'} breaths/min</p>
                        <p><strong>Temperature:</strong> ${healthQuestData.temperature || 'Not provided'} °C</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Current Health Concerns:</h5>
                        <p><strong>Present Concerns:</strong> ${healthQuestData.presentConcerns || 'None reported'}</p>
                        <p><strong>Current Medications & Vitamins:</strong> ${healthQuestData.currentMedicationsVitamins || 'None reported'}</p>
                        <p><strong>Additional Notes:</strong> ${healthQuestData.additionalNotes || 'None provided'}</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Allergies:</h5>
                        <p><strong>Has Allergies:</strong> <span style="color: ${healthQuestData.hasAllergies === 'YES' ? '#d32f2f' : '#388e3c'}; font-weight: bold;">${healthQuestData.hasAllergies || 'Not specified'}</span></p>
                        ${healthQuestData.hasAllergies === 'YES' && healthQuestData.allergiesRemarks ? `<p><strong>Allergy Details:</strong> ${healthQuestData.allergiesRemarks}</p>` : ''}
                        <p><strong>Medicine Allergies:</strong> ${healthQuestData.medicineAllergies || 'None reported'}</p>
                        <p><strong>Vaccine Allergies:</strong> ${healthQuestData.vaccineAllergies || 'None reported'}</p>
                        <p><strong>Food Allergies:</strong> ${healthQuestData.foodAllergies || 'None reported'}</p>
                        <p><strong>Other Allergies:</strong> ${healthQuestData.otherAllergies || 'None reported'}</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Health History:</h5>
                        <p><strong>Family History:</strong> ${healthQuestData.familyHistory || 'None reported'}</p>
                        <p><strong>Previous Hospitalizations:</strong> ${healthQuestData.previousHospitalizations || 'None reported'}</p>
                        <p><strong>Lifestyle Habits:</strong> ${healthQuestData.lifestyleHabits || 'None reported'}</p>
                    </div>
                    <p style="color: #666; font-size: 14px;">By submitting this form, you confirm that all information provided is accurate and complete.</p>
                </div>
            `;
        default:
            return '<p>Invalid step</p>';
    }
}

function getCollegeStepContent(step) {
    switch(step) {
        case 1:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Personal Information & Vital Signs</h4>
                    
                    <!-- Personal Information Section -->
                    <div style="margin-bottom: 25px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h5 style="margin-bottom: 15px; color: #555; font-weight: 600;">Personal Information</h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">First Name:</label>
                                <input type="text" id="studentFname" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Middle Name:</label>
                                <input type="text" id="studentMname" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Last Name:</label>
                                <input type="text" id="studentLname" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Sex:</label>
                                <select id="studentSex" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" onchange="updateCervicalCancerVisibility()">
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Department:</label>
                                <select id="studentDepartment" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;" onchange="loadProgramsForStudent()">
                                    <option value="">Select Department</option>
                                    <option value="College">College</option>
                                    <option value="SHS">Senior High School</option>
                                    <option value="JHS">Junior High School</option>
                                    <option value="Grade School">Grade School</option>
                                </select>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Program:</label>
                                <select id="studentProgram" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;" onchange="loadSectionsForStudent()">
                                    <option value="">Select Program</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Section:</label>
                                <select id="studentSection" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 200px; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Contact Number:</label>
                                <input type="tel" id="contactNumber" placeholder="e.g., 09123456789" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Birthday:</label>
                                <input type="date" id="studentBirthday" onchange="
                                    const ageInput = document.getElementById('studentAge');
                                    if (!this.value) {
                                        ageInput.value = '';
                                        return;
                                    }
                                    const birthday = new Date(this.value);
                                    const today = new Date();
                                    let age = today.getFullYear() - birthday.getFullYear();
                                    const monthDiff = today.getMonth() - birthday.getMonth();
                                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
                                        age--;
                                    }
                                    if (age >= 0 && age <= 150) {
                                        ageInput.value = age;
                                        ageInput.style.backgroundColor = 'lightgreen';
                                        setTimeout(() => ageInput.style.backgroundColor = '', 1000);
                                    } else {
                                        ageInput.value = '';
                                    }
                                " style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Age:</label>
                                <input type="number" id="studentAge" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;">
                            </div>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Home Address:</label>
                            <textarea id="homeAddress" placeholder="Enter your complete home address" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                    </div>
                    
                    <!-- Vital Signs Section -->
                    <div>
                        <h5 style="margin-bottom: 15px; color: #555; font-weight: 600;">Vital Signs & Physical Data</h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <input type="number" id="height" placeholder="Height (cm)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            <input type="number" id="weight" placeholder="Weight (kg)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            <input type="text" id="bloodPressure" placeholder="Blood Pressure (e.g., 120/80)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            <input type="number" id="heartRate" placeholder="Heart Rate (bpm)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            <input type="number" id="respiratoryRate" placeholder="Respiratory Rate (breaths/min)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                            <input type="number" id="temperature" placeholder="Temperature (°C)" step="0.1" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                    </div>
                </div>
            `;
        case 2:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Health History & Lifestyle</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Current Medications & Supplements:</label>
                            <textarea id="currentMedications" placeholder="List any medications, vitamins, or supplements you are currently taking" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Lifestyle & Habits:</label>
                            <textarea id="lifestyleHabits" placeholder="Smoking, alcohol consumption, exercise routine, diet preferences, sleep schedule" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Academic Stress & Workload:</label>
                            <textarea id="academicStress" placeholder="Describe your current academic stress level, study habits, and workload" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Current Health Concerns:</label>
                            <textarea id="currentSymptoms" placeholder="Any current symptoms, health concerns, or reasons for this visit" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>
            `;
        case 3:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Medical History & Conditions</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Allergies & Reactions:</label>
                            <textarea id="allergiesAll" placeholder="List all known allergies (medications, foods, environmental, etc.) and reactions" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Chronic Conditions:</label>
                            <textarea id="chronicConditions" placeholder="Any ongoing medical conditions (diabetes, asthma, hypertension, etc.)" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Family Medical History:</label>
                            <textarea id="familyHistory" placeholder="Significant medical conditions in immediate family members" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Previous Surgeries/Hospitalizations:</label>
                            <textarea id="previousHospitalizations" placeholder="Any previous surgeries, hospitalizations, or major medical procedures" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>
            `;
        case 4:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Mental Health & Wellness</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Mental Health History:</label>
                            <textarea id="mentalHealthHistory" placeholder="Any history of anxiety, depression, counseling, or mental health treatment" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Current Stress Levels:</label>
                            <textarea id="stressLevels" placeholder="Rate your current stress level and describe main sources of stress" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Support System:</label>
                            <textarea id="supportSystem" placeholder="Describe your support system (family, friends, counselors, etc.)" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Wellness Goals:</label>
                            <textarea id="wellnessGoals" placeholder="What are your health and wellness goals for this academic year?" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>
            `;
        case 5:
            return `
                <div style="text-align: left; padding: 20px;">
                    <h4 style="margin-bottom: 20px; color: #333;">Current Health Concerns</h4>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                        <div>
                            <label for="presentConcerns" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Present Health Concerns or Symptoms:</label>
                            <textarea id="presentConcerns" placeholder="Describe any current health concerns, symptoms, or reasons for this visit (e.g., headaches, stomach pain, fatigue, stress, etc.)" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        
                        <div>
                            <label for="currentMedicationsVitamins" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Current Medications & Vitamins:</label>
                            <textarea id="currentMedicationsVitamins" placeholder="List all medications, vitamins, supplements, or herbal remedies you are currently taking (include dosage if known)" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                        
                        <div>
                            <label for="additionalNotes" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Additional Notes:</label>
                            <textarea id="additionalNotes" placeholder="Any other information you think is important for your healthcare provider to know" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>
            `;
        case 6:
            return `
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 48px; margin-bottom: 20px;">✅</div>
                    <h3 style="color: #333; margin-bottom: 15px;">Review Your Information</h3>
                    <p style="color: #666; margin-bottom: 20px;">Please review all the information you've provided before submitting your health questionnaire.</p>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: left; margin-bottom: 20px;">
                        <h5 style="margin-bottom: 10px; color: #333; font-weight: 600;">Personal Information:</h5>
                        <p><strong>Education Level:</strong> College</p>
                        <p><strong>First Name:</strong> ${healthQuestData.studentFname || 'Not provided'}</p>
                        <p><strong>Middle Name:</strong> ${healthQuestData.studentMname || 'Not provided'}</p>
                        <p><strong>Last Name:</strong> ${healthQuestData.studentLname || 'Not provided'}</p>
                        <p><strong>Sex:</strong> ${healthQuestData.studentSex || 'Not provided'}</p>
                        <p><strong>Department:</strong> ${healthQuestData.studentDepartment || 'Not provided'}</p>
                        <p><strong>Program:</strong> ${healthQuestData.studentProgram || 'Not provided'}</p>
                        <p><strong>Section:</strong> ${healthQuestData.studentSection || 'Not provided'}</p>
                        <p><strong>Contact Number:</strong> ${healthQuestData.contactNumber || 'Not provided'}</p>
                        <p><strong>Birthday:</strong> ${healthQuestData.studentBirthday || 'Not provided'}</p>
                        <p><strong>Age:</strong> ${healthQuestData.studentAge || 'Not provided'} years old</p>
                        <p><strong>Home Address:</strong> ${healthQuestData.homeAddress || 'Not provided'}</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Vital Signs:</h5>
                        <p><strong>Height:</strong> ${healthQuestData.height || 'Not provided'} cm</p>
                        <p><strong>Weight:</strong> ${healthQuestData.weight || 'Not provided'} kg</p>
                        <p><strong>Blood Pressure:</strong> ${healthQuestData.bloodPressure || 'Not provided'}</p>
                        <p><strong>Heart Rate:</strong> ${healthQuestData.heartRate || 'Not provided'} bpm</p>
                        <p><strong>Respiratory Rate:</strong> ${healthQuestData.respiratoryRate || 'Not provided'} breaths/min</p>
                        <p><strong>Temperature:</strong> ${healthQuestData.temperature || 'Not provided'} °C</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Current Health Concerns:</h5>
                        <p><strong>Present Concerns:</strong> ${healthQuestData.presentConcerns || 'None reported'}</p>
                        <p><strong>Current Medications & Vitamins:</strong> ${healthQuestData.currentMedicationsVitamins || 'None reported'}</p>
                        <p><strong>Additional Notes:</strong> ${healthQuestData.additionalNotes || 'None provided'}</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Health & Lifestyle:</h5>
                        <p><strong>Previous Medications:</strong> ${healthQuestData.currentMedications || 'None reported'}</p>
                        <p><strong>Lifestyle Habits:</strong> ${healthQuestData.lifestyleHabits || 'None reported'}</p>
                        <p><strong>Academic Stress:</strong> ${healthQuestData.academicStress || 'None reported'}</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Medical History:</h5>
                        <p><strong>Allergies:</strong> ${healthQuestData.allergiesAll || 'None reported'}</p>
                        <p><strong>Chronic Conditions:</strong> ${healthQuestData.chronicConditions || 'None reported'}</p>
                        <p><strong>Family History:</strong> ${healthQuestData.familyHistory || 'None reported'}</p>
                        
                        <h5 style="margin: 15px 0 10px 0; color: #333; font-weight: 600;">Mental Health:</h5>
                        <p><strong>Mental Health History:</strong> ${healthQuestData.mentalHealthHistory || 'None reported'}</p>
                        <p><strong>Current Stress:</strong> ${healthQuestData.stressLevels || 'None reported'}</p>
                        <p><strong>Support System:</strong> ${healthQuestData.supportSystem || 'None reported'}</p>
                        <p><strong>Wellness Goals:</strong> ${healthQuestData.wellnessGoals || 'None reported'}</p>
                    </div>
                    <p style="color: #666; font-size: 14px;">By submitting this form, you confirm that all information provided is accurate and complete.</p>
                </div>
            `;
        default:
            return '<p>Invalid step</p>';
    }
}


function validateAndSaveStep(step) {
    // Always save vital signs and basic info data regardless of step (since they appear in step 1)
    const sexInput = document.getElementById('studentSex');
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
    
    // Save basic personal info for both database and display
    if (sexInput) {
        healthQuestData.student_sex = sexInput.value;
        healthQuestData.studentSex = sexInput.value;
        console.log('Saved sex:', sexInput.value);
    }
    if (contactInput) {
        // Only update if we have a value, don't overwrite with empty
        if (contactInput.value) {
            healthQuestData.contactNumber = contactInput.value;
            console.log('Updated contact number:', contactInput.value);
        } else if (healthQuestData.contactNumber) {
            console.log('Keeping existing contact number:', healthQuestData.contactNumber);
        }
    }
    if (birthdayInput) {
        // Only update if we have a value, don't overwrite with empty
        if (birthdayInput.value) {
            healthQuestData.student_birthday = birthdayInput.value;
            healthQuestData.studentBirthday = birthdayInput.value;
            console.log('Updated birthday:', birthdayInput.value);
        } else if (healthQuestData.studentBirthday) {
            console.log('Keeping existing birthday:', healthQuestData.studentBirthday);
        }
    }
    if (ageInput) {
        healthQuestData.student_age = ageInput.value;
        healthQuestData.studentAge = ageInput.value;
        console.log('Saved age:', ageInput.value);
    }
    if (homeAddressInput) {
        // Only update if we have a value, don't overwrite with empty
        const currentValue = homeAddressInput.value || homeAddressInput.textContent || '';
        if (currentValue) {
            healthQuestData.home_address = currentValue;
            healthQuestData.homeAddress = currentValue;
            console.log('Updated home address:', currentValue);
        } else if (healthQuestData.homeAddress) {
            console.log('Keeping existing home address:', healthQuestData.homeAddress);
        }
        console.log('Home address input type:', homeAddressInput.tagName);
    }
    
    if (heightInput) {
        healthQuestData.height = heightInput.value;
        console.log('Saved height:', heightInput.value);
    }
    if (weightInput) {
        healthQuestData.weight = weightInput.value;
        console.log('Saved weight:', weightInput.value);
    }
    if (bloodPressureInput) {
        healthQuestData.bloodPressure = bloodPressureInput.value;
        console.log('Saved blood pressure:', bloodPressureInput.value);
    }
    if (heartRateInput) {
        healthQuestData.heartRate = heartRateInput.value;
        console.log('Saved heart rate:', heartRateInput.value);
    }
    if (respiratoryRateInput) {
        healthQuestData.respiratoryRate = respiratoryRateInput.value;
        console.log('Saved respiratory rate:', respiratoryRateInput.value);
    }
    if (temperatureInput) {
        healthQuestData.temperature = temperatureInput.value;
        console.log('Saved temperature:', temperatureInput.value);
    }
    
    // Common Step 1 for both education levels
    if (step === 1) {
        // Personal Information - map to correct database field names
        healthQuestData.student_sex = document.getElementById('studentSex')?.value;
        healthQuestData.student_birthday = document.getElementById('studentBirthday')?.value;
        healthQuestData.student_age = document.getElementById('studentAge')?.value;
        healthQuestData.home_address = document.getElementById('homeAddress')?.value;
        
        // Also save for display purposes (camelCase)
        healthQuestData.studentSex = document.getElementById('studentSex')?.value;
        healthQuestData.studentBirthday = document.getElementById('studentBirthday')?.value;
        healthQuestData.studentAge = document.getElementById('studentAge')?.value;
        healthQuestData.homeAddress = document.getElementById('homeAddress')?.value;

        // Vital Signs
        healthQuestData.height = document.getElementById('height')?.value;
        healthQuestData.weight = document.getElementById('weight')?.value;
        healthQuestData.bloodPressure = document.getElementById('bloodPressure')?.value;
        healthQuestData.heartRate = document.getElementById('heartRate')?.value;
        healthQuestData.respiratoryRate = document.getElementById('respiratoryRate')?.value;
        healthQuestData.temperature = document.getElementById('temperature')?.value;

        // Keep display names for UI purposes (not saved to database)
        healthQuestData.studentFname = document.getElementById('studentFname')?.value;
        healthQuestData.studentMname = document.getElementById('studentMname')?.value;
        healthQuestData.studentLname = document.getElementById('studentLname')?.value;
        healthQuestData.studentDepartment = document.getElementById('studentDepartment')?.value;
        healthQuestData.studentProgram = document.getElementById('studentProgram')?.value;
        healthQuestData.studentSection = document.getElementById('studentSection')?.value;
        healthQuestData.contactNumber = document.getElementById('contactNumber')?.value;

        return true;
    }
    
    // Different data saving based on education level
    if (educationLevel === 'college') {
        return validateAndSaveCollegeStep(step);
    } else {
        return validateAndSaveBasicStep(step);
    }
}

function validateAndSaveBasicStep(step) {
    switch(step) {
        case 2:
            // Handle all YES/NO health conditions with correct database field names
            const conditions = ['allergies', 'medicines', 'vaccines', 'foods', 'other', 'asthma', 'healthproblem',
                              'earinfection', 'potty', 'uti', 'chickenpox', 'dengue', 'anemia', 'gastritis',
                              'pneumonia', 'obesity', 'covid19', 'otherconditions'];

            conditions.forEach(condition => {
                const yesRadio = document.getElementById(condition + 'Yes');
                const noRadio = document.getElementById(condition + 'No');
                if (yesRadio?.checked) {
                    healthQuestData['has_' + condition] = 'YES';
                    healthQuestData[condition + '_remarks'] = document.getElementById(condition + 'Remarks')?.value;
                    // Backup health data
                    localStorage.setItem('healthQuest_has_' + condition, 'YES');
                    localStorage.setItem('healthQuest_' + condition + '_remarks', healthQuestData[condition + '_remarks'] || '');
                    console.log('🔒 Backed up health condition:', condition, 'YES');
                } else if (noRadio?.checked) {
                    healthQuestData['has_' + condition] = 'NO';
                    healthQuestData[condition + '_remarks'] = '';
                    // Backup health data
                    localStorage.setItem('healthQuest_has_' + condition, 'NO');
                    localStorage.setItem('healthQuest_' + condition + '_remarks', '');
                    console.log('🔒 Backed up health condition:', condition, 'NO');
                }
            });

            break;
            
        case 3:
            // Handle hospitalization YES/NO with details
            const hospitalizationYes = document.getElementById('hospitalizationYes');
            const hospitalizationNo = document.getElementById('hospitalizationNo');
            if (hospitalizationYes?.checked) {
                healthQuestData.has_hospitalization = 'YES';
                healthQuestData.hospitalization_date = document.getElementById('hospitalizationDate')?.value;
                healthQuestData.hospital_name = document.getElementById('hospitalName')?.value;
                healthQuestData.hospitalization_remarks = document.getElementById('hospitalizationRemarks')?.value;
            } else if (hospitalizationNo?.checked) {
                healthQuestData.has_hospitalization = 'NO';
                healthQuestData.hospitalization_date = '';
                healthQuestData.hospital_name = '';
                healthQuestData.hospitalization_remarks = '';
            }
            break;

        case 4:
            // Handle immunization checkboxes
            healthQuestData.pneumonia_vaccine = document.getElementById('pneumoniaVaccine')?.checked || false;
            healthQuestData.flu_vaccine = document.getElementById('fluVaccine')?.checked || false;
            healthQuestData.measles_vaccine = document.getElementById('measlesVaccine')?.checked || false;
            healthQuestData.hep_b_vaccine = document.getElementById('hepBVaccine')?.checked || false;
            healthQuestData.cervical_cancer_vaccine = document.getElementById('cervicalCancerVaccine')?.checked || false;

            // COVID-19 vaccines
            healthQuestData.covid_1st_dose = document.getElementById('covid1stDose')?.checked || false;
            healthQuestData.covid_2nd_dose = document.getElementById('covid2ndDose')?.checked || false;
            healthQuestData.covid_booster = document.getElementById('covidBooster')?.checked || false;

            // Other vaccines
            healthQuestData.other_vaccines = document.getElementById('otherVaccines')?.checked || false;
            healthQuestData.other_vaccines_text = document.getElementById('otherVaccinesText')?.value || '';

            // Menstruation information (for females)
            healthQuestData.menarche_age = document.getElementById('menarcheAge')?.value || '';
            healthQuestData.menstrual_days = document.getElementById('menstrualDays')?.value || '';
            healthQuestData.pads_consumed = document.getElementById('padsConsumed')?.value || '';
            healthQuestData.menstrual_problems = document.getElementById('menstrualProblems')?.value || '';
            break;
            
        case 5:
            // Handle current health concerns
            healthQuestData.presentConcerns = document.getElementById('presentConcerns')?.value || '';
            healthQuestData.currentMedicationsVitamins = document.getElementById('currentMedicationsVitamins')?.value || '';
            healthQuestData.additionalNotes = document.getElementById('additionalNotes')?.value || '';
            break;
    }
    
    return true;
}

function validateAndSaveCollegeStep(step) {
    switch(step) {
        case 2:
            healthQuestData.currentMedications = document.getElementById('currentMedications')?.value;
            healthQuestData.lifestyleHabits = document.getElementById('lifestyleHabits')?.value;
            healthQuestData.academicStress = document.getElementById('academicStress')?.value;
            healthQuestData.currentSymptoms = document.getElementById('currentSymptoms')?.value;
            break;
            
        case 3:
            healthQuestData.allergiesAll = document.getElementById('allergiesAll')?.value;
            healthQuestData.chronicConditions = document.getElementById('chronicConditions')?.value;
            healthQuestData.familyHistory = document.getElementById('familyHistory')?.value;
            healthQuestData.previousHospitalizations = document.getElementById('previousHospitalizations')?.value;
            break;
            
        case 4:
            healthQuestData.mentalHealthHistory = document.getElementById('mentalHealthHistory')?.value;
            healthQuestData.stressLevels = document.getElementById('stressLevels')?.value;
            healthQuestData.supportSystem = document.getElementById('supportSystem')?.value;
            healthQuestData.wellnessGoals = document.getElementById('wellnessGoals')?.value;
            break;
            
        case 5:
            // Handle current health concerns
            healthQuestData.presentConcerns = document.getElementById('presentConcerns')?.value || '';
            healthQuestData.currentMedicationsVitamins = document.getElementById('currentMedicationsVitamins')?.value || '';
            healthQuestData.additionalNotes = document.getElementById('additionalNotes')?.value || '';
            break;
    }
    
    return true;
}

function submitHealthQuest() {
    console.log('=== SUBMITTING HEALTH QUESTIONNAIRE ===');
    console.log('healthQuestData before restore:', healthQuestData);
    
    // CRITICAL: Restore all data from backup before submission
    restoreAllHealthData();
    
    console.log('healthQuestData after restore:', healthQuestData);
    
    // Show loading state
    Swal.fire({
        title: 'Submitting...',
        text: 'Please wait while we save your health questionnaire.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Prepare data for submission - only include database fields
    const databaseFields = {
        education_level: educationLevel,
        // Personal info
        student_sex: healthQuestData.student_sex,
        student_birthday: healthQuestData.student_birthday,
        student_age: healthQuestData.student_age,
        home_address: healthQuestData.home_address,
        // Vital signs
        height: healthQuestData.height,
        weight: healthQuestData.weight,
        blood_pressure: healthQuestData.bloodPressure,
        heart_rate: healthQuestData.heartRate,
        respiratory_rate: healthQuestData.respiratoryRate,
        temperature: healthQuestData.temperature,
        // Health conditions
        has_allergies: healthQuestData.has_allergies,
        allergies_remarks: healthQuestData.allergies_remarks,
        has_medicines: healthQuestData.has_medicines,
        medicine_allergies: healthQuestData.medicine_allergies,
        has_vaccines: healthQuestData.has_vaccines,
        vaccine_allergies: healthQuestData.vaccine_allergies,
        has_foods: healthQuestData.has_foods,
        food_allergies: healthQuestData.food_allergies,
        has_other: healthQuestData.has_other,
        other_allergies: healthQuestData.other_allergies,
        has_asthma: healthQuestData.has_asthma,
        asthma_remarks: healthQuestData.asthma_remarks,
        has_healthproblem: healthQuestData.has_healthproblem,
        healthproblem_remarks: healthQuestData.healthproblem_remarks,
        has_earinfection: healthQuestData.has_earinfection,
        earinfection_remarks: healthQuestData.earinfection_remarks,
        has_potty: healthQuestData.has_potty,
        potty_remarks: healthQuestData.potty_remarks,
        has_uti: healthQuestData.has_uti,
        uti_remarks: healthQuestData.uti_remarks,
        has_chickenpox: healthQuestData.has_chickenpox,
        chickenpox_remarks: healthQuestData.chickenpox_remarks,
        has_dengue: healthQuestData.has_dengue,
        dengue_remarks: healthQuestData.dengue_remarks,
        has_anemia: healthQuestData.has_anemia,
        anemia_remarks: healthQuestData.anemia_remarks,
        has_gastritis: healthQuestData.has_gastritis,
        gastritis_remarks: healthQuestData.gastritis_remarks,
        has_pneumonia: healthQuestData.has_pneumonia,
        pneumonia_remarks: healthQuestData.pneumonia_remarks,
        has_obesity: healthQuestData.has_obesity,
        obesity_remarks: healthQuestData.obesity_remarks,
        has_covid19: healthQuestData.has_covid19,
        covid19_remarks: healthQuestData.covid19_remarks,
        has_otherconditions: healthQuestData.has_otherconditions,
        otherconditions_remarks: healthQuestData.otherconditions_remarks,
        // Hospitalization
        has_hospitalization: healthQuestData.has_hospitalization,
        hospitalization_date: healthQuestData.hospitalization_date,
        hospital_name: healthQuestData.hospital_name,
        hospitalization_remarks: healthQuestData.hospitalization_remarks,
        // Immunization (convert boolean to int for database)
        pneumonia_vaccine: healthQuestData.pneumonia_vaccine ? 1 : 0,
        flu_vaccine: healthQuestData.flu_vaccine ? 1 : 0,
        measles_vaccine: healthQuestData.measles_vaccine ? 1 : 0,
        hep_b_vaccine: healthQuestData.hep_b_vaccine ? 1 : 0,
        cervical_cancer_vaccine: healthQuestData.cervical_cancer_vaccine ? 1 : 0,
        covid_1st_dose: healthQuestData.covid_1st_dose ? 1 : 0,
        covid_2nd_dose: healthQuestData.covid_2nd_dose ? 1 : 0,
        covid_booster: healthQuestData.covid_booster ? 1 : 0,
        other_vaccines: healthQuestData.other_vaccines ? 1 : 0,
        other_vaccines_text: healthQuestData.other_vaccines_text,
        // Menstruation
        menarche_age: healthQuestData.menarche_age,
        menstrual_days: healthQuestData.menstrual_days,
        pads_consumed: healthQuestData.pads_consumed,
        menstrual_problems: healthQuestData.menstrual_problems,
        // Current health concerns
        present_concerns: healthQuestData.present_concerns,
        current_medications_vitamins: healthQuestData.current_medications_vitamins,
        additional_notes: healthQuestData.additional_notes
    };

    const submitData = {
        step: 3, // Final step
        data: databaseFields
    };

    console.log('=== DATABASE SUBMISSION DEBUG ===');
    console.log('Full databaseFields object:', databaseFields);
    console.log('Personal info being sent:', {
        student_sex: databaseFields.student_sex,
        student_birthday: databaseFields.student_birthday,
        student_age: databaseFields.student_age,
        home_address: databaseFields.home_address
    });
    console.log('Health conditions being sent:', {
        has_allergies: databaseFields.has_allergies,
        allergies_remarks: databaseFields.allergies_remarks,
        has_medicines: databaseFields.has_medicines,
        medicine_allergies: databaseFields.medicine_allergies
    });
    console.log('Menstruation data being sent:', {
        menarche_age: databaseFields.menarche_age,
        menstrual_days: databaseFields.menstrual_days,
        pads_consumed: databaseFields.pads_consumed,
        menstrual_problems: databaseFields.menstrual_problems
    });
    console.log('Submitting health questionnaire data:', submitData);
    console.log('=== END DATABASE SUBMISSION DEBUG ===');

    // Send data to API (using simple API for now to avoid JSON errors)
    fetch('health-simple-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(submitData)
    })
    .then(response => {
        console.log('API response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('API response data:', data);

        if (data.success) {
            // Success
            Swal.fire({
                title: 'Health Questionnaire Submitted!',
                html: `
                    <div style="text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 20px;">🎉</div>
                        <p>Your health questionnaire has been submitted successfully.</p>
                        <p><strong>Reference Number:</strong> HQ-${Date.now()}</p>
                        <p style="color: #666; font-size: 14px;">Your information has been recorded and will be reviewed by our medical staff.</p>
                        <p style="color: #666; font-size: 14px;">You can now proceed with your clinic visit.</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Continue to Dashboard'
            }).then(() => {
                // Reset form data
                currentStep = 1;
                healthQuestData = {};
                // Optionally reload the page or redirect
                window.location.reload();
            });
        } else {
            // Error
            console.error('Submission failed:', data.message);
            Swal.fire({
                title: 'Submission Failed',
                text: data.message || 'There was an error submitting your questionnaire. Please try again.',
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Try Again'
            });
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        Swal.fire({
            title: 'Connection Error',
            text: 'Unable to connect to the server. Please check your internet connection and try again.',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Try Again'
        });
    });
}

// Function to request medical certificate
function requestMedicalCertificate() {
    Swal.fire({
        title: 'Request Medical Certificate',
        html: `
            <form id="medCertForm" style="text-align: left;">
                <div style="margin-bottom: 15px;">
                    <label for="certPurpose" style="display: block; margin-bottom: 5px; font-weight: 600;">Purpose:</label>
                    <select id="certPurpose" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" required>
                        <option value="">Select Purpose</option>
                        <option value="school_requirements">School Requirements</option>
                        <option value="work_clearance">Work Clearance</option>
                        <option value="sports_participation">Sports Participation</option>
                        <option value="travel">Travel</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="certNotes" style="display: block; margin-bottom: 5px; font-weight: 600;">Additional Notes:</label>
                    <textarea id="certNotes" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;" placeholder="Any specific requirements or notes..."></textarea>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="contactNumber" style="display: block; margin-bottom: 5px; font-weight: 600;">Contact Number:</label>
                    <input type="tel" id="contactNumber" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" placeholder="Your contact number" required>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit Request',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        preConfirm: () => {
            const purpose = document.getElementById('certPurpose').value;
            const notes = document.getElementById('certNotes').value;
            const contact = document.getElementById('contactNumber').value;
            
            if (!purpose || !contact) {
                Swal.showValidationMessage('Please fill in all required fields');
                return false;
            }
            
            return { purpose, notes, contact };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Request Submitted!',
                html: `
                    <div style="text-align: center;">
                        <p>Your medical certificate request has been submitted successfully.</p>
                        <p><strong>Reference Number:</strong> MC-${Date.now()}</p>
                        <p style="color: #666; font-size: 14px;">You will be notified when your certificate is ready for pickup.</p>
                        <p style="color: #666; font-size: 14px;">Processing time: 1-2 business days</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonColor: '#28a745'
            });
        }
    });
}

function showAppointmentForm() {
    Swal.fire({
        title: 'Request Appointment',
        html: `
            <form id="appointmentForm" style="text-align: left;">
                <div style="margin-bottom: 15px;">
                    <label for="appointmentDate" style="display: block; margin-bottom: 5px; font-weight: 600;">Preferred Date:</label>
                    <input type="date" id="appointmentDate" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="appointmentTime" style="display: block; margin-bottom: 5px; font-weight: 600;">Preferred Time:</label>
                    <select id="appointmentTime" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" required>
                        <option value="">Select Time</option>
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="appointmentReason" style="display: block; margin-bottom: 5px; font-weight: 600;">Reason for Visit:</label>
                    <textarea id="appointmentReason" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;" placeholder="Please describe your concern..." required></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit Request',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        preConfirm: () => {
            const date = document.getElementById('appointmentDate').value;
            const time = document.getElementById('appointmentTime').value;
            const reason = document.getElementById('appointmentReason').value;
            
            if (!date || !time || !reason) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            
            return { date, time, reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Request Submitted!',
                text: 'Your appointment request has been submitted. You will be notified once it is confirmed.',
                icon: 'success',
                confirmButtonColor: '#28a745'
            });
        }
    });
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
        if (birthdayInput && healthQuestData.studentBirthday) {
            birthdayInput.value = healthQuestData.studentBirthday;
            // Recalculate age to ensure it's current
            calculateAge();
        }
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

// Toggle dropdown function
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const button = event.target.closest('.user-dropdown button');
    
    if (!button && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

// Set home as active by default and initialize
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('nav a[href="#home"]').classList.add('active');
    
    // Set minimum date to today for appointment booking
    const today = new Date().toISOString().split('T')[0];
    // This will be available when the appointment form is opened
});
