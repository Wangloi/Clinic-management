<?php
// Student Dashboard Configuration
// This file contains configuration constants and settings for the student dashboard

// Page title and meta information (using existing constants from student-config.php)
// STUDENT_DASHBOARD_TITLE already defined in student-config.php

// CSS and JavaScript file paths
define('STUDENT_CSS_PATH', '../css/student-alerts.css');
define('STUDENT_JS_LOGIN_PATH', '../js/student-login-success.js');
define('STUDENT_JS_DASHBOARD_PATH', '../js/student-dashboard.js');

// Image paths
define('CLINIC_LOGO_PATH', '../images/SRCB.png');
define('CLINIC_BG_PATH', '../images/SRCBBG.png');

// Navigation menu items
$student_nav_items = [
    [
        'id' => 'home',
        'label' => 'Home',
        'icon' => 'home',
        'active' => true
    ],
    [
        'id' => 'about',
        'label' => 'About',
        'icon' => 'info',
        'active' => false
    ]
];

// Feature cards configuration
$feature_cards = [
    [
        'title' => 'Health Questionnaire',
        'description' => 'Complete your health assessment and medical information',
        'button_text' => 'Start Assessment',
        'button_action' => 'showMedicalHistory()',
        'icon' => 'document',
        'color_theme' => 'blue'
    ]
];

// Health questionnaire configuration (using existing constants from student-config.php)
// HEALTH_QUEST_STEPS and HEALTH_QUEST_TITLE already defined in student-config.php

// Database table names (if needed for queries)
define('STUDENTS_TABLE', 'Students');
define('PROGRAMS_TABLE', 'Programs');
define('SECTIONS_TABLE', 'Sections');
define('CLINIC_VISITS_TABLE', 'Clinic_Visits');

// Error messages
define('DB_ERROR_MESSAGE', 'Database connection error. Please try again later.');
define('STUDENT_NOT_FOUND_MESSAGE', 'Student information not found.');
define('ACCESS_DENIED_MESSAGE', 'Access denied. Please log in as a student.');

// Success messages
define('HEALTH_QUEST_SUCCESS_MESSAGE', 'Health questionnaire submitted successfully!');
define('DATA_SAVED_MESSAGE', 'Your information has been saved.');

// Validation rules
define('MIN_NAME_LENGTH', 2);
define('MAX_NAME_LENGTH', 100);
define('CONTACT_NUMBER_PATTERN', '/^[0-9]{10,11}$/');

// Session configuration
define('STUDENT_SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('STUDENT_ROLE_REQUIRED', 'student');
?>
