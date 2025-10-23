<!-- Classic Medical Form Style Health Questionnaire -->
<div id="healthQuestionnaireModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Form Header -->
            <div class="bg-blue-600 text-white p-4 rounded-t-lg">
                <h2 class="text-xl font-bold text-center">STUDENT HEALTH QUESTIONNAIRE</h2>
            </div>

            <!-- Form Body -->
            <div class="p-6">
                <form id="classicHealthForm">
                    <!-- Department and Grade Level -->
                    <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
                        <div class="flex items-center">
                            <label class="font-semibold mr-2">Department/S.Y.:</label>
                            <input type="text" id="departmentSY" class="border-b border-black flex-1 px-1" readonly>
                        </div>
                        <div class="flex items-center">
                            <label class="font-semibold mr-2">Grade Level:</label>
                            <input type="text" id="gradeLevel" class="border-b border-black flex-1 px-1" readonly>
                        </div>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="newStudent" class="mr-1"> New Student
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="oldStudent" class="mr-1"> Old Student
                            </label>
                        </div>
                    </div>

                    <!-- Student Information Header -->
                    <div class="text-center font-bold text-sm mb-3 border-b border-black pb-1">
                        STUDENT INFORMATION
                    </div>

                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                        <div class="flex items-center">
                            <label class="font-semibold mr-2 w-16">NAME:</label>
                            <input type="text" id="studentName" class="border-b border-black flex-1 px-1" readonly>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center">
                                <label class="font-semibold mr-2">AGE:</label>
                                <input type="text" id="studentAge" class="border-b border-black w-12 px-1">
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="font-semibold">SEX:</label>
                                <label><input type="radio" name="sex" value="F" class="mr-1"> F</label>
                                <label><input type="radio" name="sex" value="M" class="mr-1"> M</label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                        <div class="flex items-center">
                            <label class="font-semibold mr-2">Birthday:</label>
                            <input type="date" id="studentBirthday" class="border border-black px-2 py-1">
                        </div>
                        <div class="flex items-center">
                            <label class="font-semibold mr-2">HOME ADDRESS:</label>
                            <input type="text" id="homeAddress" class="border-b border-black flex-1 px-1">
                        </div>
                    </div>

                    <!-- Vital Signs Table -->
                    <table class="w-full border-collapse border border-black mb-4 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-black p-2">Grade Level</th>
                                <th class="border border-black p-2">Height</th>
                                <th class="border border-black p-2">Weight</th>
                                <th class="border border-black p-2">BP</th>
                                <th class="border border-black p-2">HR</th>
                                <th class="border border-black p-2">RR</th>
                                <th class="border border-black p-2">Temp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="border border-black p-2 font-semibold">7</td>
                                <td class="border border-black p-2"><input type="text" id="height7" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="weight7" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="bp7" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="hr7" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="rr7" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="temp7" class="w-full"></td>
                            </tr>
                            <tr><td class="border border-black p-2 font-semibold">8</td>
                                <td class="border border-black p-2"><input type="text" id="height8" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="weight8" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="bp8" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="hr8" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="rr8" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="temp8" class="w-full"></td>
                            </tr>
                            <tr><td class="border border-black p-2 font-semibold">9</td>
                                <td class="border border-black p-2"><input type="text" id="height9" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="weight9" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="bp9" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="hr9" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="rr9" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="temp9" class="w-full"></td>
                            </tr>
                            <tr><td class="border border-black p-2 font-semibold">10</td>
                                <td class="border border-black p-2"><input type="text" id="height10" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="weight10" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="bp10" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="hr10" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="rr10" class="w-full"></td>
                                <td class="border border-black p-2"><input type="text" id="temp10" class="w-full"></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Health History Header -->
                    <div class="text-center font-bold text-sm mb-3 border-b border-black pb-1">
                        HEALTH HISTORY
                    </div>

                    <!-- Health History Table -->
                    <table class="w-full border-collapse border border-black mb-4 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-black p-2 w-1/3"></th>
                                <th class="border border-black p-2 w-16">YES</th>
                                <th class="border border-black p-2 w-16">NO</th>
                                <th class="border border-black p-2">REMARKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Allergies</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="allergies" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="allergies" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="allergiesRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Medicines</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="medicines" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="medicines" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="medicinesRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Vaccines</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="vaccines" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="vaccines" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="vaccinesRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Foods</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="foods" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="foods" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="foodsRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Other</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="other" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="other" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="otherRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Asthma</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="asthma" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="asthma" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="asthmaRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Heart Problem</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="heartProblem" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="heartProblem" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="heartProblemRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Frequent Ear Infection</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="earInfection" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="earInfection" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="earInfectionRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Problem going to Potty</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="pottyProblem" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="pottyProblem" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="pottyProblemRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Urinary Tract Infection</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="uti" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="uti" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="utiRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Chicken pox</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="chickenpox" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="chickenpox" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="chickenpoxRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Dengue</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="dengue" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="dengue" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="dengueRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Pneumonia</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="pneumonia" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="pneumonia" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="pneumoniaRemarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Covid-19</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="covid19" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="covid19" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="covid19Remarks" class="w-full">
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2 font-semibold">Other's</td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="others" value="YES">
                                </td>
                                <td class="border border-black p-2 text-center">
                                    <input type="radio" name="others" value="NO">
                                </td>
                                <td class="border border-black p-2">
                                    <input type="text" id="othersRemarks" class="w-full">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Form Buttons -->
                    <div class="flex justify-center gap-4 mt-6">
                        <button type="button" id="submitClassicForm" class="bg-blue-600 text-white px-6 py-2 rounded border border-black">
                            Submit
                        </button>
                        <button type="button" id="cancelClassicForm" class="bg-gray-300 text-black px-6 py-2 rounded border border-black">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Classic form functionality
