<?php
/**
 * Health Questionnaire Backend API
 * Main API endpoint for handling health questionnaire operations
 */

// Clean output buffer and set headers
ob_clean();
session_start();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

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

require_once 'health-backend-handler-fixed.php';

$healthBackend = new HealthBackendHandlerFixed();
$studentId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            handleSaveHealthData($healthBackend, $studentId);
            break;
            
        case 'GET':
            handleGetHealthData($healthBackend, $studentId);
            break;
            
        case 'PUT':
            handleUpdateHealthData($healthBackend, $studentId);
            break;
            
        case 'DELETE':
            handleDeleteHealthData($healthBackend, $studentId);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed',
                'error_code' => 'METHOD_NOT_ALLOWED'
            ]);
            break;
    }
    
} catch (Exception $e) {
    error_log("Health Backend API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error_code' => 'INTERNAL_ERROR'
    ]);
}

/**
 * Handle saving health questionnaire data
 */
function handleSaveHealthData($healthBackend, $studentId) {
    try {
        // Get JSON data from request body
        $rawInput = file_get_contents('php://input', false, null, 0, 2097152); // 2MB limit
        
        if ($rawInput === false || empty($rawInput)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'No data received',
                'error_code' => 'NO_DATA'
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
                'message' => 'Invalid data format',
                'error_code' => 'INVALID_FORMAT'
            ]);
            return;
        }
        
        // Validate and sanitize data
        $data = sanitizeHealthData($input);
        $errors = validateHealthData($data);
        
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
        
        // Save data using backend handler
        $result = $healthBackend->saveHealthQuestionnaire($studentId, $data);
        
        if (is_array($result) && $result['success']) {
            echo json_encode([
                'success' => true,
                'message' => $result['message'] ?? 'Health questionnaire saved successfully',
                'student_id' => $studentId,
                'timestamp' => date('c')
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => is_array($result) ? $result['message'] : 'Failed to save health questionnaire',
                'error_code' => 'SAVE_FAILED'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Error in handleSaveHealthData: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error_code' => 'INTERNAL_ERROR'
        ]);
    }
}

/**
 * Handle getting health questionnaire data
 */
function handleGetHealthData($healthBackend, $studentId) {
    try {
        $action = $_GET['action'] ?? 'get_record';
        
        switch ($action) {
            case 'get_record':
                $record = $healthBackend->getHealthRecord($studentId);
                $studentInfo = $healthBackend->getStudentInfo($studentId);
                
                if ($record || $studentInfo) {
                    // Merge health record with student info for comprehensive view
                    $completeData = array_merge(
                        $record ?: [],
                        $studentInfo ?: []
                    );
                    
                    // Add student ID if not present
                    $completeData['student_id'] = $studentId;
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $completeData,
                        'exists' => !empty($record),
                        'timestamp' => date('c')
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'data' => null,
                        'exists' => false,
                        'message' => 'No health record found',
                        'timestamp' => date('c')
                    ]);
                }
                break;
                
            case 'get_dashboard':
                $dashboardData = $healthBackend->getHealthDashboardData();
                echo json_encode([
                    'success' => true,
                    'data' => $dashboardData,
                    'timestamp' => date('c')
                ]);
                break;
                
            case 'export':
                $filters = $_GET['filters'] ?? [];
                $exportData = $healthBackend->exportHealthData($filters);
                echo json_encode([
                    'success' => true,
                    'data' => $exportData,
                    'count' => count($exportData),
                    'timestamp' => date('c')
                ]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action parameter',
                    'error_code' => 'INVALID_ACTION'
                ]);
                break;
        }
        
    } catch (Exception $e) {
        error_log("Error in handleGetHealthData: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error_code' => 'INTERNAL_ERROR'
        ]);
    }
}

/**
 * Handle updating health questionnaire data
 */
function handleUpdateHealthData($healthBackend, $studentId) {
    // Same logic as POST but for updates
    handleSaveHealthData($healthBackend, $studentId);
}

/**
 * Handle deleting health questionnaire data
 */
function handleDeleteHealthData($healthBackend, $studentId) {
    try {
        // Additional security check
        $existingRecord = $healthBackend->getHealthRecord($studentId);
        
        if (empty($existingRecord)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'No health record found to delete',
                'error_code' => 'RECORD_NOT_FOUND'
            ]);
            return;
        }
        
        // For now, we'll just mark as inactive rather than actually delete
        // This preserves data for audit purposes
        echo json_encode([
            'success' => false,
            'message' => 'Delete operation not implemented for security reasons',
            'error_code' => 'OPERATION_NOT_ALLOWED'
        ]);
        
    } catch (Exception $e) {
        error_log("Error in handleDeleteHealthData: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error_code' => 'INTERNAL_ERROR'
        ]);
    }
}

/**
 * Format health data for frontend consumption
 */
