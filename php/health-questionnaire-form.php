<!-- Health Questionnaire Modal Form with Proper Label Associations -->
<div id="healthQuestionnaireModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="bg-blue-600 text-white p-6 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Health Questionnaire</h2>
                    <button onclick="closeHealthQuestionnaire()" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span id="stepIndicator">Step 1 of 3</span>
                        <span id="progressPercent">33%</span>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-2">
                        <div id="progressBar" class="bg-white h-2 rounded-full transition-all duration-300" style="width: 33%"></div>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="healthQuestionnaireForm">
                    <!-- Step 1: Personal Information -->
                    <div id="step1" class="step-content">
                        <h3 class="text-xl font-semibold mb-6 text-gray-800">Personal Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="student_id_display" class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                                <input type="text" id="student_id_display" name="student_id_display" readonly 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none">
                            </div>
                            
                            <div>
                                <label for="full_name_display" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" id="full_name_display" name="full_name_display" readonly 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="student_sex" class="block text-sm font-medium text-gray-700 mb-2">Sex <span class="text-red-500">*</span></label>
                                <select id="student_sex" name="student_sex" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="student_birthday" class="block text-sm font-medium text-gray-700 mb-2">Birthday <span class="text-red-500">*</span></label>
                                <input type="date" id="student_birthday" name="student_birthday" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="student_age" class="block text-sm font-medium text-gray-700 mb-2">Age <span class="text-red-500">*</span></label>
                                <input type="number" id="student_age" name="student_age" min="1" max="100" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="home_address" class="block text-sm font-medium text-gray-700 mb-2">Home Address</label>
                            <textarea id="home_address" name="home_address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Enter your complete home address"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="section_display" class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                                <input type="text" id="section_display" name="section_display" readonly 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none">
                            </div>
                            
                            <div>
                                <label for="program_display" class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                                <input type="text" id="program_display" name="program_display" readonly 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Vital Signs -->
                    <div id="step2" class="step-content hidden">
                        <h3 class="text-xl font-semibold mb-6 text-gray-800">Vital Signs & Physical Data</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                                <input type="number" id="height" name="height" step="0.1" min="50" max="300" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 170.5">
                            </div>
                            
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" step="0.1" min="20" max="500" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 65.0">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                                <input type="text" id="blood_pressure" name="blood_pressure" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 120/80">
                            </div>
                            
                            <div>
                                <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">Heart Rate (bpm)</label>
                                <input type="number" id="heart_rate" name="heart_rate" min="30" max="200" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 72">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-2">Respiratory Rate (breaths/min)</label>
                                <input type="number" id="respiratory_rate" name="respiratory_rate" min="5" max="50" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 16">
                            </div>
                            
                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                                <input type="number" id="temperature" name="temperature" step="0.1" min="30" max="45" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 36.5">
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Health History -->
                    <div id="step3" class="step-content hidden">
                        <h3 class="text-xl font-semibold mb-6 text-gray-800">Health History</h3>
                        
                        <!-- Allergies Section -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-lg font-medium mb-4 text-gray-700">Allergies</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_allergies" class="block text-sm font-medium text-gray-700 mb-2">Do you have any allergies?</label>
                                    <select id="has_allergies" name="has_allergies" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="allergies_remarks" class="block text-sm font-medium text-gray-700 mb-2">Allergy Details</label>
                                    <textarea id="allergies_remarks" name="allergies_remarks" rows="2" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify allergies if any"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Common Health Conditions -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-lg font-medium mb-4 text-gray-700">Common Health Conditions</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="has_asthma" class="block text-sm font-medium text-gray-700 mb-2">Asthma</label>
                                    <select id="has_asthma" name="has_asthma" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="has_diabetes" class="block text-sm font-medium text-gray-700 mb-2">Diabetes</label>
                                    <select id="has_diabetes" name="has_diabetes" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="has_hypertension" class="block text-sm font-medium text-gray-700 mb-2">Hypertension</label>
                                    <select id="has_hypertension" name="has_hypertension" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="has_heart_disease" class="block text-sm font-medium text-gray-700 mb-2">Heart Disease</label>
                                    <select id="has_heart_disease" name="has_heart_disease" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Current Health Concerns -->
                        <div class="mb-6">
                            <label for="present_concerns" class="block text-sm font-medium text-gray-700 mb-2">Current Health Concerns</label>
                            <textarea id="present_concerns" name="present_concerns" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Describe any current health concerns or symptoms"></textarea>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-6">
                            <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea id="additional_notes" name="additional_notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Any additional information you'd like to share"></textarea>
                        </div>
                    </div>
                </form>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                    <button id="prevBtn" onclick="previousStep()" 
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                            disabled>
                        Previous
                    </button>
                    
                    <div class="flex space-x-3">
                        <button id="saveBtn" onclick="saveCurrentStep()" 
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            Save Progress
                        </button>
                        
                        <button id="nextBtn" onclick="nextStep()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Next
                        </button>
                        
                        <button id="submitBtn" onclick="submitQuestionnaire()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors hidden">
                            Submit Questionnaire
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Health Questionnaire JavaScript - Using namespace to avoid conflicts
const HealthQuestionnaire = {
    currentStep: 1,
    totalSteps: 3,
    
    // Initialize the health questionnaire
    init: function() {
        this.loadStudentData();
        this.updateStepDisplay();
    }
};

