<?php
/**
 * Health Data Export System
 * Handles exporting health questionnaire data in various formats
 */

require_once 'connection.php';
require_once 'health-backend-handler.php';
require_once 'health-analytics.php';

class HealthDataExport {
    private $conn;
    private $healthBackend;
    private $analytics;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
        $this->healthBackend = new HealthBackendHandler();
        $this->analytics = new HealthAnalytics();
    }
    
    /**
     * Export health data to CSV format
     */
    public function exportToCSV($filters = [], $includePersonalInfo = true) {
        $data = $this->healthBackend->exportHealthData($filters);
        
        if (empty($data)) {
            return false;
        }
        
        // Generate filename
        $filename = 'health_questionnaire_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = '../exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        if (!file_exists('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // CSV Headers
        $headers = $this->getCSVHeaders($includePersonalInfo);
        fputcsv($file, $headers);
        
        // CSV Data
        foreach ($data as $record) {
            $row = $this->formatRecordForCSV($record, $includePersonalInfo);
            fputcsv($file, $row);
        }
        
        fclose($file);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'record_count' => count($data),
            'file_size' => filesize($filepath)
        ];
    }
    
    /**
     * Export health statistics to JSON
     */
    public function exportStatisticsToJSON($filters = []) {
        $statistics = $this->analytics->getHealthStatistics($filters);
        
        $filename = 'health_statistics_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = '../exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        if (!file_exists('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $jsonData = json_encode($statistics, JSON_PRETTY_PRINT);
        file_put_contents($filepath, $jsonData);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'file_size' => filesize($filepath)
        ];
    }
    
    /**
     * Generate comprehensive health report in HTML format
     */
    public function generateHealthReport($filters = []) {
        $statistics = $this->analytics->getHealthStatistics($filters);
        $trends = $this->analytics->getHealthTrends('30_days');
        
        $filename = 'health_report_' . date('Y-m-d_H-i-s') . '.html';
        $filepath = '../exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        if (!file_exists('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $html = $this->generateHTMLReport($statistics, $trends, $filters);
        file_put_contents($filepath, $html);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'file_size' => filesize($filepath)
        ];
    }
    
    /**
     * Export individual student report
     */
    public function exportStudentReport($studentId, $format = 'pdf') {
        $report = $this->analytics->generateStudentHealthReport($studentId);
        
        if (!$report) {
            return [
                'success' => false,
                'message' => 'Student health record not found'
            ];
        }
        
        $filename = 'student_health_report_' . $studentId . '_' . date('Y-m-d_H-i-s');
        
        switch ($format) {
            case 'json':
                return $this->exportStudentReportJSON($report, $filename);
            case 'html':
                return $this->exportStudentReportHTML($report, $filename);
            case 'csv':
                return $this->exportStudentReportCSV($report, $filename);
            default:
                return [
                    'success' => false,
                    'message' => 'Unsupported export format'
                ];
        }
    }
    
    /**
     * Get CSV headers
     */
    private function getCSVHeaders($includePersonalInfo = true) {
        $headers = [];
        
        if ($includePersonalInfo) {
            $headers = array_merge($headers, [
                'Student ID',
                'Full Name',
                'Section',
                'Program',
                'Department'
            ]);
        }
        
        $headers = array_merge($headers, [
            'Sex',
            'Age',
            'Birthday',
            'Home Address',
            'Contact Number',
            'Height (cm)',
            'Weight (kg)',
            'Blood Pressure',
            'Heart Rate (bpm)',
            'Respiratory Rate',
            'Temperature (°C)',
            'Has Allergies',
            'Allergies Remarks',
            'Has Medicine Allergies',
            'Medicine Allergies',
            'Has Vaccine Allergies',
            'Vaccine Allergies',
            'Has Food Allergies',
            'Food Allergies',
            'Has Other Allergies',
            'Other Allergies',
            'Has Asthma',
            'Asthma Remarks',
            'Has Health Problems',
            'Health Problems Remarks',
            'Has Ear Infections',
            'Ear Infections Remarks',
            'Has Potty Problems',
            'Potty Problems Remarks',
            'Has UTI',
            'UTI Remarks',
            'Has Chickenpox',
            'Chickenpox Remarks',
            'Has Dengue',
            'Dengue Remarks',
            'Has Anemia',
            'Anemia Remarks',
            'Has Gastritis',
            'Gastritis Remarks',
            'Has Pneumonia',
            'Pneumonia Remarks',
            'Has Obesity',
            'Obesity Remarks',
            'Has COVID-19',
            'COVID-19 Remarks',
            'Has Other Conditions',
            'Other Conditions Remarks',
            'Has Hospitalization',
            'Hospitalization Date',
            'Hospital Name',
            'Hospitalization Remarks',
            'Pneumonia Vaccine',
            'Flu Vaccine',
            'Measles Vaccine',
            'Hepatitis B Vaccine',
            'Cervical Cancer Vaccine',
            'COVID-19 1st Dose',
            'COVID-19 2nd Dose',
            'COVID-19 Booster',
            'Other Vaccines',
            'Other Vaccines Text',
            'Menarche Age',
            'Menstrual Days',
            'Pads Consumed',
            'Menstrual Problems',
            'Present Concerns',
            'Current Medications/Vitamins',
            'Additional Notes',
            'Education Level',
            'Is Completed',
            'Completion Step',
            'Submission Date',
            'Last Updated'
        ]);
        
        return $headers;
    }
    
    /**
     * Format record for CSV export
     */
    private function formatRecordForCSV($record, $includePersonalInfo = true) {
        $row = [];
        
        if ($includePersonalInfo) {
            $row = array_merge($row, [
                $record['student_id'] ?? '',
                $record['full_name'] ?? '',
                $record['section_name'] ?? '',
                $record['program_name'] ?? '',
                $record['department_name'] ?? ''
            ]);
        }
        
        $row = array_merge($row, [
            $record['student_sex'] ?? '',
            $record['student_age'] ?? '',
            $record['student_birthday'] ?? '',
            $record['home_address'] ?? '',
            $record['contact_number'] ?? '',
            $record['height'] ?? '',
            $record['weight'] ?? '',
            $record['blood_pressure'] ?? '',
            $record['heart_rate'] ?? '',
            $record['respiratory_rate'] ?? '',
            $record['temperature'] ?? '',
            $record['has_allergies'] ?? '',
            $record['allergies_remarks'] ?? '',
            $record['has_medicine_allergies'] ?? '',
            $record['medicine_allergies'] ?? '',
            $record['has_vaccine_allergies'] ?? '',
            $record['vaccine_allergies'] ?? '',
            $record['has_food_allergies'] ?? '',
            $record['food_allergies'] ?? '',
            $record['has_other_allergies'] ?? '',
            $record['other_allergies'] ?? '',
            $record['has_asthma'] ?? '',
            $record['asthma_remarks'] ?? '',
            $record['has_health_problems'] ?? '',
            $record['health_problems_remarks'] ?? '',
            $record['has_ear_infections'] ?? '',
            $record['ear_infections_remarks'] ?? '',
            $record['has_potty_problems'] ?? '',
            $record['potty_problems_remarks'] ?? '',
            $record['has_uti'] ?? '',
            $record['uti_remarks'] ?? '',
            $record['has_chickenpox'] ?? '',
            $record['chickenpox_remarks'] ?? '',
            $record['has_dengue'] ?? '',
            $record['dengue_remarks'] ?? '',
            $record['has_anemia'] ?? '',
            $record['anemia_remarks'] ?? '',
            $record['has_gastritis'] ?? '',
            $record['gastritis_remarks'] ?? '',
            $record['has_pneumonia'] ?? '',
            $record['pneumonia_remarks'] ?? '',
            $record['has_obesity'] ?? '',
            $record['obesity_remarks'] ?? '',
            $record['has_covid19'] ?? '',
            $record['covid19_remarks'] ?? '',
            $record['has_other_conditions'] ?? '',
            $record['other_conditions_remarks'] ?? '',
            $record['has_hospitalization'] ?? '',
            $record['hospitalization_date'] ?? '',
            $record['hospital_name'] ?? '',
            $record['hospitalization_remarks'] ?? '',
            $record['pneumonia_vaccine'] ? 'Yes' : 'No',
            $record['flu_vaccine'] ? 'Yes' : 'No',
            $record['measles_vaccine'] ? 'Yes' : 'No',
            $record['hep_b_vaccine'] ? 'Yes' : 'No',
            $record['cervical_cancer_vaccine'] ? 'Yes' : 'No',
            $record['covid_1st_dose'] ? 'Yes' : 'No',
            $record['covid_2nd_dose'] ? 'Yes' : 'No',
            $record['covid_booster'] ? 'Yes' : 'No',
            $record['other_vaccines'] ? 'Yes' : 'No',
            $record['other_vaccines_text'] ?? '',
            $record['menarche_age'] ?? '',
            $record['menstrual_days'] ?? '',
            $record['pads_consumed'] ?? '',
            $record['menstrual_problems'] ?? '',
            $record['present_concerns'] ?? '',
            $record['current_medications_vitamins'] ?? '',
            $record['additional_notes'] ?? '',
            $record['education_level'] ?? '',
            $record['is_completed'] ? 'Yes' : 'No',
            $record['completion_step'] ?? '',
            $record['submission_date'] ?? '',
            $record['updated_at'] ?? ''
        ]);
        
        return $row;
    }
    
    /**
     * Generate HTML report
     */
    private function generateHTMLReport($statistics, $trends, $filters) {
        $filterText = '';
        if (!empty($filters)) {
            $filterText = '<p><strong>Filters Applied:</strong> ' . implode(', ', array_map(function($k, $v) {
                return ucfirst($k) . ': ' . $v;
            }, array_keys($filters), $filters)) . '</p>';
        }
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Questionnaire Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .stat-card { background: #f5f5f5; padding: 15px; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .stat-label { font-size: 14px; color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .chart { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Health Questionnaire Report</h1>
        <p>Generated on: ' . date('F j, Y \a\t g:i A') . '</p>
        ' . $filterText . '
    </div>
    
    <div class="section">
        <h2>Basic Statistics</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">' . ($statistics['basic_stats']['total_questionnaires'] ?? 0) . '</div>
                <div class="stat-label">Total Questionnaires</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . ($statistics['basic_stats']['completed_questionnaires'] ?? 0) . '</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . ($statistics['basic_stats']['pending_questionnaires'] ?? 0) . '</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . round($statistics['basic_stats']['average_age'] ?? 0, 1) . '</div>
                <div class="stat-label">Average Age</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . ($statistics['basic_stats']['male_students'] ?? 0) . '</div>
                <div class="stat-label">Male Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . ($statistics['basic_stats']['female_students'] ?? 0) . '</div>
                <div class="stat-label">Female Students</div>
            </div>
        </div>
    </div>';
        
        // Health Conditions Analysis
        $html .= '<div class="section">
        <h2>Health Conditions Analysis</h2>
        <table>
            <thead>
                <tr>
                    <th>Condition</th>
                    <th>Yes Count</th>
                    <th>No Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($statistics['conditions_analysis'] as $condition) {
            $html .= '<tr>
                <td>' . $condition['label'] . '</td>
                <td>' . $condition['yes_count'] . '</td>
                <td>' . $condition['no_count'] . '</td>
                <td>' . $condition['percentage'] . '%</td>
            </tr>';
        }
        
        $html .= '</tbody>
        </table>
    </div>';
        
        // Vaccination Coverage
        $html .= '<div class="section">
        <h2>Vaccination Coverage</h2>
        <table>
            <thead>
                <tr>
                    <th>Vaccine</th>
                    <th>Vaccinated Count</th>
                    <th>Total Count</th>
                    <th>Coverage %</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($statistics['vaccination_coverage'] as $vaccine) {
            $html .= '<tr>
                <td>' . $vaccine['label'] . '</td>
                <td>' . $vaccine['vaccinated_count'] . '</td>
                <td>' . $vaccine['total_count'] . '</td>
                <td>' . $vaccine['coverage_percentage'] . '%</td>
            </tr>';
        }
        
        $html .= '</tbody>
        </table>
    </div>
    
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Export student report as JSON
     */
    private function exportStudentReportJSON($report, $filename) {
        $filepath = '../exports/' . $filename . '.json';
        
        if (!file_exists('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $jsonData = json_encode($report, JSON_PRETTY_PRINT);
        file_put_contents($filepath, $jsonData);
        
        return [
            'success' => true,
            'filename' => $filename . '.json',
            'filepath' => $filepath,
            'file_size' => filesize($filepath)
        ];
    }
    
    /**
     * Export student report as HTML
     */
    private function exportStudentReportHTML($report, $filename) {
        $filepath = '../exports/' . $filename . '.html';
        
        if (!file_exists('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $html = $this->generateStudentHTMLReport($report);
        file_put_contents($filepath, $html);
        
        return [
            'success' => true,
            'filename' => $filename . '.html',
            'filepath' => $filepath,
            'file_size' => filesize($filepath)
        ];
    }
    
    /**
     * Export student report as CSV
     */
    private function exportStudentReportCSV($report, $filename) {
        $filepath = '../exports/' . $filename . '.csv';
        
        if (!file_exists('../exports/')) {
            mkdir('../exports/', 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Student info
        fputcsv($file, ['Student Information']);
        fputcsv($file, ['Student ID', $report['student_info']['student_id']]);
        fputcsv($file, ['Full Name', $report['student_info']['full_name']]);
        fputcsv($file, ['Section', $report['student_info']['section']]);
        fputcsv($file, ['Program', $report['student_info']['program']]);
        fputcsv($file, ['Department', $report['student_info']['department']]);
        fputcsv($file, []);
        
        // Health metrics
        if ($report['health_metrics']['bmi']) {
            fputcsv($file, ['Health Metrics']);
            fputcsv($file, ['BMI', $report['health_metrics']['bmi']]);
            fputcsv($file, ['BMI Category', $report['health_metrics']['bmi_category']]);
            fputcsv($file, []);
        }
        
        // Health conditions
        if (!empty($report['health_conditions'])) {
            fputcsv($file, ['Health Conditions']);
            fputcsv($file, ['Condition Type', 'Value', 'Remarks']);
            foreach ($report['health_conditions'] as $condition) {
                fputcsv($file, [$condition['condition_type'], $condition['condition_value'], $condition['remarks']]);
            }
            fputcsv($file, []);
        }
        
        // Health alerts
        if (!empty($report['health_alerts'])) {
            fputcsv($file, ['Health Alerts']);
            fputcsv($file, ['Alert Type', 'Title', 'Message', 'Priority']);
            foreach ($report['health_alerts'] as $alert) {
                fputcsv($file, [$alert['alert_type'], $alert['alert_title'], $alert['alert_message'], $alert['priority']]);
            }
        }
        
        fclose($file);
        
        return [
            'success' => true,
            'filename' => $filename . '.csv',
            'filepath' => $filepath,
            'file_size' => filesize($filepath)
        ];
    }
    
    /**
     * Generate student HTML report
     */
    private function generateStudentHTMLReport($report) {
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Health Report - ' . htmlspecialchars($report['student_info']['full_name']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3498db; padding-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .info-card { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #3498db; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .alert-high { background: #f8d7da; border-left: 4px solid #dc3545; }
        .alert-medium { background: #fff3cd; border-left: 4px solid #ffc107; }
        .alert-low { background: #d1ecf1; border-left: 4px solid #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Health Report</h1>
        <h2>' . htmlspecialchars($report['student_info']['full_name']) . '</h2>
        <p>Student ID: ' . htmlspecialchars($report['student_info']['student_id']) . '</p>
        <p>Generated on: ' . date('F j, Y \a\t g:i A') . '</p>
    </div>';
        
        // Student Information
        $html .= '<div class="section">
        <h3>Student Information</h3>
        <div class="info-grid">
            <div class="info-card">
                <strong>Section:</strong><br>' . htmlspecialchars($report['student_info']['section']) . '
            </div>
            <div class="info-card">
                <strong>Program:</strong><br>' . htmlspecialchars($report['student_info']['program']) . '
            </div>
            <div class="info-card">
                <strong>Department:</strong><br>' . htmlspecialchars($report['student_info']['department']) . '
            </div>
        </div>
    </div>';
        
        // Health Metrics
        if ($report['health_metrics']['bmi']) {
            $html .= '<div class="section">
            <h3>Health Metrics</h3>
            <div class="info-grid">
                <div class="info-card">
                    <strong>BMI:</strong><br>' . $report['health_metrics']['bmi'] . '
                </div>
                <div class="info-card">
                    <strong>BMI Category:</strong><br>' . $report['health_metrics']['bmi_category'] . '
                </div>
            </div>
        </div>';
        }
        
        // Health Alerts
        if (!empty($report['health_alerts'])) {
            $html .= '<div class="section">
            <h3>Health Alerts</h3>';
            
            foreach ($report['health_alerts'] as $alert) {
                $alertClass = 'alert-low';
                if ($alert['priority'] === 'High' || $alert['priority'] === 'Critical') {
                    $alertClass = 'alert-high';
                } elseif ($alert['priority'] === 'Medium') {
                    $alertClass = 'alert-medium';
                }
                
                $html .= '<div class="alert ' . $alertClass . '">
                    <strong>' . htmlspecialchars($alert['alert_title']) . '</strong> (' . htmlspecialchars($alert['priority']) . ')<br>
                    ' . htmlspecialchars($alert['alert_message']) . '
                </div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</body>
</html>';
        
        return $html;
    }
}
?>
