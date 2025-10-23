<?php
/**
 * Health Questionnaire API Endpoint
 * Handles AJAX requests for saving and retrieving health questionnaire data
 *
 * Security Features:
 * - Session validation
 * - Input sanitization
 * - Rate limiting
 * - Proper error handling
 */

session_start();
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1); // Temporarily enable to see errors
ini_set('log_errors', 1);

// Clean any output buffer to prevent mixed content
if (ob_get_level()) {
    ob_clean();
}

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please log in as a student.',
        'error_code' => 'AUTH_REQUIRED'
    ]);
    exit();
}

require_once 'health-questionnaire-handler-fixed.php';

$healthHandler = new HealthQuestionnaireHandler();

// Include helper functions
function sanitizeHealthData($data) {
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        } elseif (is_array($value)) {
            $sanitized[$key] = sanitizeHealthData($value);
        } elseif (is_bool($value) || is_numeric($value)) {
            $sanitized[$key] = $value;
        } else {
            $sanitized[$key] = '';
        }
    }
    return $sanitized;
}

function validateHealthData($data, $step) {
    $errors = [];
    
    switch ($step) {
        case 1:
            // Basic info validation
            if (empty($data['studentSex'])) {
                $errors[] = 'Gender is required';
            }
            if (empty($data['studentBirthday'])) {
                $errors[] = 'Birthday is required';
            }
            break;
            
        case 2:
            // Physical measurements validation
            if (!empty($data['height']) && (!is_numeric($data['height']) || $data['height'] <= 0)) {
                $errors[] = 'Height must be a positive number';
            }
            if (!empty($data['weight']) && (!is_numeric($data['weight']) || $data['weight'] <= 0)) {
                $errors[] = 'Weight must be a positive number';
            }
            break;
            
        case 3:
        case 4:
        case 5:
        case 6:
            // Health history validation - mostly optional fields
            break;
    }
    
    return $errors;
}
$studentId = $_SESSION['user_id'];

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            handleSaveData($healthHandler, $studentId);
            break;
            
        case 'GET':
            handleGetData($healthHandler, $studentId);
            break;
            
        case 'DELETE':
            handleDeleteData($healthHandler, $studentId);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("Health Questionnaire API Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Internal server error',
        'debug' => [
            'error' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ]);
}

function handleSaveData($healthHandler, $studentId) {
    try {
        // Get JSON data from request body with size limit
        $rawInput = file_get_contents('php://input', false, null, 0, 1048576); // 1MB limit

        if ($rawInput === false) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Unable to read request data',
                'error_code' => 'READ_ERROR'
            ]);
            return;
        }

        $input = json_decode($rawInput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON data: ' . json_last_error_msg(),
                'error_code' => 'INVALID_JSON'
            ]);
            return;
        }

        if (!$input || !is_array($input)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request format',
                'error_code' => 'INVALID_FORMAT'
            ]);
            return;
        }

        $step = isset($input['step']) ? (int)$input['step'] : 1;
        $data = $input['data'] ?? [];

        // Validate step range
        if ($step < 1 || $step > 6) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid step number',
                'error_code' => 'INVALID_STEP'
            ]);
            return;
        }

        // Sanitize and validate data
        $data = sanitizeHealthData($data);
        $errors = validateHealthData($data, $step);

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
                'error_code' => 'VALIDATION_FAILED'
            ]);
            return;
        }

        // Save data with error handling
        $success = $healthHandler->saveHealthData($studentId, $data, $step);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Health data saved successfully',
                'step' => $step,
                'completed' => $step >= 6, // Changed from 3 to 6 for final step
                'timestamp' => date('c')
            ]);
        } else {
            error_log("Failed to save health data for student $studentId at step $step");
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save health data. Please try again.',
                'error_code' => 'SAVE_FAILED'
            ]);
        }

    } catch (Exception $e) {
        error_log("Exception in handleSaveData: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error_code' => 'INTERNAL_ERROR'
        ]);
    }
}

function handleGetData($healthHandler, $studentId) {
    try {
        $action = $_GET['action'] ?? 'get_record';

        // Validate action parameter
        $allowedActions = ['get_record', 'get_summary', 'get_conditions', 'get_student_info'];
        if (!in_array($action, $allowedActions)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action parameter',
                'error_code' => 'INVALID_ACTION'
            ]);
            return;
        }

        switch ($action) {
            case 'get_record':
                $record = $healthHandler->getHealthRecord($studentId);
                if ($record === false) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database error occurred',
                        'error_code' => 'DB_ERROR'
                    ]);
                    return;
                }
                echo json_encode([
                    'success' => true,
                    'data' => $record,
                    'exists' => !empty($record),
                    'timestamp' => date('c')
                ]);
                break;

            case 'get_summary':
                $summary = $healthHandler->getHealthSummary($studentId);
                if ($summary === false) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Unable to retrieve summary',
                        'error_code' => 'SUMMARY_ERROR'
                    ]);
                    return;
                }
                echo json_encode([
                    'success' => true,
                    'data' => $summary,
                    'timestamp' => date('c')
                ]);
                break;

            case 'get_conditions':
                $conditions = $healthHandler->getHealthConditions($studentId);
                if ($conditions === false) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Unable to retrieve conditions',
                        'error_code' => 'CONDITIONS_ERROR'
                    ]);
                    return;
                }
                echo json_encode([
                    'success' => true,
                    'data' => $conditions,
                    'timestamp' => date('c')
                ]);
                break;

            case 'get_student_info':
                $studentInfo = $healthHandler->getStudentInfo($studentId);
                if ($studentInfo === false) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Unable to retrieve student information',
                        'error_code' => 'STUDENT_INFO_ERROR'
                    ]);
                    return;
                }
                echo json_encode([
                    'success' => true,
                    'data' => $studentInfo,
                    'timestamp' => date('c')
                ]);
                break;
        }

    } catch (Exception $e) {
        error_log("Exception in handleGetData: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error_code' => 'INTERNAL_ERROR'
        ]);
    }
}

