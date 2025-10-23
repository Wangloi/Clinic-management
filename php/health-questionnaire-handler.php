<?php
// Health Questionnaire Handler for Health_Questionnaires table
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
     */
    public function saveHealthData($studentId, $data, $step = 1) {
        try {
            // Check if record exists
            $existing = $this->getHealthRecord($studentId);
            
            if ($existing) {
                return $this->updateHealthData($studentId, $data, $step);
            } else {
                return $this->insertHealthData($studentId, $data, $step);
            }
        } catch (Exception $e) {
            error_log("Error saving health data: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Insert new health questionnaire record
     */
    private function insertHealthData($studentId, $data, $step) {
        $sql = "INSERT INTO Health_Questionnaires (
            student_id, education_level, student_sex, student_birthday, student_age, 
            home_address, height, weight, blood_pressure, heart_rate, respiratory_rate, 
            temperature, submitted_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            $studentId,
            $data['education_level'] ?? 'basic',
            $data['student_sex'] ?? $data['sex'] ?? '',
            $data['student_birthday'] ?? $data['birth_date'] ?? null,
            $data['student_age'] ?? $data['age'] ?? 0,
            $data['home_address'] ?? '',
            $data['height'] ?? null,
            $data['weight'] ?? null,
            $data['blood_pressure'] ?? null,
            $data['heart_rate'] ?? null,
            $data['respiratory_rate'] ?? null,
            $data['temperature'] ?? null
        ]);
    }
    
    /**
     * Update existing health questionnaire record
     */
    private function updateHealthData($studentId, $data, $step) {
        // Build dynamic update query based on provided data and step
        $updateFields = [];
        $params = [];
        
        // Step 1: Personal & Academic Information
        if ($step == 1 || isset($data['student_sex'])) {
            if (isset($data['student_sex']) || isset($data['sex'])) {
                $updateFields[] = "student_sex = ?";
                $params[] = $data['student_sex'] ?? $data['sex'];
            }
            if (isset($data['student_birthday']) || isset($data['birth_date'])) {
                $updateFields[] = "student_birthday = ?";
                $params[] = $data['student_birthday'] ?? $data['birth_date'];
            }
            if (isset($data['student_age']) || isset($data['age'])) {
                $updateFields[] = "student_age = ?";
                $params[] = $data['student_age'] ?? $data['age'];
            }
            if (isset($data['home_address'])) {
                $updateFields[] = "home_address = ?";
                $params[] = $data['home_address'];
            }
            if (isset($data['education_level'])) {
                $updateFields[] = "education_level = ?";
                $params[] = $data['education_level'];
            }
        }
        
        // Step 2: Vital Signs
        if ($step == 2) {
            $vitalFields = ['height', 'weight', 'blood_pressure', 'heart_rate', 'respiratory_rate', 'temperature'];
            foreach ($vitalFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
        }
        
        // Step 3: Health History (Basic Education)
        if ($step == 3) {
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
            if (isset($data['education_level']) && $data['education_level'] === 'college') {
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
            }
        }
        
        if (empty($updateFields)) {
            return true; // Nothing to update
        }
        
        // Always update timestamp
        $updateFields[] = "updated_at = NOW()";
        $params[] = $studentId;
        
        $sql = "UPDATE Health_Questionnaires SET " . implode(', ', $updateFields) . " WHERE student_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Get health questionnaire record for a student
     */
    public function getHealthRecord($studentId) {
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
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get health summary for a student
     */
    public function getHealthSummary($studentId) {
        $sql = "SELECT 
                    hq.questionnaire_id as id,
                    hq.student_id,
                    CONCAT(s.Student_Fname, ' ', IFNULL(s.Student_Mname, ''), ' ', s.Student_Lname) AS full_name,
                    hq.student_age as age,
                    hq.student_sex as sex,
                    sec.section_name,
                    p.program_name,
                    d.department_level,
                    hq.height,
                    hq.weight,
                    hq.blood_pressure,
                    hq.heart_rate,
                    hq.temperature,
                    hq.education_level,
                    hq.submitted_at,
                    hq.created_at,
                    hq.updated_at
                FROM Health_Questionnaires hq
                LEFT JOIN Students s ON hq.student_id = s.student_id
                LEFT JOIN Sections sec ON s.section_id = sec.section_id
                LEFT JOIN Programs p ON sec.program_id = p.program_id
                LEFT JOIN Departments d ON p.department_id = d.department_id
                WHERE hq.student_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all health records (for admin)
     */
    public function getAllHealthRecords($limit = 100, $offset = 0) {
        $sql = "SELECT 
                    hq.questionnaire_id as id,
                    hq.student_id,
                    CONCAT(s.Student_Fname, ' ', IFNULL(s.Student_Mname, ''), ' ', s.Student_Lname) AS full_name,
                    hq.student_age as age,
                    hq.student_sex as sex,
                    sec.section_name,
                    p.program_name,
                    d.department_level,
                    hq.education_level,
                    hq.submitted_at,
                    hq.created_at
                FROM Health_Questionnaires hq
                LEFT JOIN Students s ON hq.student_id = s.student_id
                LEFT JOIN Sections sec ON s.section_id = sec.section_id
                LEFT JOIN Programs p ON sec.program_id = p.program_id
                LEFT JOIN Departments d ON p.department_id = d.department_id
                ORDER BY hq.submitted_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get health conditions summary
     */
    public function getHealthConditions($studentId = null) {
        $baseQuery = "SELECT 
            hq.student_id,
            CONCAT(s.Student_Fname, ' ', s.Student_Lname) AS student_name,
            -- Count of health conditions
            (CASE WHEN hq.has_asthma = 'YES' THEN 1 ELSE 0 END +
             CASE WHEN hq.has_allergies = 'YES' THEN 1 ELSE 0 END +
             CASE WHEN hq.has_healthproblem = 'YES' THEN 1 ELSE 0 END +
             CASE WHEN hq.has_covid19 = 'YES' THEN 1 ELSE 0 END +
             CASE WHEN hq.has_dengue = 'YES' THEN 1 ELSE 0 END +
             CASE WHEN hq.has_anemia = 'YES' THEN 1 ELSE 0 END) AS total_conditions,
            
            -- Specific conditions
            hq.has_asthma,
            hq.has_allergies,
            hq.has_healthproblem,
            hq.has_covid19,
            hq.has_dengue,
            hq.has_anemia,
            hq.has_hospitalization,
            
            hq.submitted_at
        FROM Health_Questionnaires hq
        LEFT JOIN Students s ON hq.student_id = s.student_id";
        
        if ($studentId) {
            $sql = $baseQuery . " WHERE hq.student_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$studentId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $sql = $baseQuery . " ORDER BY total_conditions DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    /**
     * Delete health record
     */
    public function deleteHealthRecord($studentId) {
        $sql = "DELETE FROM Health_Questionnaires WHERE student_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$studentId]);
    }
    
    /**
     * Get completion statistics
     */
    public function getCompletionStats() {
        $sql = "SELECT 
            COUNT(*) as total_records,
            SUM(CASE WHEN submitted_at IS NOT NULL THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN submitted_at IS NULL THEN 1 ELSE 0 END) as incomplete,
            COUNT(CASE WHEN education_level = 'basic' THEN 1 END) as basic_education,
            COUNT(CASE WHEN education_level = 'college' THEN 1 END) as college_education
        FROM Health_Questionnaires";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Search health records
     */
    public function searchHealthRecords($searchTerm, $limit = 50) {
        $sql = "SELECT 
                    hq.questionnaire_id as id,
                    hq.student_id,
                    CONCAT(s.Student_Fname, ' ', IFNULL(s.Student_Mname, ''), ' ', s.Student_Lname) AS full_name,
                    sec.section_name,
                    p.program_name,
                    d.department_level,
                    hq.education_level,
                    hq.submitted_at
                FROM Health_Questionnaires hq
                LEFT JOIN Students s ON hq.student_id = s.student_id
                LEFT JOIN Sections sec ON s.section_id = sec.section_id
                LEFT JOIN Programs p ON sec.program_id = p.program_id
                LEFT JOIN Departments d ON p.department_id = d.department_id
                WHERE hq.student_id LIKE ? 
                OR s.Student_Fname LIKE ? 
                OR s.Student_Lname LIKE ?
                OR p.program_name LIKE ?
                ORDER BY hq.submitted_at DESC 
                LIMIT ?";
        
        $searchPattern = "%$searchTerm%";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$searchPattern, $searchPattern, $searchPattern, $searchPattern, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get student section info for form pre-filling
     */
    public function getStudentInfo($studentId) {
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
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Helper functions for form processing
function sanitizeHealthData($data) {
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        } else {
            $sanitized[$key] = $value;
        }
    }
    return $sanitized;
}

function validateHealthData($data, $step) {
    $errors = [];
    
    switch ($step) {
        case 1: // Personal Information
            $required = ['student_sex', 'student_birthday', 'student_age'];
            foreach ($required as $field) {
                if (empty($data[$field]) && empty($data[str_replace('student_', '', $field)])) {
                    $errors[] = "Field '$field' is required";
                }
            }
            break;
            
        case 2: // Vital Signs
            // Vital signs are optional but validate format if provided
            if (!empty($data['height']) && ($data['height'] < 50 || $data['height'] > 300)) {
                $errors[] = "Height must be between 50-300 cm";
            }
            if (!empty($data['weight']) && ($data['weight'] < 20 || $data['weight'] > 500)) {
                $errors[] = "Weight must be between 20-500 kg";
            }
            break;
            
        case 3: // Health History - No specific validation needed for YES/NO fields
            break;
    }
    
    return $errors;
}
?>
