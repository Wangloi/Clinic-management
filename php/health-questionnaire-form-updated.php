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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_medicines" class="block text-sm font-medium text-gray-700 mb-2">Medicine allergies?</label>
                                    <select id="has_medicines" name="has_medicines"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="medicine_allergies" class="block text-sm font-medium text-gray-700 mb-2">Medicine Allergy Details</label>
                                    <textarea id="medicine_allergies" name="medicine_allergies" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify medicine allergies"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_vaccines" class="block text-sm font-medium text-gray-700 mb-2">Vaccine allergies?</label>
                                    <select id="has_vaccines" name="has_vaccines"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="vaccine_allergies" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Allergy Details</label>
                                    <textarea id="vaccine_allergies" name="vaccine_allergies" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify vaccine allergies"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_foods" class="block text-sm font-medium text-gray-700 mb-2">Food allergies?</label>
                                    <select id="has_foods" name="has_foods"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="food_allergies" class="block text-sm font-medium text-gray-700 mb-2">Food Allergy Details</label>
                                    <textarea id="food_allergies" name="food_allergies" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify food allergies"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="has_other" class="block text-sm font-medium text-gray-700 mb-2">Other allergies?</label>
                                    <select id="has_other" name="has_other"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="other_allergies" class="block text-sm font-medium text-gray-700 mb-2">Other Allergy Details</label>
                                    <textarea id="other_allergies" name="other_allergies" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify other allergies"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Health Conditions Section -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-lg font-medium mb-4 text-gray-700">Health Conditions</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_asthma" class="block text-sm font-medium text-gray-700 mb-2">Asthma</label>
                                    <select id="has_asthma" name="has_asthma"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="asthma_remarks" class="block text-sm font-medium text-gray-700 mb-2">Asthma Details</label>
                                    <textarea id="asthma_remarks" name="asthma_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about asthma"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_healthproblem" class="block text-sm font-medium text-gray-700 mb-2">Other Health Problems</label>
                                    <select id="has_healthproblem" name="has_healthproblem"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="healthproblem_remarks" class="block text-sm font-medium text-gray-700 mb-2">Health Problem Details</label>
                                    <textarea id="healthproblem_remarks" name="healthproblem_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify other health problems"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_earinfection" class="block text-sm font-medium text-gray-700 mb-2">Ear Infection</label>
                                    <select id="has_earinfection" name="has_earinfection"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="earinfection_remarks" class="block text-sm font-medium text-gray-700 mb-2">Ear Infection Details</label>
                                    <textarea id="earinfection_remarks" name="earinfection_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about ear infection"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_potty" class="block text-sm font-medium text-gray-700 mb-2">Potty Training Issues</label>
                                    <select id="has_potty" name="has_potty"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="potty_remarks" class="block text-sm font-medium text-gray-700 mb-2">Potty Training Details</label>
                                    <textarea id="potty_remarks" name="potty_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about potty training issues"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_uti" class="block text-sm font-medium text-gray-700 mb-2">Urinary Tract Infection (UTI)</label>
                                    <select id="has_uti" name="has_uti"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="uti_remarks" class="block text-sm font-medium text-gray-700 mb-2">UTI Details</label>
                                    <textarea id="uti_remarks" name="uti_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about UTI"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_chickenpox" class="block text-sm font-medium text-gray-700 mb-2">Chickenpox</label>
                                    <select id="has_chickenpox" name="has_chickenpox"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="chickenpox_remarks" class="block text-sm font-medium text-gray-700 mb-2">Chickenpox Details</label>
                                    <textarea id="chickenpox_remarks" name="chickenpox_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about chickenpox"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_dengue" class="block text-sm font-medium text-gray-700 mb-2">Dengue</label>
                                    <select id="has_dengue" name="has_dengue"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="dengue_remarks" class="block text-sm font-medium text-gray-700 mb-2">Dengue Details</label>
                                    <textarea id="dengue_remarks" name="dengue_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about dengue"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_anemia" class="block text-sm font-medium text-gray-700 mb-2">Anemia</label>
                                    <select id="has_anemia" name="has_anemia"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="anemia_remarks" class="block text-sm font-medium text-gray-700 mb-2">Anemia Details</label>
                                    <textarea id="anemia_remarks" name="anemia_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about anemia"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_gastritis" class="block text-sm font-medium text-gray-700 mb-2">Gastritis</label>
                                    <select id="has_gastritis" name="has_gastritis"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="gastritis_remarks" class="block text-sm font-medium text-gray-700 mb-2">Gastritis Details</label>
                                    <textarea id="gastritis_remarks" name="gastritis_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about gastritis"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_pneumonia" class="block text-sm font-medium text-gray-700 mb-2">Pneumonia</label>
                                    <select id="has_pneumonia" name="has_pneumonia"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="pneumonia_remarks" class="block text-sm font-medium text-gray-700 mb-2">Pneumonia Details</label>
                                    <textarea id="pneumonia_remarks" name="pneumonia_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about pneumonia"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_obesity" class="block text-sm font-medium text-gray-700 mb-2">Obesity</label>
                                    <select id="has_obesity" name="has_obesity"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="obesity_remarks" class="block text-sm font-medium text-gray-700 mb-2">Obesity Details</label>
                                    <textarea id="obesity_remarks" name="obesity_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about obesity"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_covid19" class="block text-sm font-medium text-gray-700 mb-2">COVID-19</label>
                                    <select id="has_covid19" name="has_covid19"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="covid19_remarks" class="block text-sm font-medium text-gray-700 mb-2">COVID-19 Details</label>
                                    <textarea id="covid19_remarks" name="covid19_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about COVID-19"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="has_otherconditions" class="block text-sm font-medium text-gray-700 mb-2">Other Conditions</label>
                                    <select id="has_otherconditions" name="has_otherconditions"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="otherconditions_remarks" class="block text-sm font-medium text-gray-700 mb-2">Other Conditions Details</label>
                                    <textarea id="otherconditions_remarks" name="otherconditions_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify other conditions"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Hospitalization Section -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-lg font-medium mb-4 text-gray-700">Hospitalization History</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="has_hospitalization" class="block text-sm font-medium text-gray-700 mb-2">Have you been hospitalized?</label>
                                    <select id="has_hospitalization" name="has_hospitalization"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="NO">No</option>
                                        <option value="YES">Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="hospitalization_date" class="block text-sm font-medium text-gray-700 mb-2">Hospitalization Date</label>
                                    <input type="date" id="hospitalization_date" name="hospitalization_date"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="hospital_name" class="block text-sm font-medium text-gray-700 mb-2">Hospital Name</label>
                                    <input type="text" id="hospital_name" name="hospital_name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Name of the hospital">
                                </div>

                                <div>
                                    <label for="hospitalization_remarks" class="block text-sm font-medium text-gray-700 mb-2">Hospitalization Details</label>
                                    <textarea id="hospitalization_remarks" name="hospitalization_remarks" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Details about hospitalization"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Immunization Section -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-lg font-medium mb-4 text-gray-700">Immunization</h4>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="pneumonia_vaccine" name="pneumonia_vaccine" value="1"
                                           class="mr-2">
                                    <label for="pneumonia_vaccine" class="text-sm font-medium text-gray-700">Pneumonia</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="flu_vaccine" name="flu_vaccine" value="1"
                                           class="mr-2">
                                    <label for="flu_vaccine" class="text-sm font-medium text-gray-700">Flu</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="measles_vaccine" name="measles_vaccine" value="1"
                                           class="mr-2">
                                    <label for="measles_vaccine" class="text-sm font-medium text-gray-700">Measles</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="hep_b_vaccine" name="hep_b_vaccine" value="1"
                                           class="mr-2">
                                    <label for="hep_b_vaccine" class="text-sm font-medium text-gray-700">Hepatitis B</label>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="cervical_cancer_vaccine" name="cervical_cancer_vaccine" value="1"
                                           class="mr-2">
                                    <label for="cervical_cancer_vaccine" class="text-sm font-medium text-gray-700">Cervical Cancer</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="covid_1st_dose" name="covid_1st_dose" value="1"
                                           class="mr-2">
                                    <label for="covid_1st_dose" class="text-sm font-medium text-gray-700">COVID-19 1st Dose</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="covid_2nd_dose" name="covid_2nd_dose" value="1"
                                           class="mr-2">
                                    <label for="covid_2nd_dose" class="text-sm font-medium text-gray-700">COVID-19 2nd Dose</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="covid_booster" name="covid_booster" value="1"
                                           class="mr-2">
                                    <label for="covid_booster" class="text-sm font-medium text-gray-700">COVID-19 Booster</label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="other_vaccines" name="other_vaccines" value="1"
                                           class="mr-2">
                                    <label for="other_vaccines" class="text-sm font-medium text-gray-700">Other Vaccines</label>
                                </div>

                                <div>
                                    <label for="other_vaccines_text" class="block text-sm font-medium text-gray-700 mb-2">Other Vaccines Details</label>
                                    <textarea id="other_vaccines_text" name="other_vaccines_text" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Specify other vaccines"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Menstruation Section (Female only) -->
                        <div id="menstruation_section" class="mb-6 p-4 bg-pink-50 rounded-lg hidden">
                            <h4 class="text-lg font-medium mb-4 text-gray-700">Menstruation (Female Students)</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="menarche_age" class="block text-sm font-medium text-gray-700 mb-2">Age of Menarche</label>
                                    <input type="number" id="menarche_age" name="menarche_age" min="8" max="18"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Age when menstruation started">
                                </div>

                                <div>
                                    <label for="menstrual_days" class="block text-sm font-medium text-gray-700 mb-2">Days of Menstruation</label>
                                    <input type="number" id="menstrual_days" name="menstrual_days" min="1" max="10"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Number of days">
                                </div>
                            </div>

                            <div class="grid grid-cols-1