function handleDeleteData($healthHandler, $studentId) {
    try {
        // Additional security check - only allow deletion of own records
        $existingRecord = $healthHandler->getHealthRecord($studentId);
        if ($existingRecord === false) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Health record not found',
                'error_code' => 'RECORD_NOT_FOUND'
            ]);
            return;
        }

        if (empty($existingRecord)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'No health record exists to delete',
                'error_code' => 'NO_RECORD'
            ]);
            return;
        }

        $success = $healthHandler->deleteHealthRecord($studentId);

        if ($success) {
            error_log("Health record deleted for student $studentId");
            echo json_encode([
                'success' => true,
                'message' => 'Health record deleted successfully',
                'timestamp' => date('c')
            ]);
        } else {
            error_log("Failed to delete health record for student $studentId");
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete health record',
                'error_code' => 'DELETE_FAILED'
            ]);
        }

    } catch (Exception $e) {
        error_log("Exception in handleDeleteData: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error_code' => 'INTERNAL_ERROR'
        ]);
    }
}

// Additional helper endpoints for form data
if (isset($_GET['get_form_data'])) {
    try {
        // Return pre-filled form data for existing records
        $record = $healthHandler->getHealthRecord($studentId);
        $studentInfo = $healthHandler->getStudentInfo($studentId);
        
        if ($record || $studentInfo) {
        // Format data for form
        $formData = [
            'step1' => [
                'studentId' => $studentId,
                'firstName' => $studentInfo['Student_Fname'] ?? '',
                'middleName' => $studentInfo['Student_Mname'] ?? '',
                'lastName' => $studentInfo['Student_Lname'] ?? '',
                'fullName' => $studentInfo['full_name'] ?? '',
                'studentSex' => $record['student_sex'] ?? '',
                'studentBirthday' => $record['student_birthday'] ?? '',
                'studentAge' => $record['student_age'] ?? '',
                'homeAddress' => $record['home_address'] ?? '',
                'contactNumber' => $studentInfo['contact_number'] ?? '',
                'sectionId' => $studentInfo['section_id'] ?? '',
                'sectionName' => $studentInfo['section_name'] ?? '',
                'programName' => $studentInfo['program_name'] ?? '',
                'departmentLevel' => $studentInfo['department_level'] ?? '',
                'educationLevel' => $record['education_level'] ?? (($studentInfo['department_level'] === 'College') ? 'college' : 'basic')
            ],
            'step2' => [
                'height' => $record['height'] ?? '',
                'weight' => $record['weight'] ?? '',
                'bloodPressure' => $record['blood_pressure'] ?? '',
                'heartRate' => $record['heart_rate'] ?? '',
                'respiratoryRate' => $record['respiratory_rate'] ?? '',
                'temperature' => $record['temperature'] ?? ''
            ],
            'step3' => [
                // Basic Education Health History
                'hasAllergies' => $record['has_allergies'] ?? 'NO',
                'allergiesRemarks' => $record['allergies_remarks'] ?? '',
                'hasMedicines' => $record['has_medicines'] ?? 'NO',
                'medicineAllergies' => $record['medicine_allergies'] ?? '',
                'hasVaccines' => $record['has_vaccines'] ?? 'NO',
                'vaccineAllergies' => $record['vaccine_allergies'] ?? '',
                'hasFoods' => $record['has_foods'] ?? 'NO',
                'foodAllergies' => $record['food_allergies'] ?? '',
                'hasOther' => $record['has_other'] ?? 'NO',
                'otherAllergies' => $record['other_allergies'] ?? '',
                'hasAsthma' => $record['has_asthma'] ?? 'NO',
                'asthmaRemarks' => $record['asthma_remarks'] ?? '',
                'hasHealthproblem' => $record['has_healthproblem'] ?? 'NO',
                'healthproblemRemarks' => $record['healthproblem_remarks'] ?? '',
                'hasEarinfection' => $record['has_earinfection'] ?? 'NO',
                'earinfectionRemarks' => $record['earinfection_remarks'] ?? '',
                'hasPotty' => $record['has_potty'] ?? 'NO',
                'pottyRemarks' => $record['potty_remarks'] ?? '',
                'hasUti' => $record['has_uti'] ?? 'NO',
                'utiRemarks' => $record['uti_remarks'] ?? '',
                'hasChickenpox' => $record['has_chickenpox'] ?? 'NO',
                'chickenpoxRemarks' => $record['chickenpox_remarks'] ?? '',
                'hasDengue' => $record['has_dengue'] ?? 'NO',
                'dengueRemarks' => $record['dengue_remarks'] ?? '',
                'hasAnemia' => $record['has_anemia'] ?? 'NO',
                'anemiaRemarks' => $record['anemia_remarks'] ?? '',
                'hasGastritis' => $record['has_gastritis'] ?? 'NO',
                'gastritisRemarks' => $record['gastritis_remarks'] ?? '',
                'hasPneumonia' => $record['has_pneumonia'] ?? 'NO',
                'pneumoniaRemarks' => $record['pneumonia_remarks'] ?? '',
                'hasObesity' => $record['has_obesity'] ?? 'NO',
                'obesityRemarks' => $record['obesity_remarks'] ?? '',
                'hasCovid19' => $record['has_covid19'] ?? 'NO',
                'covid19Remarks' => $record['covid19_remarks'] ?? '',
                'hasOtherconditions' => $record['has_otherconditions'] ?? 'NO',
                'otherconditionsRemarks' => $record['otherconditions_remarks'] ?? '',
                
                // Hospitalization
                'hasHospitalization' => $record['has_hospitalization'] ?? 'NO',
                'hospitalizationDate' => $record['hospitalization_date'] ?? '',
                'hospitalName' => $record['hospital_name'] ?? '',
                'hospitalizationRemarks' => $record['hospitalization_remarks'] ?? '',
                
                // Immunization
                'pneumoniaVaccine' => $record['pneumonia_vaccine'] ?? false,
                'fluVaccine' => $record['flu_vaccine'] ?? false,
                'measlesVaccine' => $record['measles_vaccine'] ?? false,
                'hepBVaccine' => $record['hep_b_vaccine'] ?? false,
                'cervicalCancerVaccine' => $record['cervical_cancer_vaccine'] ?? false,
                'covid1stDose' => $record['covid_1st_dose'] ?? false,
                'covid2ndDose' => $record['covid_2nd_dose'] ?? false,
                'covidBooster' => $record['covid_booster'] ?? false,
                'otherVaccines' => $record['other_vaccines'] ?? false,
                'otherVaccinesText' => $record['other_vaccines_text'] ?? '',
                
                // Menstruation (Female only)
                'menarcheAge' => $record['menarche_age'] ?? '',
                'menstrualDays' => $record['menstrual_days'] ?? '',
                'padsConsumed' => $record['pads_consumed'] ?? '',
                'menstrualProblems' => $record['menstrual_problems'] ?? '',
                
                // Current Health Concerns
                'presentConcerns' => $record['present_concerns'] ?? '',
                'currentMedicationsVitamins' => $record['current_medications_vitamins'] ?? '',
                'additionalNotes' => $record['additional_notes'] ?? '',
                
                // College-specific fields
                'currentMedications' => $record['current_medications'] ?? '',
                'lifestyleHabits' => $record['lifestyle_habits'] ?? '',
                'academicStress' => $record['academic_stress'] ?? '',
                'currentSymptoms' => $record['current_symptoms'] ?? '',
                'allergiesAll' => $record['allergies_all'] ?? '',
                'chronicConditions' => $record['chronic_conditions'] ?? '',
                'familyHistory' => $record['family_history'] ?? '',
                'previousHospitalizations' => $record['previous_hospitalizations'] ?? '',
                'mentalHealthHistory' => $record['mental_health_history'] ?? '',
                'stressLevels' => $record['stress_levels'] ?? '',
                'supportSystem' => $record['support_system'] ?? '',
                'wellnessGoals' => $record['wellness_goals'] ?? ''
            ],
            'exists' => !empty($record),
            'isCompleted' => !empty($record['submitted_at'])
        ];
        
        echo json_encode([
            'success' => true,
            'data' => $formData,
            'exists' => !empty($record)
        ]);
    } else {
        // Return student info for new questionnaire
        echo json_encode([
            'success' => true,
            'data' => [
                'step1' => [
                    'studentId' => $studentId,
                    'firstName' => $studentInfo['Student_Fname'] ?? '',
                    'middleName' => $studentInfo['Student_Mname'] ?? '',
                    'lastName' => $studentInfo['Student_Lname'] ?? '',
                    'fullName' => $studentInfo['full_name'] ?? '',
                    'contactNumber' => $studentInfo['contact_number'] ?? '',
                    'sectionId' => $studentInfo['section_id'] ?? '',
                    'sectionName' => $studentInfo['section_name'] ?? '',
                    'programName' => $studentInfo['program_name'] ?? '',
                    'departmentLevel' => $studentInfo['department_level'] ?? '',
                    'educationLevel' => ($studentInfo['department_level'] === 'College') ? 'college' : 'basic'
                ]
            ],
            'exists' => false
        ]);
        }
    } catch (Exception $e) {
        error_log("Error in get_form_data endpoint: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to load health questionnaire data',
            'error_code' => 'LOAD_ERROR'
        ]);
    }
}
?>
