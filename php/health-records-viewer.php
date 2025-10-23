
<!-- Health Records Viewer Modal -->
<div id="healthRecordsViewerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="bg-green-600 text-white p-6 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">My Health Records - Review</h2>
                    <button onclick="closeHealthRecordsViewer()" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div id="healthRecordsContent">
                    <!-- Content will be loaded here -->
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                    <button onclick="closeHealthRecordsViewer()" 
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                    <div class="space-x-4">
                        <button onclick="printHealthRecords()" 
                                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            📄 Print Records
                        </button>
                        <button onclick="editHealthRecords()" 
                                class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            ✏️ Edit Records
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Health Records Viewer Functions
function showHealthRecordsViewer(healthData) {
    const modal = document.getElementById('healthRecordsViewerModal');
    const content = document.getElementById('healthRecordsContent');
    
    if (!healthData || !healthData.data) {
        content.innerHTML = '<p class="text-red-500">No health records found.</p>';
        modal.classList.remove('hidden');
        return;
    }
    
    const data = healthData.data;
    
    content.innerHTML = generateHealthRecordsHTML(data);
    modal.classList.remove('hidden');
}

function generateHealthRecordsHTML(data) {
    return `
        <!-- Personal Information Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-green-500 pb-2">
                📋 Personal Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Student ID</label>
                    <p class="text-lg font-semibold text-gray-800">${data.student_id || 'N/A'}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Full Name</label>
                    <p class="text-lg font-semibold text-gray-800">${data.full_name || 'N/A'}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Sex</label>
                    <p class="text-lg font-semibold text-gray-800">${data.student_sex || 'N/A'}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Age</label>
                    <p class="text-lg font-semibold text-gray-800">${data.student_age || 'N/A'}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Birthday</label>
                    <p class="text-lg font-semibold text-gray-800">${formatDate(data.student_birthday) || 'N/A'}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Contact Number</label>
                    <p class="text-lg font-semibold text-gray-800">${data.contact_number || 'N/A'}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Home Address</label>
                    <p class="text-lg font-semibold text-gray-800">${data.home_address || 'N/A'}</p>
                </div>
            </div>
        </div>

        <!-- Academic Information Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-blue-500 pb-2">
                🎓 Academic Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Section</label>
                    <p class="text-lg font-semibold text-gray-800">${data.section_name || 'N/A'}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Program</label>
                    <p class="text-lg font-semibold text-gray-800">${data.program_name || 'N/A'}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Department</label>
                    <p class="text-lg font-semibold text-gray-800">${getDepartmentDisplayName(data.department_level) || 'N/A'}</p>
                </div>
            </div>
        </div>

        <!-- Vital Signs Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-red-500 pb-2">
                ❤️ Vital Signs & Physical Data
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-red-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Height</label>
                    <p class="text-lg font-semibold text-gray-800">${data.height ? data.height + ' cm' : 'N/A'}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Weight</label>
                    <p class="text-lg font-semibold text-gray-800">${data.weight ? data.weight + ' kg' : 'N/A'}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">BMI</label>
                    <p class="text-lg font-semibold text-gray-800">${calculateBMI(data.height, data.weight)}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Blood Pressure</label>
                    <p class="text-lg font-semibold text-gray-800">${data.blood_pressure || 'N/A'}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Heart Rate</label>
                    <p class="text-lg font-semibold text-gray-800">${data.heart_rate ? data.heart_rate + ' bpm' : 'N/A'}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Respiratory Rate</label>
                    <p class="text-lg font-semibold text-gray-800">${data.respiratory_rate ? data.respiratory_rate + ' breaths/min' : 'N/A'}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600">Temperature</label>
                    <p class="text-lg font-semibold text-gray-800">${data.temperature ? data.temperature + ' °C' : 'N/A'}</p>
                </div>
            </div>
        </div>

        <!-- Health Conditions Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-yellow-500 pb-2">
                🏥 Health Conditions & Medical History
            </h3>
            ${generateHealthConditionsHTML(data)}
        </div>

        <!-- Vaccination Records Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-purple-500 pb-2">
                💉 Vaccination Records
            </h3>
            ${generateVaccinationHTML(data)}
        </div>

        <!-- Allergy Information Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-orange-500 pb-2">
                🚨 Allergy Information
            </h3>
            ${generateAllergyHTML(data)}
        </div>

        <!-- Hospitalization History Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-pink-500 pb-2">
                🏥 Hospitalization History
            </h3>
            ${generateHospitalizationHTML(data)}
        </div>

        <!-- Menstruation Information Section (for females) -->
        ${data.student_sex === 'Female' ? `
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-rose-500 pb-2">
                🌸 Menstruation Information
            </h3>
            ${generateMenstruationHTML(data)}
        </div>
        ` : ''}

        <!-- Current Health Status Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b-2 border-indigo-500 pb-2">
                🩺 Current Health Status
            </h3>
            <div class="space-y-4">
                ${data.present_concerns ? `
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Present Health Concerns</label>
                    <p class="text-gray-800">${data.present_concerns}</p>
                </div>
                ` : ''}
                ${data.current_medications_vitamins ? `
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Current Medications/Vitamins</label>
                    <p class="text-gray-800">${data.current_medications_vitamins}</p>
                </div>
                ` : ''}
                ${data.additional_notes ? `
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Additional Notes</label>
                    <p class="text-gray-800">${data.additional_notes}</p>
                </div>
                ` : ''}
            </div>
        </div>

        <!-- Record Information -->
        <div class="bg-gray-100 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-700 mb-2">Record Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <p><strong>Submitted:</strong> ${formatDateTime(data.submission_date)}</p>
                <p><strong>Last Updated:</strong> ${formatDateTime(data.updated_at)}</p>
                <p><strong>Status:</strong> ${data.is_completed ? 'Completed' : 'In Progress'}</p>
                <p><strong>Education Level:</strong> ${data.education_level || 'Basic'}</p>
            </div>
        </div>
    `;
}