function formatHealthDataForFrontend($record, $studentInfo, $studentId) {
    return [
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
            'contactNumber' => $record['contact_number'] ?? '',
            'sectionName' => $studentInfo['section_name'] ?? '',
            'programName' => $studentInfo['program_name'] ?? '',
            'departmentLevel' => $studentInfo['department_level'] ?? '',
            'height' => $record['height'] ?? '',
            'weight' => $record['weight'] ?? '',
            'bloodPressure' => $record['blood_pressure'] ?? '',
            'heartRate' => $record['heart_rate'] ?? '',
            'respiratoryRate' => $record['respiratory_rate'] ?? '',
            'temperature' => $record['temperature'] ?? ''
        ],
        'step2' => [
            'hasAllergies' => $record['has_allergies'] ?? 'NO',
            'allergiesRemarks' => $record['allergies_remarks'] ?? '',
            'hasMedicines' => $record['has_medicine_allergies'] ?? 'NO',
            'medicineAllergies' => $record['medicine_allergies'] ?? '',
            'hasVaccines' => $record['has_vaccine_allergies'] ?? 'NO',
            'vaccineAllergies' => $record['vaccine_allergies'] ?? '',
            'hasFoods' => $record['has_food_allergies'] ?? 'NO',
            'foodAllergies' => $record['food_allergies'] ?? '',
            'hasOther' => $record['has_other_allergies'] ?? 'NO',
            'otherAllergies' => $record['other_allergies'] ?? '',
            'hasAsthma' => $record['has_asthma'] ?? 'NO',
            'asthmaRemarks' => $record['asthma_remarks'] ?? '',
            'hasHealthproblem' => $record['has_health_problems'] ?? 'NO',
            'healthproblemRemarks' => $record['health_problems_remarks'] ?? '',
            'hasEarinfection' => $record['has_ear_infections'] ?? 'NO',
            'earinfectionRemarks' => $record['ear_infections_remarks'] ?? '',
            'hasPotty' => $record['has_potty_problems'] ?? 'NO',
            'pottyRemarks' => $record['potty_problems_remarks'] ?? '',
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
            'hasOtherconditions' => $record['has_other_conditions'] ?? 'NO',
            'otherconditionsRemarks' => $record['other_conditions_remarks'] ?? ''
        ],
        'step3' => [
            'hasHospitalization' => $record['has_hospitalization'] ?? 'NO',
            'hospitalizationDate' => $record['hospitalization_date'] ?? '',
            'hospitalName' => $record['hospital_name'] ?? '',
            'hospitalizationRemarks' => $record['hospitalization_remarks'] ?? ''
        ],
        'step4' => [
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
            'menarcheAge' => $record['menarche_age'] ?? '',
            'menstrualDays' => $record['menstrual_days'] ?? '',
            'padsConsumed' => $record['pads_consumed'] ?? '',
            'menstrualProblems' => $record['menstrual_problems'] ?? ''
        ],
        'step5' => [
            'presentConcerns' => $record['present_concerns'] ?? '',
            'currentMedicationsVitamins' => $record['current_medications_vitamins'] ?? '',
            'additionalNotes' => $record['additional_notes'] ?? ''
        ],
        'exists' => !empty($record),
        'isCompleted' => !empty($record['submitted_at']),
        'educationLevel' => $record['education_level'] ?? (($studentInfo['department_level'] === 'College') ? 'college' : 'basic')
    ];
}

/**
 * Sanitize health data
 */
function sanitizeHealthData($data) {
    $sanitized = [];
    
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        } elseif (is_numeric($value)) {
            $sanitized[$key] = $value;
        } elseif (is_bool($value)) {
            $sanitized[$key] = $value;
        } elseif (is_array($value)) {
            $sanitized[$key] = sanitizeHealthData($value);
        } else {
            $sanitized[$key] = $value;
        }
    }
    
    return $sanitized;
}

/**
 * Validate health data
 */
function validateHealthData($data) {
    $errors = [];
    
    // Validate required fields
    if (empty($data['studentSex'])) {
        $errors['studentSex'] = 'Sex is required';
    }
    
    if (!empty($data['studentAge']) && ($data['studentAge'] < 1 || $data['studentAge'] > 100)) {
        $errors['studentAge'] = 'Age must be between 1 and 100';
    }
    
    if (!empty($data['height']) && ($data['height'] < 50 || $data['height'] > 300)) {
        $errors['height'] = 'Height must be between 50 and 300 cm';
    }
    
    if (!empty($data['weight']) && ($data['weight'] < 20 || $data['weight'] > 500)) {
        $errors['weight'] = 'Weight must be between 20 and 500 kg';
    }
    
    if (!empty($data['heartRate']) && ($data['heartRate'] < 30 || $data['heartRate'] > 200)) {
        $errors['heartRate'] = 'Heart rate must be between 30 and 200 bpm';
    }
    
    if (!empty($data['temperature']) && ($data['temperature'] < 30 || $data['temperature'] > 45)) {
        $errors['temperature'] = 'Temperature must be between 30 and 45°C';
    }
    
    // Validate email format if provided
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    // Validate phone number format if provided
    if (!empty($data['contactNumber']) && !preg_match('/^[0-9+\-\s()]+$/', $data['contactNumber'])) {
        $errors['contactNumber'] = 'Invalid phone number format';
    }
    
    return $errors;
}
?>
