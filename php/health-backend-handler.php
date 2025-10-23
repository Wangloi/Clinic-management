<?php
/**
 * Health Questionnaire Backend Handler
 * Comprehensive backend system for managing student health questionnaire data
 */

require_once 'connection.php';

class HealthBackendHandler {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }
    
    /**
     * Save complete health questionnaire data
     */
    public function saveHealthQuestionnaire($studentId, $data) {
        try {
            $this->conn->beginTransaction();
            
            // Check if record exists
            $existingRecord = $this->getHealthRecord($studentId);
            
            if ($existingRecord) {
                // Update existing record
                $result = $this->updateHealthRecord($studentId, $data);
            } else {
                // Create new record
                $result = $this->createHealthRecord($studentId, $data);
            }
            
            if ($result) {
                // Update health conditions summary
                $this->updateHealthConditionsSummary($studentId, $data);
                
                // Generate health alerts if needed
                $this->generateHealthAlerts($studentId, $data);
                
                // Update statistics
                $this->updateHealthStatistics();
                
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error saving health questionnaire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new health record
     */
    private function createHealthRecord($studentId, $data) {
        $sql = "INSERT INTO health_questionnaires (
            student_id, student_sex, student_birthday, student_age, home_address, contact_number,
            height, weight, blood_pressure, heart_rate, respiratory_rate, temperature,
            has_allergies, allergies_remarks, has_medicine_allergies, medicine_allergies,
            has_vaccine_allergies, vaccine_allergies, has_food_allergies, food_allergies,
            has_other_allergies, other_allergies, has_asthma, asthma_remarks,
            has_health_problems, health_problems_remarks, has_ear_infections, ear_infections_remarks,
            has_potty_problems, potty_problems_remarks, has_uti, uti_remarks,
            has_chickenpox, chickenpox_remarks, has_dengue, dengue_remarks,
            has_anemia, anemia_remarks, has_gastritis, gastritis_remarks,
            has_pneumonia, pneumonia_remarks, has_obesity, obesity_remarks,
            has_covid19, covid19_remarks, has_other_conditions, other_conditions_remarks,
            has_hospitalization, hospitalization_date, hospital_name, hospitalization_remarks,
            pneumonia_vaccine, flu_vaccine, measles_vaccine, hep_b_vaccine,
            cervical_cancer_vaccine, covid_1st_dose, covid_2nd_dose, covid_booster,
            other_vaccines, other_vaccines_text,
            menarche_age, menstrual_days, pads_consumed, menstrual_problems,
            present_concerns, current_medications_vitamins, additional_notes,
            education_level, is_completed, completion_step, submitted_at
        ) VALUES (
            :student_id, :student_sex, :student_birthday, :student_age, :home_address, :contact_number,
            :height, :weight, :blood_pressure, :heart_rate, :respiratory_rate, :temperature,
            :has_allergies, :allergies_remarks, :has_medicine_allergies, :medicine_allergies,
            :has_vaccine_allergies, :vaccine_allergies, :has_food_allergies, :food_allergies,
            :has_other_allergies, :other_allergies, :has_asthma, :asthma_remarks,
            :has_health_problems, :health_problems_remarks, :has_ear_infections, :ear_infections_remarks,
            :has_potty_problems, :potty_problems_remarks, :has_uti, :uti_remarks,
            :has_chickenpox, :chickenpox_remarks, :has_dengue, :dengue_remarks,
            :has_anemia, :anemia_remarks, :has_gastritis, :gastritis_remarks,
            :has_pneumonia, :pneumonia_remarks, :has_obesity, :obesity_remarks,
            :has_covid19, :covid19_remarks, :has_other_conditions, :other_conditions_remarks,
            :has_hospitalization, :hospitalization_date, :hospital_name, :hospitalization_remarks,
            :pneumonia_vaccine, :flu_vaccine, :measles_vaccine, :hep_b_vaccine,
            :cervical_cancer_vaccine, :covid_1st_dose, :covid_2nd_dose, :covid_booster,
            :other_vaccines, :other_vaccines_text,
            :menarche_age, :menstrual_days, :pads_consumed, :menstrual_problems,
            :present_concerns, :current_medications_vitamins, :additional_notes,
            :education_level, :is_completed, :completion_step, :submitted_at
        )";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($this->prepareHealthData($studentId, $data));
    }
    
    /**
     * Update existing health record
     */
    private function updateHealthRecord($studentId, $data) {
        $sql = "UPDATE health_questionnaires SET
            student_sex = :student_sex, student_birthday = :student_birthday, 
            student_age = :student_age, home_address = :home_address, contact_number = :contact_number,
            height = :height, weight = :weight, blood_pressure = :blood_pressure, 
            heart_rate = :heart_rate, respiratory_rate = :respiratory_rate, temperature = :temperature,
            has_allergies = :has_allergies, allergies_remarks = :allergies_remarks,
            has_medicine_allergies = :has_medicine_allergies, medicine_allergies = :medicine_allergies,
            has_vaccine_allergies = :has_vaccine_allergies, vaccine_allergies = :vaccine_allergies,
            has_food_allergies = :has_food_allergies, food_allergies = :food_allergies,
            has_other_allergies = :has_other_allergies, other_allergies = :other_allergies,
            has_asthma = :has_asthma, asthma_remarks = :asthma_remarks,
            has_health_problems = :has_health_problems, health_problems_remarks = :health_problems_remarks,
            has_ear_infections = :has_ear_infections, ear_infections_remarks = :ear_infections_remarks,
            has_potty_problems = :has_potty_problems, potty_problems_remarks = :potty_problems_remarks,
            has_uti = :has_uti, uti_remarks = :uti_remarks,
            has_chickenpox = :has_chickenpox, chickenpox_remarks = :chickenpox_remarks,
            has_dengue = :has_dengue, dengue_remarks = :dengue_remarks,
            has_anemia = :has_anemia, anemia_remarks = :anemia_remarks,
            has_gastritis = :has_gastritis, gastritis_remarks = :gastritis_remarks,
            has_pneumonia = :has_pneumonia, pneumonia_remarks = :pneumonia_remarks,
            has_obesity = :has_obesity, obesity_remarks = :obesity_remarks,
            has_covid19 = :has_covid19, covid19_remarks = :covid19_remarks,
            has_other_conditions = :has_other_conditions, other_conditions_remarks = :other_conditions_remarks,
            has_hospitalization = :has_hospitalization, hospitalization_date = :hospitalization_date,
            hospital_name = :hospital_name, hospitalization_remarks = :hospitalization_remarks,
            pneumonia_vaccine = :pneumonia_vaccine, flu_vaccine = :flu_vaccine,
            measles_vaccine = :measles_vaccine, hep_b_vaccine = :hep_b_vaccine,
            cervical_cancer_vaccine = :cervical_cancer_vaccine, covid_1st_dose = :covid_1st_dose,
            covid_2nd_dose = :covid_2nd_dose, covid_booster = :covid_booster,
            other_vaccines = :other_vaccines, other_vaccines_text = :other_vaccines_text,
            menarche_age = :menarche_age, menstrual_days = :menstrual_days,
            pads_consumed = :pads_consumed, menstrual_problems = :menstrual_problems,
            present_concerns = :present_concerns, current_medications_vitamins = :current_medications_vitamins,
            additional_notes = :additional_notes, education_level = :education_level,
            is_completed = :is_completed, completion_step = :completion_step,
            submitted_at = :submitted_at, updated_at = CURRENT_TIMESTAMP
            WHERE student_id = :student_id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($this->prepareHealthData($studentId, $data));
    }
    
    /**
     * Prepare health data for database insertion
     */
    private function prepareHealthData($studentId, $data) {
        return [
            ':student_id' => $studentId,
            ':student_sex' => $data['studentSex'] ?? null,
            ':student_birthday' => $data['studentBirthday'] ?? null,
            ':student_age' => $data['studentAge'] ?? null,
            ':home_address' => $data['homeAddress'] ?? null,
            ':contact_number' => $data['contactNumber'] ?? null,
            ':height' => $data['height'] ?? null,
            ':weight' => $data['weight'] ?? null,
            ':blood_pressure' => $data['bloodPressure'] ?? null,
            ':heart_rate' => $data['heartRate'] ?? null,
            ':respiratory_rate' => $data['respiratoryRate'] ?? null,
            ':temperature' => $data['temperature'] ?? null,
            ':has_allergies' => $data['hasAllergies'] ?? 'NO',
            ':allergies_remarks' => $data['allergiesRemarks'] ?? null,
            ':has_medicine_allergies' => $data['hasMedicines'] ?? 'NO',
            ':medicine_allergies' => $data['medicineAllergies'] ?? null,
            ':has_vaccine_allergies' => $data['hasVaccines'] ?? 'NO',
            ':vaccine_allergies' => $data['vaccineAllergies'] ?? null,
            ':has_food_allergies' => $data['hasFoods'] ?? 'NO',
            ':food_allergies' => $data['foodAllergies'] ?? null,
            ':has_other_allergies' => $data['hasOther'] ?? 'NO',
            ':other_allergies' => $data['otherAllergies'] ?? null,
            ':has_asthma' => $data['hasAsthma'] ?? 'NO',
            ':asthma_remarks' => $data['asthmaRemarks'] ?? null,
            ':has_health_problems' => $data['hasHealthproblem'] ?? 'NO',
            ':health_problems_remarks' => $data['healthproblemRemarks'] ?? null,
            ':has_ear_infections' => $data['hasEarinfection'] ?? 'NO',
            ':ear_infections_remarks' => $data['earinfectionRemarks'] ?? null,
            ':has_potty_problems' => $data['hasPotty'] ?? 'NO',
            ':potty_problems_remarks' => $data['pottyRemarks'] ?? null,
            ':has_uti' => $data['hasUti'] ?? 'NO',
            ':uti_remarks' => $data['utiRemarks'] ?? null,
            ':has_chickenpox' => $data['hasChickenpox'] ?? 'NO',
            ':chickenpox_remarks' => $data['chickenpoxRemarks'] ?? null,
            ':has_dengue' => $data['hasDengue'] ?? 'NO',
            ':dengue_remarks' => $data['dengueRemarks'] ?? null,
            ':has_anemia' => $data['hasAnemia'] ?? 'NO',
            ':anemia_remarks' => $data['anemiaRemarks'] ?? null,
            ':has_gastritis' => $data['hasGastritis'] ?? 'NO',
            ':gastritis_remarks' => $data['gastritisRemarks'] ?? null,
            ':has_pneumonia' => $data['hasPneumonia'] ?? 'NO',
            ':pneumonia_remarks' => $data['pneumoniaRemarks'] ?? null,
            ':has_obesity' => $data['hasObesity'] ?? 'NO',
            ':obesity_remarks' => $data['obesityRemarks'] ?? null,
            ':has_covid19' => $data['hasCovid19'] ?? 'NO',
            ':covid19_remarks' => $data['covid19Remarks'] ?? null,
            ':has_other_conditions' => $data['hasOtherconditions'] ?? 'NO',
            ':other_conditions_remarks' => $data['otherconditionsRemarks'] ?? null,
            ':has_hospitalization' => $data['hasHospitalization'] ?? 'NO',
            ':hospitalization_date' => $data['hospitalizationDate'] ?? null,
            ':hospital_name' => $data['hospitalName'] ?? null,
            ':hospitalization_remarks' => $data['hospitalizationRemarks'] ?? null,
            ':pneumonia_vaccine' => isset($data['pneumoniaVaccine']) ? 1 : 0,
            ':flu_vaccine' => isset($data['fluVaccine']) ? 1 : 0,
            ':measles_vaccine' => isset($data['measlesVaccine']) ? 1 : 0,
            ':hep_b_vaccine' => isset($data['hepBVaccine']) ? 1 : 0,
            ':cervical_cancer_vaccine' => isset($data['cervicalCancerVaccine']) ? 1 : 0,
            ':covid_1st_dose' => isset($data['covid1stDose']) ? 1 : 0,
            ':covid_2nd_dose' => isset($data['covid2ndDose']) ? 1 : 0,
            ':covid_booster' => isset($data['covidBooster']) ? 1 : 0,
            ':other_vaccines' => isset($data['otherVaccines']) ? 1 : 0,
            ':other_vaccines_text' => $data['otherVaccinesText'] ?? null,
            ':menarche_age' => $data['menarcheAge'] ?? null,
            ':menstrual_days' => $data['menstrualDays'] ?? null,
            ':pads_consumed' => $data['padsConsumed'] ?? null,
            ':menstrual_problems' => $data['menstrualProblems'] ?? null,
            ':present_concerns' => $data['presentConcerns'] ?? null,
            ':current_medications_vitamins' => $data['currentMedicationsVitamins'] ?? null,
            ':additional_notes' => $data['additionalNotes'] ?? null,
            ':education_level' => $data['educationLevel'] ?? 'basic',
            ':is_completed' => isset($data['isCompleted']) ? 1 : 0,
            ':completion_step' => $data['completionStep'] ?? 1,
            ':submitted_at' => isset($data['isCompleted']) ? date('Y-m-d H:i:s') : null
        ];
    }
    
    /**
     * Get health record for a student
     */
    public function getHealthRecord($studentId) {
        $sql = "SELECT * FROM health_questionnaires WHERE student_id = :student_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get student information from the students table
     */
    public function getStudentInfo($studentId) {
        // First try the simple query to get basic student info
        try {
            $sql = "SELECT s.*, 
                           CONCAT(s.Student_Fname, ' ', COALESCE(s.Student_Mname, ''), ' ', s.Student_Lname) as full_name
                    FROM students s
                    WHERE s.Student_ID = :student_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':student_id' => $studentId]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                return false;
            }
            
            // Try to get section info if section_id exists
            if (!empty($student['section_id'])) {
                try {
                    $sectionSql = "SELECT section_name FROM sections WHERE section_id = :section_id";
                    $sectionStmt = $this->conn->prepare($sectionSql);
                    $sectionStmt->execute([':section_id' => $student['section_id']]);
                    $section = $sectionStmt->fetch(PDO::FETCH_ASSOC);
                    if ($section) {
                        $student['section_name'] = $section['section_name'];
                    }
                } catch (Exception $e) {
                    // Section table might not exist, continue without it
                    $student['section_name'] = 'Unknown';
                }
            }
            
            // Try to get program info if program_id exists
            if (!empty($student['program_id'])) {
                try {
                    $programSql = "SELECT program_name FROM programs WHERE program_id = :program_id";
                    $programStmt = $this->conn->prepare($programSql);
                    $programStmt->execute([':program_id' => $student['program_id']]);
                    $program = $programStmt->fetch(PDO::FETCH_ASSOC);
                    if ($program) {
                        $student['program_name'] = $program['program_name'];
                    }
                } catch (Exception $e) {
                    // Program table might not exist, continue without it
                    $student['program_name'] = 'Unknown';
                }
            }
            
            // Try to get department info if it exists
            try {
                // Check if departments table exists and has the expected columns
                $checkDeptSql = "SHOW TABLES LIKE 'departments'";
                $checkStmt = $this->conn->query($checkDeptSql);
                
                if ($checkStmt->rowCount() > 0 && !empty($student['program_id'])) {
                    // Try to get department through program
                    $deptSql = "SELECT d.* FROM departments d 
                               LEFT JOIN programs p ON d.department_id = p.department_id 
                               WHERE p.program_id = :program_id";
                    $deptStmt = $this->conn->prepare($deptSql);
                    $deptStmt->execute([':program_id' => $student['program_id']]);
                    $dept = $deptStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($dept) {
                        // Use whatever department columns are available
                        $student['department_name'] = $dept['department_name'] ?? $dept['dept_name'] ?? 'Unknown';
                        $student['department_level'] = $dept['department_level'] ?? $dept['level'] ?? 'Unknown';
                    }
                }
            } catch (Exception $e) {
                // Department table might not exist or have different structure
                $student['department_name'] = 'Unknown';
                $student['department_level'] = 'Unknown';
            }
            
            // Set default values for missing fields
            $student['section_name'] = $student['section_name'] ?? 'Unknown';
            $student['program_name'] = $student['program_name'] ?? 'Unknown';
            $student['department_name'] = $student['department_name'] ?? 'Unknown';
            $student['department_level'] = $student['department_level'] ?? 'basic';
            
            return $student;
            
        } catch (Exception $e) {
            error_log("Error in getStudentInfo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update health conditions summary
     */
    private function updateHealthConditionsSummary($studentId, $data) {
        // Delete existing conditions for this student
        $deleteSql = "DELETE FROM health_conditions_summary WHERE student_id = :student_id";
        $deleteStmt = $this->conn->prepare($deleteSql);
        $deleteStmt->execute([':student_id' => $studentId]);
        
        // Define health conditions to track
        $conditions = [
            'allergies' => $data['hasAllergies'] ?? 'NO',
            'medicine_allergies' => $data['hasMedicines'] ?? 'NO',
            'vaccine_allergies' => $data['hasVaccines'] ?? 'NO',
            'food_allergies' => $data['hasFoods'] ?? 'NO',
            'other_allergies' => $data['hasOther'] ?? 'NO',
            'asthma' => $data['hasAsthma'] ?? 'NO',
            'health_problems' => $data['hasHealthproblem'] ?? 'NO',
            'ear_infections' => $data['hasEarinfection'] ?? 'NO',
            'potty_problems' => $data['hasPotty'] ?? 'NO',
            'uti' => $data['hasUti'] ?? 'NO',
            'chickenpox' => $data['hasChickenpox'] ?? 'NO',
            'dengue' => $data['hasDengue'] ?? 'NO',
            'anemia' => $data['hasAnemia'] ?? 'NO',
            'gastritis' => $data['hasGastritis'] ?? 'NO',
            'pneumonia' => $data['hasPneumonia'] ?? 'NO',
            'obesity' => $data['hasObesity'] ?? 'NO',
            'covid19' => $data['hasCovid19'] ?? 'NO',
            'other_conditions' => $data['hasOtherconditions'] ?? 'NO',
            'hospitalization' => $data['hasHospitalization'] ?? 'NO'
        ];
        
        $insertSql = "INSERT INTO health_conditions_summary 
                      (student_id, condition_type, condition_value, remarks, requires_attention) 
                      VALUES (:student_id, :condition_type, :condition_value, :remarks, :requires_attention)";
        $insertStmt = $this->conn->prepare($insertSql);
        
        foreach ($conditions as $conditionType => $conditionValue) {
            $remarks = $data[$conditionType . 'Remarks'] ?? null;
            $requiresAttention = ($conditionValue === 'YES') ? 1 : 0;
            
            $insertStmt->execute([
                ':student_id' => $studentId,
                ':condition_type' => $conditionType,
                ':condition_value' => $conditionValue,
                ':remarks' => $remarks,
                ':requires_attention' => $requiresAttention
            ]);
        }
    }
    
    /**
     * Generate health alerts based on questionnaire data
     */
    private function generateHealthAlerts($studentId, $data) {
        // Clear existing alerts for this student
        $clearSql = "UPDATE health_alerts SET is_active = 0 WHERE student_id = :student_id";
        $clearStmt = $this->conn->prepare($clearSql);
        $clearStmt->execute([':student_id' => $studentId]);
        
        $alerts = [];
        
        // Critical health conditions
        if (($data['hasAsthma'] ?? 'NO') === 'YES') {
            $alerts[] = [
                'type' => 'Medical',
                'title' => 'Asthma Alert',
                'message' => 'Student has asthma. Ensure emergency inhaler is available.',
                'priority' => 'High'
            ];
        }
        
        // Allergy alerts
        $allergyTypes = ['hasAllergies', 'hasMedicines', 'hasVaccines', 'hasFoods'];
        foreach ($allergyTypes as $allergyType) {
            if (($data[$allergyType] ?? 'NO') === 'YES') {
                $alerts[] = [
                    'type' => 'Allergy',
                    'title' => 'Allergy Alert',
                    'message' => 'Student has reported allergies. Check details before any medical procedures.',
                    'priority' => 'Critical'
                ];
                break; // Only create one allergy alert
            }
        }
        
        // Chronic conditions
        $chronicConditions = ['hasDengue', 'hasAnemia', 'hasObesity'];
        foreach ($chronicConditions as $condition) {
            if (($data[$condition] ?? 'NO') === 'YES') {
                $conditionName = str_replace('has', '', $condition);
                $alerts[] = [
                    'type' => 'Medical',
                    'title' => ucfirst($conditionName) . ' History',
                    'message' => "Student has history of $conditionName. Monitor for related symptoms.",
                    'priority' => 'Medium'
                ];
            }
        }
        
        // Insert alerts
        if (!empty($alerts)) {
            $insertSql = "INSERT INTO health_alerts 
                          (student_id, alert_type, alert_title, alert_message, priority) 
                          VALUES (:student_id, :alert_type, :alert_title, :alert_message, :priority)";
            $insertStmt = $this->conn->prepare($insertSql);
            
            foreach ($alerts as $alert) {
                $insertStmt->execute([
                    ':student_id' => $studentId,
                    ':alert_type' => $alert['type'],
                    ':alert_title' => $alert['title'],
                    ':alert_message' => $alert['message'],
                    ':priority' => $alert['priority']
                ]);
            }
        }
    }
    
    /**
     * Update health statistics
     */
    private function updateHealthStatistics() {
        $today = date('Y-m-d');
        
        // Get statistics
        $totalSql = "SELECT COUNT(*) as total FROM health_questionnaires";
        $completedSql = "SELECT COUNT(*) as completed FROM health_questionnaires WHERE is_completed = 1";
        $pendingSql = "SELECT COUNT(*) as pending FROM health_questionnaires WHERE is_completed = 0";
        $highRiskSql = "SELECT COUNT(DISTINCT student_id) as high_risk FROM health_alerts WHERE is_active = 1 AND priority IN ('High', 'Critical')";
        
        $totalStmt = $this->conn->query($totalSql);
        $completedStmt = $this->conn->query($completedSql);
        $pendingStmt = $this->conn->query($pendingSql);
        $highRiskStmt = $this->conn->query($highRiskSql);
        
        $total = $totalStmt->fetchColumn();
        $completed = $completedStmt->fetchColumn();
        $pending = $pendingStmt->fetchColumn();
        $highRisk = $highRiskStmt->fetchColumn();
        
        // Update or insert statistics
        $upsertSql = "INSERT INTO health_statistics 
                      (stat_date, total_questionnaires, completed_questionnaires, pending_questionnaires, high_risk_students) 
                      VALUES (:stat_date, :total, :completed, :pending, :high_risk)
                      ON DUPLICATE KEY UPDATE
                      total_questionnaires = :total,
                      completed_questionnaires = :completed,
                      pending_questionnaires = :pending,
                      high_risk_students = :high_risk";
        
        $upsertStmt = $this->conn->prepare($upsertSql);
        $upsertStmt->execute([
            ':stat_date' => $today,
            ':total' => $total,
            ':completed' => $completed,
            ':pending' => $pending,
            ':high_risk' => $highRisk
        ]);
    }
    
    /**
     * Get health dashboard data
     */
    public function getHealthDashboardData() {
        $data = [];
        
        // Get latest statistics
        $statsSql = "SELECT * FROM health_statistics ORDER BY stat_date DESC LIMIT 1";
        $statsStmt = $this->conn->query($statsSql);
        $data['statistics'] = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get active alerts
        $alertsSql = "SELECT * FROM health_alerts WHERE is_active = 1 ORDER BY priority DESC, created_at DESC LIMIT 10";
        $alertsStmt = $this->conn->query($alertsSql);
        $data['recent_alerts'] = $alertsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get common conditions
        $conditionsSql = "SELECT condition_type, COUNT(*) as count 
                          FROM health_conditions_summary 
                          WHERE condition_value = 'YES' 
                          GROUP BY condition_type 
                          ORDER BY count DESC LIMIT 10";
        $conditionsStmt = $this->conn->query($conditionsSql);
        $data['common_conditions'] = $conditionsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    /**
     * Export health data to CSV
     */
    public function exportHealthData($filters = []) {
        $sql = "SELECT hq.*, 
                       CONCAT(si.Student_Fname, ' ', COALESCE(si.Student_Mname, ''), ' ', si.Student_Lname) as full_name,
                       si.section_name, si.program_name, si.department_name
                FROM health_questionnaires hq
                LEFT JOIN (
                    SELECT s.Student_ID, s.Student_Fname, s.Student_Mname, s.Student_Lname,
                           sec.section_name, p.program_name, d.department_name
                    FROM students s
                    LEFT JOIN sections sec ON s.section_id = sec.section_id
                    LEFT JOIN programs p ON s.program_id = p.program_id
                    LEFT JOIN departments d ON p.department_id = d.department_id
                ) si ON hq.student_id = si.Student_ID
                WHERE 1=1";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['department'])) {
            $sql .= " AND si.department_name = :department";
            $params[':department'] = $filters['department'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND hq.submission_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND hq.submission_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY hq.submission_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