function generateHealthConditionsHTML(data) {
    const conditions = [
        { key: 'has_allergies', label: 'Allergies', remarks: 'allergies_remarks' },
        { key: 'has_asthma', label: 'Asthma', remarks: 'asthma_remarks' },
        { key: 'has_healthproblem', label: 'Health Problems', remarks: 'healthproblem_remarks' },
        { key: 'has_earinfection', label: 'Ear Infections', remarks: 'earinfection_remarks' },
        { key: 'has_potty', label: 'Potty Problems', remarks: 'potty_remarks' },
        { key: 'has_uti', label: 'UTI', remarks: 'uti_remarks' },
        { key: 'has_chickenpox', label: 'Chickenpox', remarks: 'chickenpox_remarks' },
        { key: 'has_dengue', label: 'Dengue', remarks: 'dengue_remarks' },
        { key: 'has_anemia', label: 'Anemia', remarks: 'anemia_remarks' },
        { key: 'has_gastritis', label: 'Gastritis', remarks: 'gastritis_remarks' },
        { key: 'has_pneumonia', label: 'Pneumonia', remarks: 'pneumonia_remarks' },
        { key: 'has_obesity', label: 'Obesity', remarks: 'obesity_remarks' },
        { key: 'has_covid19', label: 'COVID-19', remarks: 'covid19_remarks' },
        { key: 'has_otherconditions', label: 'Other Conditions', remarks: 'otherconditions_remarks' }
    ];

    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    
    conditions.forEach(condition => {
        const hasCondition = data[condition.key] === 'YES';
        const remarks = data[condition.remarks];
        
        html += `
            <div class="bg-yellow-50 p-4 rounded-lg border-l-4 ${hasCondition ? 'border-red-500' : 'border-green-500'}">
                <div class="flex items-center justify-between mb-2">
                    <label class="font-medium text-gray-700">${condition.label}</label>
                    <span class="px-2 py-1 rounded text-sm font-semibold ${hasCondition ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                        ${hasCondition ? 'YES' : 'NO'}
                    </span>
                </div>
                ${hasCondition && remarks ? `<p class="text-sm text-gray-600 mt-2">${remarks}</p>` : ''}
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

function generateVaccinationHTML(data) {
    const vaccines = [
        { key: 'pneumonia_vaccine', label: 'Pneumonia Vaccine' },
        { key: 'flu_vaccine', label: 'Flu Vaccine' },
        { key: 'measles_vaccine', label: 'Measles Vaccine' },
        { key: 'hep_b_vaccine', label: 'Hepatitis B Vaccine' },
        { key: 'cervical_cancer_vaccine', label: 'Cervical Cancer Vaccine' },
        { key: 'covid_1st_dose', label: 'COVID-19 1st Dose' },
        { key: 'covid_2nd_dose', label: 'COVID-19 2nd Dose' },
        { key: 'covid_booster', label: 'COVID-19 Booster' }
    ];

    let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
    
    vaccines.forEach(vaccine => {
        const isVaccinated = data[vaccine.key] == 1;
        
        html += `
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 ${isVaccinated ? 'border-green-500' : 'border-gray-300'}">
                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700">${vaccine.label}</label>
                    <span class="px-2 py-1 rounded text-sm font-semibold ${isVaccinated ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'}">
                        ${isVaccinated ? '✓ Yes' : '✗ No'}
                    </span>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

function generateAllergyHTML(data) {
    const allergies = [
        { key: 'has_medicines', label: 'Medicine Allergies', remarks: 'medicine_allergies' },
        { key: 'has_vaccines', label: 'Vaccine Allergies', remarks: 'vaccine_allergies' },
        { key: 'has_foods', label: 'Food Allergies', remarks: 'food_allergies' },
        { key: 'has_other', label: 'Other Allergies', remarks: 'other_allergies' }
    ];

    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    
    allergies.forEach(allergy => {
        const hasAllergy = data[allergy.key] === 'YES';
        const remarks = data[allergy.remarks];
        
        html += `
            <div class="bg-orange-50 p-4 rounded-lg border-l-4 ${hasAllergy ? 'border-red-500' : 'border-green-500'}">
                <div class="flex items-center justify-between mb-2">
                    <label class="font-medium text-gray-700">${allergy.label}</label>
                    <span class="px-2 py-1 rounded text-sm font-semibold ${hasAllergy ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                        ${hasAllergy ? 'YES' : 'NO'}
                    </span>
                </div>
                ${hasAllergy && remarks ? `<p class="text-sm text-gray-600 mt-2"><strong>Details:</strong> ${remarks}</p>` : ''}
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

function generateHospitalizationHTML(data) {
    const hasHospitalization = data.has_hospitalization === 'YES';
    
    if (!hasHospitalization) {
        return `
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <div class="flex items-center">
                    <span class="text-green-600 mr-2">✓</span>
                    <span class="font-medium text-gray-700">No previous hospitalizations reported</span>
                </div>
            </div>
        `;
    }
    
    return `
        <div class="bg-pink-50 p-4 rounded-lg border-l-4 border-red-500">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Date of Hospitalization</label>
                    <p class="text-lg font-semibold text-gray-800">${formatDate(data.hospitalization_date) || 'Not specified'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Hospital Name</label>
                    <p class="text-lg font-semibold text-gray-800">${data.hospital_name || 'Not specified'}</p>
                </div>
            </div>
            ${data.hospitalization_remarks ? `
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600 mb-2">Reason/Details</label>
                <p class="text-gray-800">${data.hospitalization_remarks}</p>
            </div>
            ` : ''}
        </div>
    `;
}

function generateMenstruationHTML(data) {
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-rose-50 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600">Age of Menarche</label>
                <p class="text-lg font-semibold text-gray-800">${data.menarche_age ? data.menarche_age + ' years old' : 'N/A'}</p>
            </div>
            <div class="bg-rose-50 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600">Menstrual Days</label>
                <p class="text-lg font-semibold text-gray-800">${data.menstrual_days ? data.menstrual_days + ' days' : 'N/A'}</p>
            </div>
            <div class="bg-rose-50 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600">Pads Consumed</label>
                <p class="text-lg font-semibold text-gray-800">${data.pads_consumed || 'N/A'}</p>
            </div>
            <div class="bg-rose-50 p-4 rounded-lg col-span-1 md:col-span-2 lg:col-span-1">
                <label class="block text-sm font-medium text-gray-600">Problems</label>
                <p class="text-lg font-semibold text-gray-800">${data.menstrual_problems ? 'Yes' : 'None reported'}</p>
            </div>
        </div>
        ${data.menstrual_problems ? `
        <div class="mt-4 bg-rose-50 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-600 mb-2">Menstrual Problems Details</label>
            <p class="text-gray-800">${data.menstrual_problems}</p>
        </div>
        ` : ''}
    `;
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return null;
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function calculateBMI(height, weight) {
    if (!height || !weight) return 'N/A';
    const heightInMeters = height / 100;
    const bmi = (weight / (heightInMeters * heightInMeters)).toFixed(1);
    
    let category = '';
    if (bmi < 18.5) category = '(Underweight)';
    else if (bmi < 25) category = '(Normal)';
    else if (bmi < 30) category = '(Overweight)';
    else category = '(Obese)';
    
    return `${bmi} ${category}`;
}

function closeHealthRecordsViewer() {
    document.getElementById('healthRecordsViewerModal').classList.add('hidden');
}

function printHealthRecords() {
    const content = document.getElementById('healthRecordsContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Health Records</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .grid { display: grid; gap: 10px; }
                .bg-gray-50, .bg-red-50, .bg-blue-50, .bg-yellow-50, .bg-purple-50, .bg-indigo-50 { 
                    background-color: #f9f9f9; 
                    padding: 10px; 
                    border-radius: 5px; 
                    border: 1px solid #ddd;
                }
                .border-b-2 { border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 15px; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <h1>Student Health Records</h1>
            ${content}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function editHealthRecords() {
    closeHealthRecordsViewer();
    // Open the health questionnaire form in edit mode
    const modal = document.getElementById('healthQuestionnaireModal');
    if (modal) {
        modal.classList.remove('hidden');
        if (typeof initializeHealthQuestionnaire === 'function') {
            initializeHealthQuestionnaire();
        }
    }
}

// Helper function to get department display name (matches admin-students.php)
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
</script>