// Initialize health questionnaire (called from dashboard)
function initializeHealthQuestionnaire() {
    HealthQuestionnaire.init();
}

// Close health questionnaire modal
function closeHealthQuestionnaire() {
    document.getElementById('healthQuestionnaireModal').classList.add('hidden');
    HealthQuestionnaire.currentStep = 1;
    HealthQuestionnaire.updateStepDisplay();
}

// Load student data from API
HealthQuestionnaire.loadStudentData = async function() {
    try {
        const response = await fetch('health-questionnaire-backend-api.php?action=get_record');
        const data = await response.json();
        
        if (data.success && data.data.step1) {
            const step1Data = data.data.step1;
            
            // Populate readonly fields
            document.getElementById('student_id_display').value = step1Data.studentId || '';
            document.getElementById('full_name_display').value = step1Data.fullName || '';
            document.getElementById('section_display').value = step1Data.sectionName || '';
            document.getElementById('program_display').value = step1Data.programName || '';
            
            // Populate editable fields if data exists
            if (data.exists) {
                document.getElementById('student_sex').value = step1Data.studentSex || '';
                document.getElementById('student_birthday').value = step1Data.studentBirthday || '';
                document.getElementById('student_age').value = step1Data.studentAge || '';
                document.getElementById('home_address').value = step1Data.homeAddress || '';
                
                // Step 2 data
                if (data.data.step2) {
                    const step2Data = data.data.step2;
                    document.getElementById('height').value = step2Data.height || '';
                    document.getElementById('weight').value = step2Data.weight || '';
                    document.getElementById('blood_pressure').value = step2Data.bloodPressure || '';
                    document.getElementById('heart_rate').value = step2Data.heartRate || '';
                    document.getElementById('respiratory_rate').value = step2Data.respiratoryRate || '';
                    document.getElementById('temperature').value = step2Data.temperature || '';
                }
                
                // Step 3 data
                if (data.data.step3) {
                    const step3Data = data.data.step3;
                    document.getElementById('has_allergies').value = step3Data.hasAllergies || 'NO';
                    document.getElementById('allergies_remarks').value = step3Data.allergiesRemarks || '';
                    document.getElementById('has_asthma').value = step3Data.hasAsthma || 'NO';
                    document.getElementById('present_concerns').value = step3Data.presentConcerns || '';
                    document.getElementById('additional_notes').value = step3Data.additionalNotes || '';
                }
            }
        }
    } catch (error) {
        console.error('Error loading student data:', error);
        alert('Error loading student information. Please try again.');
    }
};

// Update step display and navigation
HealthQuestionnaire.updateStepDisplay = function() {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.add('hidden');
    });
    
    // Show current step
    document.getElementById(`step${this.currentStep}`).classList.remove('hidden');
    
    // Update progress bar
    const progress = (this.currentStep / this.totalSteps) * 100;
    document.getElementById('progressBar').style.width = `${progress}%`;
    document.getElementById('stepIndicator').textContent = `Step ${this.currentStep} of ${this.totalSteps}`;
    document.getElementById('progressPercent').textContent = `${Math.round(progress)}%`;
    
    // Update navigation buttons
    document.getElementById('prevBtn').disabled = this.currentStep === 1;
    document.getElementById('nextBtn').classList.toggle('hidden', this.currentStep === this.totalSteps);
    document.getElementById('submitBtn').classList.toggle('hidden', this.currentStep !== this.totalSteps);
};

// Navigate to next step
function nextStep() {
    if (HealthQuestionnaire.currentStep < HealthQuestionnaire.totalSteps) {
        HealthQuestionnaire.currentStep++;
        HealthQuestionnaire.updateStepDisplay();
    }
}

// Navigate to previous step
function previousStep() {
    if (HealthQuestionnaire.currentStep > 1) {
        HealthQuestionnaire.currentStep--;
        HealthQuestionnaire.updateStepDisplay();
    }
}

// Save current step data
async function saveCurrentStep() {
    const formData = new FormData(document.getElementById('healthQuestionnaireForm'));
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('health-questionnaire-backend-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                step: HealthQuestionnaire.currentStep,
                data: data
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Progress saved successfully!');
        } else {
            alert('Error saving data: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error saving data:', error);
        alert('Error saving data. Please try again.');
    }
}

// Submit complete questionnaire
async function submitQuestionnaire() {
    const formData = new FormData(document.getElementById('healthQuestionnaireForm'));
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('health-questionnaire-backend-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                step: 3,
                data: data,
                isCompleted: true
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Health questionnaire submitted successfully!');
            closeHealthQuestionnaire();
        } else {
            alert('Error submitting questionnaire: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error submitting questionnaire:', error);
        alert('Error submitting questionnaire. Please try again.');
    }
}

// Auto-calculate age from birthday
document.getElementById('student_birthday').addEventListener('change', function() {
    const birthday = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birthday.getFullYear();
    const monthDiff = today.getMonth() - birthday.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
        age--;
    }
    
    if (age >= 0 && age <= 100) {
        document.getElementById('student_age').value = age;
    }
});
</script>
