-- ==========================
-- Health Questionnaires Table
-- ==========================
CREATE TABLE IF NOT EXISTS Health_Questionnaires (
    questionnaire_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    education_level ENUM('basic', 'college') DEFAULT 'basic', -- Auto-determined from student's department_level

    -- Additional Personal Information (not in Students table)
    student_sex ENUM('Male', 'Female'),
    student_birthday DATE,
    student_age INT,
    home_address TEXT,

    -- Vital Signs
    height DECIMAL(5,2), -- in cm
    weight DECIMAL(5,2), -- in kg
    blood_pressure VARCHAR(20),
    heart_rate INT, -- bpm
    respiratory_rate INT, -- breaths/min
    temperature DECIMAL(4,1), -- in Celsius

    -- Health History (Basic Education)
    has_allergies ENUM('YES', 'NO'),
    allergies_remarks TEXT,
    has_medicines ENUM('YES', 'NO'),
    medicine_allergies TEXT,
    has_vaccines ENUM('YES', 'NO'),
    vaccine_allergies TEXT,
    has_foods ENUM('YES', 'NO'),
    food_allergies TEXT,
    has_other ENUM('YES', 'NO'),
    other_allergies TEXT,
    has_asthma ENUM('YES', 'NO'),
    asthma_remarks TEXT,
    has_healthproblem ENUM('YES', 'NO'),
    healthproblem_remarks TEXT,
    has_earinfection ENUM('YES', 'NO'),
    earinfection_remarks TEXT,
    has_potty ENUM('YES', 'NO'),
    potty_remarks TEXT,
    has_uti ENUM('YES', 'NO'),
    uti_remarks TEXT,
    has_chickenpox ENUM('YES', 'NO'),
    chickenpox_remarks TEXT,
    has_dengue ENUM('YES', 'NO'),
    dengue_remarks TEXT,
    has_anemia ENUM('YES', 'NO'),
    anemia_remarks TEXT,
    has_gastritis ENUM('YES', 'NO'),
    gastritis_remarks TEXT,
    has_pneumonia ENUM('YES', 'NO'),
    pneumonia_remarks TEXT,
    has_obesity ENUM('YES', 'NO'),
    obesity_remarks TEXT,
    has_covid19 ENUM('YES', 'NO'),
    covid19_remarks TEXT,
    has_otherconditions ENUM('YES', 'NO'),
    otherconditions_remarks TEXT,

    -- Hospitalization History
    has_hospitalization ENUM('YES', 'NO'),
    hospitalization_date DATE,
    hospital_name VARCHAR(200),
    hospitalization_remarks TEXT,

    -- Immunization
    pneumonia_vaccine BOOLEAN DEFAULT FALSE,
    flu_vaccine BOOLEAN DEFAULT FALSE,
    measles_vaccine BOOLEAN DEFAULT FALSE,
    hep_b_vaccine BOOLEAN DEFAULT FALSE,
    cervical_cancer_vaccine BOOLEAN DEFAULT FALSE,
    covid_1st_dose BOOLEAN DEFAULT FALSE,
    covid_2nd_dose BOOLEAN DEFAULT FALSE,
    covid_booster BOOLEAN DEFAULT FALSE,
    other_vaccines BOOLEAN DEFAULT FALSE,
    other_vaccines_text TEXT,

    -- Menstruation (Female only)
    menarche_age INT,
    menstrual_days INT,
    pads_consumed VARCHAR(20),
    menstrual_problems TEXT,

    -- Current Health Concerns
    present_concerns TEXT,
    current_medications_vitamins TEXT,
    additional_notes TEXT,

    -- College-specific fields
    current_medications TEXT,
    lifestyle_habits TEXT,
    academic_stress TEXT,
    current_symptoms TEXT,
    allergies_all TEXT,
    chronic_conditions TEXT,
    family_history TEXT,
    previous_hospitalizations TEXT,
    mental_health_history TEXT,
    stress_levels TEXT,
    support_system TEXT,
    wellness_goals TEXT,

    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key constraint
    FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,

    -- Index for performance
    INDEX idx_student_id (student_id),
    INDEX idx_submitted_at (submitted_at)
);
