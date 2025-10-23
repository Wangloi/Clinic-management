<?php
/**
 * Health Questionnaire Backend Installation Script
 * Sets up the database tables and initial configuration
 */

require_once 'connection.php';

class HealthBackendInstaller {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }
    
    /**
     * Install all required tables and configurations
     */
    public function install() {
        try {
            echo "<h2>Installing Health Questionnaire Backend...</h2>";
            
            // Create tables
            $this->createHealthQuestionnairesTable();
            $this->createHealthConditionsSummaryTable();
            $this->createHealthAlertsTable();
            $this->createHealthStatisticsTable();
            
            // Create exports directory
            $this->createExportsDirectory();
            
            // Insert sample data (optional)
            $this->insertSampleData();
            
            echo "<p style='color: green;'><strong>✓ Installation completed successfully!</strong></p>";
            echo "<h3>Next Steps:</h3>";
            echo "<ul>";
            echo "<li>Update your student dashboard to use the new backend API</li>";
            echo "<li>Test the health questionnaire functionality</li>";
            echo "<li>Configure any additional settings as needed</li>";
            echo "</ul>";
            
            return true;
            
        } catch (Exception $e) {
            echo "<p style='color: red;'><strong>✗ Installation failed:</strong> " . $e->getMessage() . "</p>";
            return false;
        }
    }
    
    /**
     * Create health_questionnaires table
     */
    private function createHealthQuestionnairesTable() {
        echo "<p>Creating health_questionnaires table...</p>";
        
        $sql = "CREATE TABLE IF NOT EXISTS `health_questionnaires` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `student_id` varchar(50) NOT NULL,
            `submission_date` timestamp DEFAULT CURRENT_TIMESTAMP,
            `last_updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `is_completed` tinyint(1) DEFAULT 0,
            `completion_step` int(11) DEFAULT 1,
            
            -- Personal Information (Step 1)
            `student_sex` enum('Male','Female') DEFAULT NULL,
            `student_birthday` date DEFAULT NULL,
            `student_age` int(11) DEFAULT NULL,
            `home_address` text,
            `contact_number` varchar(20) DEFAULT NULL,
            
            -- Vital Signs & Physical Data (Step 1)
            `height` decimal(5,2) DEFAULT NULL COMMENT 'Height in cm',
            `weight` decimal(5,2) DEFAULT NULL COMMENT 'Weight in kg',
            `blood_pressure` varchar(20) DEFAULT NULL,
            `heart_rate` int(11) DEFAULT NULL COMMENT 'Heart rate in bpm',
            `respiratory_rate` int(11) DEFAULT NULL COMMENT 'Respiratory rate per minute',
            `temperature` decimal(4,1) DEFAULT NULL COMMENT 'Temperature in Celsius',
            
            -- Health History General (Step 2)
            `has_allergies` enum('YES','NO') DEFAULT 'NO',
            `allergies_remarks` text,
            `has_medicine_allergies` enum('YES','NO') DEFAULT 'NO',
            `medicine_allergies` text,
            `has_vaccine_allergies` enum('YES','NO') DEFAULT 'NO',
            `vaccine_allergies` text,
            `has_food_allergies` enum('YES','NO') DEFAULT 'NO',
            `food_allergies` text,
            `has_other_allergies` enum('YES','NO') DEFAULT 'NO',
            `other_allergies` text,
            `has_asthma` enum('YES','NO') DEFAULT 'NO',
            `asthma_remarks` text,
            `has_health_problems` enum('YES','NO') DEFAULT 'NO',
            `health_problems_remarks` text,
            `has_ear_infections` enum('YES','NO') DEFAULT 'NO',
            `ear_infections_remarks` text,
            `has_potty_problems` enum('YES','NO') DEFAULT 'NO',
            `potty_problems_remarks` text,
            `has_uti` enum('YES','NO') DEFAULT 'NO',
            `uti_remarks` text,
            `has_chickenpox` enum('YES','NO') DEFAULT 'NO',
            `chickenpox_remarks` text,
            `has_dengue` enum('YES','NO') DEFAULT 'NO',
            `dengue_remarks` text,
            `has_anemia` enum('YES','NO') DEFAULT 'NO',
            `anemia_remarks` text,
            `has_gastritis` enum('YES','NO') DEFAULT 'NO',
            `gastritis_remarks` text,
            `has_pneumonia` enum('YES','NO') DEFAULT 'NO',
            `pneumonia_remarks` text,
            `has_obesity` enum('YES','NO') DEFAULT 'NO',
            `obesity_remarks` text,
            `has_covid19` enum('YES','NO') DEFAULT 'NO',
            `covid19_remarks` text,
            `has_other_conditions` enum('YES','NO') DEFAULT 'NO',
            `other_conditions_remarks` text,
            
            -- Hospitalization History (Step 3)
            `has_hospitalization` enum('YES','NO') DEFAULT 'NO',
            `hospitalization_date` date DEFAULT NULL,
            `hospital_name` varchar(255) DEFAULT NULL,
            `hospitalization_remarks` text,
            
            -- Immunization & Health Information (Step 4)
            `pneumonia_vaccine` tinyint(1) DEFAULT 0,
            `flu_vaccine` tinyint(1) DEFAULT 0,
            `measles_vaccine` tinyint(1) DEFAULT 0,
            `hep_b_vaccine` tinyint(1) DEFAULT 0,
            `cervical_cancer_vaccine` tinyint(1) DEFAULT 0,
            `covid_1st_dose` tinyint(1) DEFAULT 0,
            `covid_2nd_dose` tinyint(1) DEFAULT 0,
            `covid_booster` tinyint(1) DEFAULT 0,
            `other_vaccines` tinyint(1) DEFAULT 0,
            `other_vaccines_text` text,
            
            -- Menstruation Information (Female Only)
            `menarche_age` int(11) DEFAULT NULL,
            `menstrual_days` int(11) DEFAULT NULL,
            `pads_consumed` varchar(20) DEFAULT NULL,
            `menstrual_problems` text,
            
            -- Current Health Concerns (Step 5)
            `present_concerns` text,
            `current_medications_vitamins` text,
            `additional_notes` text,
            
            -- College-specific fields (Step 6 for college students)
            `current_medications` text,
            `lifestyle_habits` text,
            `academic_stress` text,
            `current_symptoms` text,
            `allergies_all` text,
            `chronic_conditions` text,
            `family_history` text,
            `previous_hospitalizations` text,
            `mental_health_history` text,
            `stress_levels` varchar(50) DEFAULT NULL,
            `support_system` text,
            `wellness_goals` text,
            
            -- Metadata
            `education_level` enum('basic','college') DEFAULT 'basic',
            `submitted_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_student_questionnaire` (`student_id`),
            KEY `idx_student_id` (`student_id`),
            KEY `idx_submission_date` (`submission_date`),
            KEY `idx_education_level` (`education_level`),
            KEY `idx_completed` (`is_completed`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->conn->exec($sql);
        echo "<p style='color: green;'>✓ health_questionnaires table created</p>";
    }
    
    /**
     * Create health_conditions_summary table
     */
    private function createHealthConditionsSummaryTable() {
        echo "<p>Creating health_conditions_summary table...</p>";
        
        $sql = "CREATE TABLE IF NOT EXISTS `health_conditions_summary` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `student_id` varchar(50) NOT NULL,
            `condition_type` varchar(100) NOT NULL,
            `condition_value` enum('YES','NO') DEFAULT 'NO',
            `remarks` text,
            `severity` enum('Low','Medium','High') DEFAULT 'Low',
            `requires_attention` tinyint(1) DEFAULT 0,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            PRIMARY KEY (`id`),
            KEY `idx_student_condition` (`student_id`, `condition_type`),
            KEY `idx_condition_type` (`condition_type`),
            KEY `idx_requires_attention` (`requires_attention`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->conn->exec($sql);
        echo "<p style='color: green;'>✓ health_conditions_summary table created</p>";
    }
    
    /**
     * Create health_alerts table
     */
    private function createHealthAlertsTable() {
        echo "<p>Creating health_alerts table...</p>";
        
        $sql = "CREATE TABLE IF NOT EXISTS `health_alerts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `student_id` varchar(50) NOT NULL,
            `alert_type` enum('Medical','Allergy','Emergency','Follow-up') DEFAULT 'Medical',
            `alert_title` varchar(255) NOT NULL,
            `alert_message` text NOT NULL,
            `priority` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
            `is_active` tinyint(1) DEFAULT 1,
            `acknowledged_by` varchar(100) DEFAULT NULL,
            `acknowledged_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `expires_at` timestamp NULL DEFAULT NULL,
            
            PRIMARY KEY (`id`),
            KEY `idx_student_alerts` (`student_id`),
            KEY `idx_alert_type` (`alert_type`),
            KEY `idx_priority` (`priority`),
            KEY `idx_active_alerts` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->conn->exec($sql);
        echo "<p style='color: green;'>✓ health_alerts table created</p>";
    }
    
    /**
     * Create health_statistics table
     */
    private function createHealthStatisticsTable() {
        echo "<p>Creating health_statistics table...</p>";
        
        $sql = "CREATE TABLE IF NOT EXISTS `health_statistics` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `stat_date` date NOT NULL,
            `total_questionnaires` int(11) DEFAULT 0,
            `completed_questionnaires` int(11) DEFAULT 0,
            `pending_questionnaires` int(11) DEFAULT 0,
            `high_risk_students` int(11) DEFAULT 0,
            `common_conditions` json DEFAULT NULL,
            `vaccination_rates` json DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_stat_date` (`stat_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->conn->exec($sql);
        echo "<p style='color: green;'>✓ health_statistics table created</p>";
    }
    
    /**
     * Create exports directory
     */
    private function createExportsDirectory() {
        echo "<p>Creating exports directory...</p>";
        
        $exportDir = '../exports/';
        if (!file_exists($exportDir)) {
            if (mkdir($exportDir, 0755, true)) {
                echo "<p style='color: green;'>✓ Exports directory created</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Could not create exports directory. Please create it manually.</p>";
            }
        } else {
            echo "<p style='color: green;'>✓ Exports directory already exists</p>";
        }
        
        // Create .htaccess to protect exports directory
        $htaccessContent = "Order Deny,Allow\nDeny from all\nAllow from 127.0.0.1\nAllow from ::1";
        file_put_contents($exportDir . '.htaccess', $htaccessContent);
    }
    
    /**
     * Insert sample data for testing
     */
    private function insertSampleData() {
        echo "<p>Inserting sample statistics data...</p>";
        
        $today = date('Y-m-d');
        $sql = "INSERT IGNORE INTO health_statistics 
                (stat_date, total_questionnaires, completed_questionnaires, pending_questionnaires, high_risk_students) 
                VALUES (:stat_date, 0, 0, 0, 0)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':stat_date' => $today]);
        
        echo "<p style='color: green;'>✓ Sample data inserted</p>";
    }
    
    /**
     * Check system requirements
     */
    public function checkRequirements() {
        echo "<h2>Checking System Requirements...</h2>";
        
        $requirements = [
            'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'JSON Extension' => extension_loaded('json'),
            'Database Connection' => $this->testDatabaseConnection()
        ];
        
        $allPassed = true;
        
        foreach ($requirements as $requirement => $passed) {
            $status = $passed ? "<span style='color: green;'>✓ PASS</span>" : "<span style='color: red;'>✗ FAIL</span>";
            echo "<p>$requirement: $status</p>";
            
            if (!$passed) {
                $allPassed = false;
            }
        }
        
        if ($allPassed) {
            echo "<p style='color: green;'><strong>✓ All requirements met!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>✗ Some requirements not met. Please fix these issues before installing.</strong></p>";
        }
        
        return $allPassed;
    }
    
    /**
     * Test database connection
     */
    private function testDatabaseConnection() {
        try {
            $this->conn->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Run installation if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'install-health-backend.php') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Health Questionnaire Backend Installation</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
            .container { background: #f9f9f9; padding: 20px; border-radius: 8px; }
            .button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
            .button:hover { background: #0056b3; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Health Questionnaire Backend Installation</h1>
            
            <?php
            $installer = new HealthBackendInstaller();
            
            if (isset($_GET['action']) && $_GET['action'] === 'install') {
                if ($installer->checkRequirements()) {
                    $installer->install();
                }
            } else {
                echo "<p>This will install the Health Questionnaire Backend system including:</p>";
                echo "<ul>";
                echo "<li>Database tables for health questionnaires</li>";
                echo "<li>Health conditions summary tracking</li>";
                echo "<li>Health alerts system</li>";
                echo "<li>Statistics and analytics tables</li>";
                echo "<li>Export functionality setup</li>";
                echo "</ul>";
                
                $installer->checkRequirements();
                
                echo "<p><a href='?action=install' class='button'>Install Health Backend</a></p>";
            }
            ?>
        </div>
    </body>
    </html>
    <?php
}
?>
