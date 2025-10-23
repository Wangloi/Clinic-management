<?php
/**
 * Health Analytics and Reporting System
 * Provides comprehensive analytics for health questionnaire data
 */

require_once 'connection.php';

class HealthAnalytics {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }
    
    /**
     * Get comprehensive health statistics
     */
    public function getHealthStatistics($filters = []) {
        $whereClause = "WHERE 1=1";
        $params = [];
        
        // Apply filters
        if (!empty($filters['department'])) {
            $whereClause .= " AND d.department_name = :department";
            $params[':department'] = $filters['department'];
        }
        
        if (!empty($filters['date_from'])) {
            $whereClause .= " AND hq.submission_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereClause .= " AND hq.submission_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        // Basic statistics
        $basicStats = $this->getBasicStatistics($whereClause, $params);
        
        // Health conditions analysis
        $conditionsAnalysis = $this->getHealthConditionsAnalysis($whereClause, $params);
        
        // Vaccination coverage
        $vaccinationCoverage = $this->getVaccinationCoverage($whereClause, $params);
        
        // Risk assessment
        $riskAssessment = $this->getRiskAssessment($whereClause, $params);
        
        // Demographic breakdown
        $demographics = $this->getDemographicBreakdown($whereClause, $params);
        
        return [
            'basic_stats' => $basicStats,
            'conditions_analysis' => $conditionsAnalysis,
            'vaccination_coverage' => $vaccinationCoverage,
            'risk_assessment' => $riskAssessment,
            'demographics' => $demographics,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Get basic health statistics
     */
    private function getBasicStatistics($whereClause, $params) {
        $sql = "SELECT 
                    COUNT(*) as total_questionnaires,
                    COUNT(CASE WHEN hq.is_completed = 1 THEN 1 END) as completed_questionnaires,
                    COUNT(CASE WHEN hq.is_completed = 0 THEN 1 END) as pending_questionnaires,
                    AVG(hq.student_age) as average_age,
                    COUNT(CASE WHEN hq.student_sex = 'Male' THEN 1 END) as male_students,
                    COUNT(CASE WHEN hq.student_sex = 'Female' THEN 1 END) as female_students,
                    AVG(hq.height) as average_height,
                    AVG(hq.weight) as average_weight,
                    AVG(hq.heart_rate) as average_heart_rate,
                    AVG(hq.temperature) as average_temperature
                FROM health_questionnaires hq
                LEFT JOIN students s ON hq.student_id = s.Student_ID
                LEFT JOIN sections sec ON s.section_id = sec.section_id
                LEFT JOIN programs p ON s.program_id = p.program_id
                LEFT JOIN departments d ON p.department_id = d.department_id
                $whereClause";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get health conditions analysis
     */
    private function getHealthConditionsAnalysis($whereClause, $params) {
        $conditions = [
            'has_allergies' => 'Allergies',
            'has_asthma' => 'Asthma',
            'has_health_problems' => 'Health Problems',
            'has_chickenpox' => 'Chickenpox',
            'has_dengue' => 'Dengue',
            'has_anemia' => 'Anemia',
            'has_gastritis' => 'Gastritis',
            'has_pneumonia' => 'Pneumonia',
            'has_obesity' => 'Obesity',
            'has_covid19' => 'COVID-19',
            'has_hospitalization' => 'Hospitalization History'
        ];
        
        $analysis = [];
        
        foreach ($conditions as $field => $label) {
            $sql = "SELECT 
                        COUNT(CASE WHEN hq.$field = 'YES' THEN 1 END) as yes_count,
                        COUNT(CASE WHEN hq.$field = 'NO' THEN 1 END) as no_count,
                        COUNT(*) as total_count
                    FROM health_questionnaires hq
                    LEFT JOIN students s ON hq.student_id = s.Student_ID
                    LEFT JOIN sections sec ON s.section_id = sec.section_id
                    LEFT JOIN programs p ON s.program_id = p.program_id
                    LEFT JOIN departments d ON p.department_id = d.department_id
                    $whereClause";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $analysis[$field] = [
                'label' => $label,
                'yes_count' => (int)$result['yes_count'],
                'no_count' => (int)$result['no_count'],
                'total_count' => (int)$result['total_count'],
                'percentage' => $result['total_count'] > 0 ? round(($result['yes_count'] / $result['total_count']) * 100, 2) : 0
            ];
        }
        
        return $analysis;
    }
    
    /**
     * Get vaccination coverage analysis
     */
    private function getVaccinationCoverage($whereClause, $params) {
        $vaccines = [
            'pneumonia_vaccine' => 'Pneumonia',
            'flu_vaccine' => 'Flu',
            'measles_vaccine' => 'Measles',
            'hep_b_vaccine' => 'Hepatitis B',
            'cervical_cancer_vaccine' => 'Cervical Cancer (HPV)',
            'covid_1st_dose' => 'COVID-19 1st Dose',
            'covid_2nd_dose' => 'COVID-19 2nd Dose',
            'covid_booster' => 'COVID-19 Booster'
        ];
        
        $coverage = [];
        
        foreach ($vaccines as $field => $label) {
            $sql = "SELECT 
                        COUNT(CASE WHEN hq.$field = 1 THEN 1 END) as vaccinated_count,
                        COUNT(*) as total_count
                    FROM health_questionnaires hq
                    LEFT JOIN students s ON hq.student_id = s.Student_ID
                    LEFT JOIN sections sec ON s.section_id = sec.section_id
                    LEFT JOIN programs p ON s.program_id = p.program_id
                    LEFT JOIN departments d ON p.department_id = d.department_id
                    $whereClause";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $coverage[$field] = [
                'label' => $label,
                'vaccinated_count' => (int)$result['vaccinated_count'],
                'total_count' => (int)$result['total_count'],
                'coverage_percentage' => $result['total_count'] > 0 ? round(($result['vaccinated_count'] / $result['total_count']) * 100, 2) : 0
            ];
        }
        
        return $coverage;
    }
    
    /**
     * Get risk assessment data
     */
    private function getRiskAssessment($whereClause, $params) {
        // High-risk conditions
        $highRiskSql = "SELECT 
                            COUNT(CASE WHEN (hq.has_asthma = 'YES' OR hq.has_allergies = 'YES' OR hq.has_anemia = 'YES' OR hq.has_obesity = 'YES') THEN 1 END) as high_risk_count,
                            COUNT(*) as total_count
                        FROM health_questionnaires hq
                        LEFT JOIN students s ON hq.student_id = s.Student_ID
                        LEFT JOIN sections sec ON s.section_id = sec.section_id
                        LEFT JOIN programs p ON s.program_id = p.program_id
                        LEFT JOIN departments d ON p.department_id = d.department_id
                        $whereClause";
        
        $stmt = $this->conn->prepare($highRiskSql);
        $stmt->execute($params);
        $riskData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // BMI analysis (if height and weight are available)
        $bmiSql = "SELECT 
                       COUNT(CASE WHEN (hq.weight / POWER(hq.height/100, 2)) < 18.5 THEN 1 END) as underweight_count,
                       COUNT(CASE WHEN (hq.weight / POWER(hq.height/100, 2)) BETWEEN 18.5 AND 24.9 THEN 1 END) as normal_weight_count,
                       COUNT(CASE WHEN (hq.weight / POWER(hq.height/100, 2)) BETWEEN 25 AND 29.9 THEN 1 END) as overweight_count,
                       COUNT(CASE WHEN (hq.weight / POWER(hq.height/100, 2)) >= 30 THEN 1 END) as obese_count,
                       COUNT(CASE WHEN hq.height IS NOT NULL AND hq.weight IS NOT NULL THEN 1 END) as bmi_available_count
                   FROM health_questionnaires hq
                   LEFT JOIN students s ON hq.student_id = s.Student_ID
                   LEFT JOIN sections sec ON s.section_id = sec.section_id
                   LEFT JOIN programs p ON s.program_id = p.program_id
                   LEFT JOIN departments d ON p.department_id = d.department_id
                   $whereClause";
        
        $stmt = $this->conn->prepare($bmiSql);
        $stmt->execute($params);
        $bmiData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'high_risk' => [
                'count' => (int)$riskData['high_risk_count'],
                'total' => (int)$riskData['total_count'],
                'percentage' => $riskData['total_count'] > 0 ? round(($riskData['high_risk_count'] / $riskData['total_count']) * 100, 2) : 0
            ],
            'bmi_distribution' => [
                'underweight' => (int)$bmiData['underweight_count'],
                'normal_weight' => (int)$bmiData['normal_weight_count'],
                'overweight' => (int)$bmiData['overweight_count'],
                'obese' => (int)$bmiData['obese_count'],
                'data_available' => (int)$bmiData['bmi_available_count']
            ]
        ];
    }
    
    /**
     * Get demographic breakdown
     */
    private function getDemographicBreakdown($whereClause, $params) {
        // Age groups
        $ageGroupsSql = "SELECT 
                             COUNT(CASE WHEN hq.student_age BETWEEN 5 AND 12 THEN 1 END) as elementary_age,
                             COUNT(CASE WHEN hq.student_age BETWEEN 13 AND 15 THEN 1 END) as junior_high_age,
                             COUNT(CASE WHEN hq.student_age BETWEEN 16 AND 18 THEN 1 END) as senior_high_age,
                             COUNT(CASE WHEN hq.student_age BETWEEN 19 AND 25 THEN 1 END) as college_age,
                             COUNT(CASE WHEN hq.student_age > 25 THEN 1 END) as adult_age
                         FROM health_questionnaires hq
                         LEFT JOIN students s ON hq.student_id = s.Student_ID
                         LEFT JOIN sections sec ON s.section_id = sec.section_id
                         LEFT JOIN programs p ON s.program_id = p.program_id
                         LEFT JOIN departments d ON p.department_id = d.department_id
                         $whereClause";
        
        $stmt = $this->conn->prepare($ageGroupsSql);
        $stmt->execute($params);
        $ageGroups = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Department breakdown
        $departmentSql = "SELECT 
                              d.department_name,
                              COUNT(*) as student_count
                          FROM health_questionnaires hq
                          LEFT JOIN students s ON hq.student_id = s.Student_ID
                          LEFT JOIN sections sec ON s.section_id = sec.section_id
                          LEFT JOIN programs p ON s.program_id = p.program_id
                          LEFT JOIN departments d ON p.department_id = d.department_id
                          $whereClause
                          GROUP BY d.department_name
                          ORDER BY student_count DESC";
        
        $stmt = $this->conn->prepare($departmentSql);
        $stmt->execute($params);
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'age_groups' => $ageGroups,
            'departments' => $departments
        ];
    }
    
    /**
     * Generate health report for specific student
     */
    public function generateStudentHealthReport($studentId) {
        // Get student health record
        $healthSql = "SELECT hq.*, 
                             CONCAT(s.Student_Fname, ' ', COALESCE(s.Student_Mname, ''), ' ', s.Student_Lname) as full_name,
                             sec.section_name, p.program_name, d.department_name
                      FROM health_questionnaires hq
                      LEFT JOIN students s ON hq.student_id = s.Student_ID
                      LEFT JOIN sections sec ON s.section_id = sec.section_id
                      LEFT JOIN programs p ON s.program_id = p.program_id
                      LEFT JOIN departments d ON p.department_id = d.department_id
                      WHERE hq.student_id = :student_id";
        
        $stmt = $this->conn->prepare($healthSql);
        $stmt->execute([':student_id' => $studentId]);
        $healthRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$healthRecord) {
            return null;
        }
        
        // Get health alerts for this student
        $alertsSql = "SELECT * FROM health_alerts WHERE student_id = :student_id AND is_active = 1 ORDER BY priority DESC";
        $stmt = $this->conn->prepare($alertsSql);
        $stmt->execute([':student_id' => $studentId]);
        $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get health conditions summary
        $conditionsSql = "SELECT * FROM health_conditions_summary WHERE student_id = :student_id AND condition_value = 'YES'";
        $stmt = $this->conn->prepare($conditionsSql);
        $stmt->execute([':student_id' => $studentId]);
        $conditions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate BMI if height and weight are available
        $bmi = null;
        $bmiCategory = null;
        if ($healthRecord['height'] && $healthRecord['weight']) {
            $heightInMeters = $healthRecord['height'] / 100;
            $bmi = round($healthRecord['weight'] / ($heightInMeters * $heightInMeters), 2);
            
            if ($bmi < 18.5) {
                $bmiCategory = 'Underweight';
            } elseif ($bmi < 25) {
                $bmiCategory = 'Normal weight';
            } elseif ($bmi < 30) {
                $bmiCategory = 'Overweight';
            } else {
                $bmiCategory = 'Obese';
            }
        }
        
        return [
            'student_info' => [
                'student_id' => $studentId,
                'full_name' => $healthRecord['full_name'],
                'section' => $healthRecord['section_name'],
                'program' => $healthRecord['program_name'],
                'department' => $healthRecord['department_name']
            ],
            'health_record' => $healthRecord,
            'health_alerts' => $alerts,
            'health_conditions' => $conditions,
            'health_metrics' => [
                'bmi' => $bmi,
                'bmi_category' => $bmiCategory,
                'vital_signs' => [
                    'height' => $healthRecord['height'],
                    'weight' => $healthRecord['weight'],
                    'blood_pressure' => $healthRecord['blood_pressure'],
                    'heart_rate' => $healthRecord['heart_rate'],
                    'temperature' => $healthRecord['temperature']
                ]
            ],
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Get trending health data
     */
    public function getHealthTrends($period = '30_days') {
        $dateFilter = '';
        switch ($period) {
            case '7_days':
                $dateFilter = "AND hq.submission_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case '30_days':
                $dateFilter = "AND hq.submission_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case '90_days':
                $dateFilter = "AND hq.submission_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
                break;
            case '1_year':
                $dateFilter = "AND hq.submission_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
        }
        
        // Daily submission trends
        $trendsSql = "SELECT 
                          DATE(hq.submission_date) as submission_date,
                          COUNT(*) as submissions_count,
                          COUNT(CASE WHEN hq.is_completed = 1 THEN 1 END) as completed_count
                      FROM health_questionnaires hq
                      WHERE 1=1 $dateFilter
                      GROUP BY DATE(hq.submission_date)
                      ORDER BY submission_date DESC";
        
        $stmt = $this->conn->query($trendsSql);
        $trends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Common conditions trends
        $conditionsTrendsSql = "SELECT 
                                    condition_type,
                                    COUNT(*) as condition_count
                                FROM health_conditions_summary hcs
                                JOIN health_questionnaires hq ON hcs.student_id = hq.student_id
                                WHERE hcs.condition_value = 'YES' $dateFilter
                                GROUP BY condition_type
                                ORDER BY condition_count DESC
                                LIMIT 10";
        
        $stmt = $this->conn->query($conditionsTrendsSql);
        $conditionsTrends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'submission_trends' => $trends,
            'conditions_trends' => $conditionsTrends,
            'period' => $period,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
}
?>