function initializeClassicHealthForm() {
    // Load existing data if available
    loadClassicFormData();
    
    // Set up event listeners
    document.getElementById('submitClassicForm').addEventListener('click', submitClassicForm);
    document.getElementById('cancelClassicForm').addEventListener('click', closeClassicForm);
}

async function loadClassicFormData() {
    try {
        const response = await fetch('health-questionnaire-backend-api.php?action=get_record');
        const result = await response.json();
        
        if (result.success && result.exists && result.data) {
            const data = result.data;
            
            // Populate basic info
            document.getElementById('studentName').value = data.full_name || '';
            document.getElementById('studentAge').value = data.student_age || '';
            document.getElementById('studentBirthday').value = data.student_birthday || '';
            document.getElementById('homeAddress').value = data.home_address || '';
            document.getElementById('departmentSY').value = data.department_level || '';
            document.getElementById('gradeLevel').value = data.section_name || '';
            
            // Set sex radio buttons
            if (data.student_sex) {
                const sexRadio = document.querySelector(`input[name="sex"][value="${data.student_sex}"]`);
                if (sexRadio) sexRadio.checked = true;
            }
            
            // Populate health conditions
            const conditions = [
                'allergies', 'medicines', 'vaccines', 'foods', 'other', 'asthma',
                'heartProblem', 'earInfection', 'pottyProblem', 'uti', 'chickenpox',
                'dengue', 'pneumonia', 'covid19', 'others'
            ];
            
            conditions.forEach(condition => {
                const value = data[`has_${condition}`] || data[`has_${condition.toLowerCase()}`];
                if (value) {
                    const radio = document.querySelector(`input[name="${condition}"][value="${value}"]`);
                    if (radio) radio.checked = true;
                }
                
                const remarks = data[`${condition}_remarks`] || data[`${condition.toLowerCase()}_remarks`];
                const remarksField = document.getElementById(`${condition}Remarks`);
                if (remarksField && remarks) {
                    remarksField.value = remarks;
                }
            });
            
            // Populate vital signs (use current data for all grade levels)
            const vitals = ['height', 'weight', 'bp', 'hr', 'rr', 'temp'];
            const grades = ['7', '8', '9', '10'];
            
            grades.forEach(grade => {
                vitals.forEach(vital => {
                    const field = document.getElementById(`${vital}${grade}`);
                    if (field) {
                        let value = '';
                        switch(vital) {
                            case 'height': value = data.height || ''; break;
                            case 'weight': value = data.weight || ''; break;
                            case 'bp': value = data.blood_pressure || ''; break;
                            case 'hr': value = data.heart_rate || ''; break;
                            case 'rr': value = data.respiratory_rate || ''; break;
                            case 'temp': value = data.temperature || ''; break;
                        }
                        field.value = value;
                    }
                });
            });
        }
    } catch (error) {
        console.error('Error loading classic form data:', error);
    }
}

async function submitClassicForm() {
    try {
        const formData = collectClassicFormData();
        
        const response = await fetch('health-questionnaire-backend-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                step: 'classic',
                data: formData,
                isCompleted: true
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Health questionnaire submitted successfully.',
                icon: 'success'
            }).then(() => {
                closeClassicForm();
                location.reload();
            });
        } else {
            throw new Error(result.message || 'Submission failed');
        }
    } catch (error) {
        console.error('Error submitting classic form:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to submit health questionnaire. Please try again.',
            icon: 'error'
        });
    }
}

function collectClassicFormData() {
    const data = {};
    
    // Basic info
    data.student_age = document.getElementById('studentAge').value;
    data.student_birthday = document.getElementById('studentBirthday').value;
    data.home_address = document.getElementById('homeAddress').value;
    
    // Sex
    const sexRadio = document.querySelector('input[name="sex"]:checked');
    if (sexRadio) data.student_sex = sexRadio.value;
    
    // Vital signs (use grade 10 as current)
    data.height = document.getElementById('height10').value;
    data.weight = document.getElementById('weight10').value;
    data.blood_pressure = document.getElementById('bp10').value;
    data.heart_rate = document.getElementById('hr10').value;
    data.respiratory_rate = document.getElementById('rr10').value;
    data.temperature = document.getElementById('temp10').value;
    
    // Health conditions
    const conditions = [
        'allergies', 'medicines', 'vaccines', 'foods', 'other', 'asthma',
        'heartProblem', 'earInfection', 'pottyProblem', 'uti', 'chickenpox',
        'dengue', 'pneumonia', 'covid19', 'others'
    ];
    
    conditions.forEach(condition => {
        const radio = document.querySelector(`input[name="${condition}"]:checked`);
        if (radio) {
            data[`has_${condition.toLowerCase()}`] = radio.value;
        }
        
        const remarksField = document.getElementById(`${condition}Remarks`);
        if (remarksField && remarksField.value) {
            data[`${condition.toLowerCase()}_remarks`] = remarksField.value;
        }
    });
    
    return data;
}

function closeClassicForm() {
    document.getElementById('healthQuestionnaireModal').classList.add('hidden');
}

// Initialize when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    // Replace the existing health questionnaire initialization
    window.initializeHealthQuestionnaire = initializeClassicHealthForm;
    console.log('Classic health form loaded and ready');
});

// Also make it available globally
window.initializeClassicHealthForm = initializeClassicHealthForm;
</script>
