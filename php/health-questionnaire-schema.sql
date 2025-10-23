-- Health Questionnaire Database Schema
-- This creates the complete database structure for student health questionnaires

-- Main health questionnaire table
CREATE TABLE IF NOT EXISTS `health_questionnaires` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Health conditions summary table for quick reporting
CREATE TABLE IF NOT EXISTS `health_conditions_summary` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Health alerts table for flagging important conditions
CREATE TABLE IF NOT EXISTS `health_alerts` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Health statistics table for dashboard analytics
CREATE TABLE IF NOT EXISTS `health_statistics` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
