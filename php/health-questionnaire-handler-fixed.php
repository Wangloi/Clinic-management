<?php
// Fixed Health Questionnaire Handler for Health_Questionnaires table
// This file handles all database operations for the existing Health_Questionnaires table

require_once 'connection.php';

class HealthQuestionnaireHandler {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }
    
    /**
     * Save or update health questionnaire data
     * @param int $studentId Student ID
     * @param array $data Sanitized health data
     * @param int $step Current step (1-6)
     * @return bool Success status
     */
    public function saveHealthData($studentId, $data, $step = 1) {
        try {
            // Validate inputs
            if (!is_int($studentId) || $studentId <= 0) {
                error_log("Invalid student ID: $studentId");
                return false;
            }

            if (!is_array($data)) {
                error_log("Invalid data format for student $studentId");
                return false;
            }

            if (!is_int($step) || $step < 1 || $step > 6) {
                error_log("Invalid step: $step for student $studentId");
                return false;
            }

            // Check if record exists
            $existing = $this->getHealthRecord($studentId);

            if ($existing !== false && !empty($existing)) {
                $result = $this->updateHealthData($studentId, $data, $step);
                if ($result) {
                    error_log("Successfully updated health data for student $studentId at step $step");
                } else {
                    error_log("Failed to update health data for student $studentId at step $step");
                }
                return $result;
            } else {
                $result = $this->insertHealthData($studentId, $data, $step);
                if ($result) {
                    error_log("Successfully inserted health data for student $studentId at step $step");
                } else {
                    error_log("Failed to insert health data for student $studentId at step $step");
                }
                return $result;
            }
        } catch (Exception $e) {
            error_log("Exception saving health data for student $studentId: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Insert new health questionnaire record
     * @param int $studentId Student ID
     * @param array $data Sanitized health data
     * @param int $step Current step
     * @return bool Success status
     */
    private function insertHealthData($studentId, $data, $step) {
        try {
            // Get student info to auto-populate fields
            $studentInfo = $this->getStudentInfo($studentId);
            if ($studentInfo === false) {
                error_log("Failed to get student info for student $studentId during insert");
                return false;
            }

            // Auto-determine education level based on department and program
            $educationLevel = 'basic';
            if ($studentInfo) {
                $deptLevel = isset($studentInfo['department_level']) ? strtolower(trim($studentInfo['department_level'])) : '';
                $programName = isset($studentInfo['program_name']) ? strtolower(trim($studentInfo['program_name'])) : '';

                // Debug logs
                error_log("Student $studentId - Department level: '$deptLevel', Program: '$programName'");

                // Check program name first (more reliable indicator)
                $collegeProgramIndicators = ['bs', 'ba', 'bse', 'bachelor', 'associate', 'diploma', 'certificate'];
                $isCollegeProgram = false;
                foreach ($collegeProgramIndicators as $indicator) {
                    if (strpos(strtolower($programName), $indicator) !== false) {
                        $isCollegeProgram = true;
                        break;
                    }
                }

                // Also check if department_level is 'College'
                if (!$isCollegeProgram && strtolower($deptLevel) === 'college') {
                    $isCollegeProgram = true;
                }

                // Check department level
                $isCollegeDepartment = (strpos($deptLevel, 'college') !== false || strpos($deptLevel, 'tertiary') !== false || strpos($deptLevel, 'higher') !== false);

                // Determine education level
                if ($isCollegeProgram || $isCollegeDepartment) {
                    $educationLevel = 'college';
                }

                error_log("Student $studentId - Determined education level: '$educationLevel' (Program college: " . ($isCollegeProgram ? 'yes' : 'no') . ", Dept college: " . ($isCollegeDepartment ? 'yes' : 'no') . ")");
            }

            $sql = "INSERT INTO Health_Questionnaires (
                student_id, education_level, student_sex, student_birthday, student_age,
                home_address, height, weight, blood_pressure, heart_rate, respiratory_rate,
                temperature, submitted_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare insert statement for student $studentId");
                return false;
            }

            $params = [
                $studentId,
                $data['education_level'] ?? $educationLevel,
                $data['student_sex'] ?? $data['sex'] ?? null,
                $data['student_birthday'] ?? $data['birth_date'] ?? null,
                $data['student_age'] ?? $data['age'] ?? null,
                $data['home_address'] ?? null,
                $data['height'] ?? null,
                $data['weight'] ?? null,
                $data['blood_pressure'] ?? null,
                $data['heart_rate'] ?? null,
                $data['respiratory_rate'] ?? null,
                $data['temperature'] ?? null
            ];

            $result = $stmt->execute($params);
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Insert failed for student $studentId: " . implode(", ", $errorInfo));
            }

            return $result;
        } catch (Exception $e) {
            error_log("Exception during insert for student $studentId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing health questionnaire record
     * @param int $studentId Student ID
     * @param array $data Sanitized health data
     * @param int $step Current step
     * @return bool Success status
     */
    private function updateHealthData($studentId, $data, $step) {
        try {
            // Build dynamic update query based on provided data
            $updateFields = [];
            $params = [];

            // Always allow updating these basic fields
            $basicFields = [
                'student_sex' => 'student_sex',
                'sex' => 'student_sex', // Alternative field name
                'student_birthday' => 'student_birthday',
                'birth_date' => 'student_birthday', // Alternative field name
                'student_age' => 'student_age',
                'age' => 'student_age', // Alternative field name
                'home_address' => 'home_address'
            ];

            foreach ($basicFields as $inputField => $dbField) {
                if (isset($data[$inputField]) && $data[$inputField] !== '') {
                    $updateFields[] = "$dbField = ?";
                    $params[] = $data[$inputField];
                }
            }

            // Auto-update education_level based on student's current department and program
            $studentInfo = $this->getStudentInfo($studentId);
            if ($studentInfo !== false && $studentInfo) {
                $deptLevel = isset($studentInfo['department_level']) ? strtolower(trim($studentInfo['department_level'])) : '';
                $programName = isset($studentInfo['program_name']) ? strtolower(trim($studentInfo['program_name'])) : '';

                // Debug logs
                error_log("Update - Student $studentId - Department level: '$deptLevel', Program: '$programName'");

                $educationLevel = 'basic';

                // Check program name first (more reliable indicator)
                $collegeProgramIndicators = ['bs', 'ba', 'bse', 'bachelor', 'associate', 'diploma', 'certificate'];
                $isCollegeProgram = false;
                foreach ($collegeProgramIndicators as $indicator) {
                    if (strpos(strtolower($programName), $indicator) !== false) {
                        $isCollegeProgram = true;
                        break;
                    }
                }

                // Also check if department_level is 'College'
                if (!$isCollegeProgram && strtolower($deptLevel) === 'college') {
                    $isCollegeProgram = true;
                }

                // Check department level
                $isCollegeDepartment = (strpos($deptLevel, 'college') !== false || strpos($deptLevel, 'tertiary') !== false || strpos($deptLevel, 'higher') !== false);

                // Determine education level
                if ($isCollegeProgram || $isCollegeDepartment) {
                    $educationLevel = 'college';
                }

                error_log("Update - Student $studentId - Determined education level: '$educationLevel' (Program college: " . ($isCollegeProgram ? 'yes' : 'no') . ", Dept college: " . ($isCollegeDepartment ? 'yes' : 'no') . ")");
                $updateFields[] = "education_level = ?";
                $params[] = $educationLevel;
            }
            
            // Vital Signs (Step 2 or any step)
            $vitalFields = ['height', 'weight', 'blood_pressure', 'heart_rate', 'respiratory_rate', 'temperature'];
            foreach ($vitalFields as $field) {
                if (isset($data[$field]) && $data[$field] !== '') {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // Health History Fields (Step 3 or any step)
            $healthFields = [
                'has_allergies', 'allergies_remarks', 'has_medicines', 'medicine_allergies',
                'has_vaccines', 'vaccine_allergies', 'has_foods', 'food_allergies',
                'has_other', 'other_allergies', 'has_asthma', 'asthma_remarks',
                'has_healthproblem', 'healthproblem_remarks', 'has_earinfection', 'earinfection_remarks',
                'has_potty', 'potty_remarks', 'has_uti', 'uti_remarks',
                'has_chickenpox', 'chickenpox_remarks', 'has_dengue', 'dengue_remarks',
                'has_anemia', 'anemia_remarks', 'has_gastritis', 'gastritis_remarks',
                'has_pneumonia', 'pneumonia_remarks', 'has_obesity', 'obesity_remarks',
                'has_covid19', 'covid19_remarks', 'has_otherconditions', 'otherconditions_remarks'
            ];
            
            foreach ($healthFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // Hospitalization
            $hospitalizationFields = ['has_hospitalization', 'hospitalization_date', 'hospital_name', 'hospitalization_remarks'];
            foreach ($hospitalizationFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // Immunization
            $immunizationFields = [
                'pneumonia_vaccine', 'flu_vaccine', 'measles_vaccine', 'hep_b_vaccine',
                'cervical_cancer_vaccine', 'covid_1st_dose', 'covid_2nd_dose', 'covid_booster',
                'other_vaccines', 'other_vaccines_text'
            ];
            foreach ($immunizationFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // Menstruation (Female only)
            $menstrualFields = ['menarche_age', 'menstrual_days', 'pads_consumed', 'menstrual_problems'];
            foreach ($menstrualFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // Current Health Concerns
            $currentHealthFields = ['present_concerns', 'current_medications_vitamins', 'additional_notes'];
            foreach ($currentHealthFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // College-specific fields
            $collegeFields = [
                'current_medications', 'lifestyle_habits', 'academic_stress', 'current_symptoms',
                'allergies_all', 'chronic_conditions', 'family_history', 'previous_hospitalizations',
                'mental_health_history', 'stress_levels', 'support_system', 'wellness_goals'
            ];
            foreach ($collegeFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                error_log("No fields to update for student $studentId at step $step");
                return true; // Nothing to update is not an error
            }

            // Always update timestamp
            $updateFields[] = "updated_at = NOW()";

            // Add student ID for WHERE clause
            $params[] = $studentId;

            $sql = "UPDATE Health_Questionnaires SET " . implode(', ', $updateFields) . " WHERE student_id = ?";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare update statement for student $studentId");
                return false;
            }

            $result = $stmt->execute($params);

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Update failed for student $studentId: " . implode(", ", $errorInfo));
            }

            return $result;

        } catch (Exception $e) {
            error_log("Exception during update for student $studentId: " . $e->getMessage());
            error_log("Update stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Get health questionnaire record for a student
     * @param int $studentId Student ID
     * @return array|false Health record data or false on error
     */
    public function getHealthRecord($studentId) {
        try {
            if (!is_int($studentId) || $studentId <= 0) {
                error_log("Invalid student ID for getHealthRecord: $studentId");
                return false;
            }

            $sql = "SELECT hq.*,
                           s.Student_Fname, s.Student_Mname, s.Student_Lname,
                           sec.section_name,
                           p.program_name,
                           d.department_level
                    FROM Health_Questionnaires hq
                    LEFT JOIN Students s ON hq.student_id = s.student_id
                    LEFT JOIN Sections sec ON s.section_id = sec.section_id
                    LEFT JOIN Programs p ON sec.program_id = p.program_id
                    LEFT JOIN Departments d ON p.department_id = d.department_id
                    WHERE hq.student_id = ?";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare getHealthRecord statement for student $studentId");
                return false;
            }

            $result = $stmt->execute([$studentId]);
            if (!$result) {
                error_log("Failed to execute getHealthRecord for student $studentId");
                return false;
            }

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Exception in getHealthRecord for student $studentId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get student info for form pre-filling
     * @param int $studentId Student ID
     * @return array|false Student info or false on error
     */
    public function getStudentInfo($studentId) {
        try {
            if (!is_int($studentId) || $studentId <= 0) {
                error_log("Invalid student ID for getStudentInfo: $studentId");
                return false;
            }

            $sql = "SELECT
                        s.student_id,
                        s.Student_Fname,
                        s.Student_Mname,
                        s.Student_Lname,
                        s.contact_number,
                        s.section_id,
                        sec.section_name,
                        p.program_id,
                        p.program_name,
                        d.department_id,
                        d.department_level
                    FROM Students s
                    LEFT JOIN Sections sec ON s.section_id = sec.section_id
                    LEFT JOIN Programs p ON sec.program_id = p.program_id
                    LEFT JOIN Departments d ON p.department_id = d.department_id
                    WHERE s.student_id = ?";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare getStudentInfo statement for student $studentId");
                return false;
            }

            $result = $stmt->execute([$studentId]);
            if (!$result) {
                error_log("Failed to execute getStudentInfo for student $studentId");
                return false;
            }

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Add formatted full name
            if ($result) {
                $result['full_name'] = $this->formatStudentName(
                    $result['Student_Fname'],
                    $result['Student_Mname'],
                    $result['Student_Lname']
                );
            }

            return $result;
        } catch (Exception $e) {
            error_log("Exception in getStudentInfo for student $studentId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format student name properly
     */
    private function formatStudentName($firstName, $middleName, $lastName) {
        $nameParts = [];
        
        if (!empty($firstName)) {
            $nameParts[] = trim($firstName);
        }
        
        if (!empty($middleName)) {
            $nameParts[] = trim($middleName);
        }
        
        if (!empty($lastName)) {
            $nameParts[] = trim($lastName);
        }
        
        return implode(' ', $nameParts);
    }
    
    /**
     * Get student name only by ID
     * @param int $studentId Student ID
     * @return string|null Student name or null if not found/error
     */
    public function getStudentName($studentId) {
        try {
            if (!is_int($studentId) || $studentId <= 0) {
                error_log("Invalid student ID for getStudentName: $studentId");
                return null;
            }

            $sql = "SELECT Student_Fname, Student_Mname, Student_Lname FROM Students WHERE student_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare getStudentName statement for student $studentId");
                return null;
            }

            $result = $stmt->execute([$studentId]);
            if (!$result) {
                error_log("Failed to execute getStudentName for student $studentId");
                return null;
            }

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $this->formatStudentName(
                    $result['Student_Fname'],
                    $result['Student_Mname'],
                    $result['Student_Lname']
                );
            }

            return null;
        } catch (Exception $e) {
            error_log("Exception in getStudentName for student $studentId: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Test database connection and table existence
     * @return array Connection and table status
     */
    public function testConnection() {
        try {
            // Test connection
            $stmt = $this->conn->query("SELECT 1");
            $connectionOk = $stmt !== false;

            // Test Health_Questionnaires table
            $stmt = $this->conn->query("SHOW TABLES LIKE 'Health_Questionnaires'");
            $tableExists = $stmt && $stmt->fetch() !== false;

            // Test Students table
            $stmt = $this->conn->query("SHOW TABLES LIKE 'Students'");
            $studentsTableExists = $stmt && $stmt->fetch() !== false;

            return [
                'connection' => $connectionOk,
                'health_table' => $tableExists,
                'students_table' => $studentsTableExists
            ];
        } catch (Exception $e) {
            error_log("Exception in testConnection: " . $e->getMessage());
            return [
                'connection' => false,
                'health_table' => false,
                'students_table' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete health record for a student
     * @param int $studentId Student ID
     * @return bool Success status
     */
    public function deleteHealthRecord($studentId) {
        try {
            if (!is_int($studentId) || $studentId <= 0) {
                error_log("Invalid student ID for deleteHealthRecord: $studentId");
                return false;
            }

            $sql = "DELETE FROM Health_Questionnaires WHERE student_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare deleteHealthRecord statement for student $studentId");
                return false;
            }

            $result = $stmt->execute([$studentId]);
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Delete failed for student $studentId: " . implode(", ", $errorInfo));
            }

            return $result;
        } catch (Exception $e) {
            error_log("Exception in deleteHealthRecord for student $studentId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get health summary for a student (placeholder for future implementation)
     * @param int $studentId Student ID
     * @return array|false Summary data or false on error
     */
    public function getHealthSummary($studentId) {
        // Placeholder - implement based on requirements
        error_log("getHealthSummary called for student $studentId - not implemented yet");
        return ['message' => 'Summary feature not implemented yet'];
    }

    /**
     * Get health conditions for a student (placeholder for future implementation)
     * @param int $studentId Student ID
     * @return array|false Conditions data or false on error
     */
    public function getHealthConditions($studentId) {
        // Placeholder - implement based on requirements
        error_log("getHealthConditions called for student $studentId - not implemented yet");
        return ['message' => 'Conditions feature not implemented yet'];
    }
}

// Helper functions for form processing

/**
 * Sanitize health data input
 * @param array $data Raw input data
 * @return array Sanitized data
 */
function sanitizeHealthData($data) {
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            // Trim whitespace and escape HTML characters
            $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        } elseif (is_numeric($value)) {
            // Convert numeric strings to appropriate types
            if (is_string($value) && strpos($value, '.') !== false) {
                $sanitized[$key] = (float)$value;
            } else {
                $sanitized[$key] = (int)$value;
            }
        } elseif (is_bool($value)) {
            $sanitized[$key] = $value;
        } else {
            // For other types, keep as is but log potential issues
            $sanitized[$key] = $value;
            error_log("Unexpected data type for key '$key': " . gettype($value));
        }
    }
    return $sanitized;
}

/**
 * Validate health data based on step
 * @param array $data Sanitized data
 * @param int $step Current step (1-6)
 * @return array Array of validation errors
 */
function validateHealthData($data, $step) {
    $errors = [];

    switch ($step) {
        case 1: // Personal Information
            $gender = $data['student_sex'] ?? $data['sex'] ?? '';
            if (empty($gender) || !in_array(strtolower($gender), ['male', 'female'])) {
                $errors[] = "Valid gender (Male/Female) is required";
            }

            $age = $data['student_age'] ?? $data['age'] ?? '';
            if (empty($age) || !is_numeric($age) || $age < 1 || $age > 120) {
                $errors[] = "Valid age (1-120) is required";
            }
            break;

        case 2: // Vital Signs - Optional but validate format if provided
            if (isset($data['height']) && (!is_numeric($data['height']) || $data['height'] < 50 || $data['height'] > 300)) {
                $errors[] = "Height must be a number between 50-300 cm";
            }
            if (isset($data['weight']) && (!is_numeric($data['weight']) || $data['weight'] < 20 || $data['weight'] > 500)) {
                $errors[] = "Weight must be a number between 20-500 kg";
            }
            if (isset($data['heart_rate']) && (!is_numeric($data['heart_rate']) || $data['heart_rate'] < 30 || $data['heart_rate'] > 200)) {
                $errors[] = "Heart rate must be between 30-200 bpm";
            }
            if (isset($data['temperature']) && (!is_numeric($data['temperature']) || $data['temperature'] < 30 || $data['temperature'] > 45)) {
                $errors[] = "Temperature must be between 30-45°C";
            }
            break;

        case 3: // Health History - Validate YES/NO fields
            $yesNoFields = [
                'has_allergies', 'has_medicines', 'has_vaccines', 'has_foods', 'has_other',
                'has_asthma', 'has_healthproblem', 'has_earinfection', 'has_potty', 'has_uti',
                'has_chickenpox', 'has_dengue', 'has_anemia', 'has_gastritis', 'has_pneumonia',
                'has_obesity', 'has_covid19', 'has_otherconditions', 'has_hospitalization'
            ];

            foreach ($yesNoFields as $field) {
                if (isset($data[$field]) && !in_array(strtoupper($data[$field]), ['YES', 'NO'])) {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . " must be YES or NO";
                }
            }
            break;

        default:
            // For other steps, basic validation
            break;
    }

    return $errors;
}
?>
