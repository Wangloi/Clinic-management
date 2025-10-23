<?php
// Student Dashboard Configuration
// This file contains configuration settings for student-related functionality

// Student Dashboard Settings
define('STUDENT_DASHBOARD_TITLE', 'Student Dashboard | Integrated Digital Clinic Management System of St. Rita\'s College of Balingasag');
define('STUDENT_LOGIN_REDIRECT', 'student-login.php');
define('STUDENT_DASHBOARD_REDIRECT', 'student-dashboard.php');

// Alert Settings
define('LOGIN_SUCCESS_TIMER', 5000); // 5 seconds
define('LOGIN_SUCCESS_MESSAGE', 'You have successfully logged into the SRCB Clinic Management System.');

// Health Quest Settings
define('HEALTH_QUEST_STEPS', 5);
define('HEALTH_QUEST_TITLE', 'Your health quest begins here');

// Student Session Keys
define('STUDENT_SESSION_KEYS', [
    'username',
    'logged_in',
    'is_superadmin',
    'user_id',
    'full_name',
    'user_type',
    'ip_address',
    'user_agent',
    'login_success',
    'login_message'
]);

// CSS and JS File Paths
define('STUDENT_CSS_FILES', [
    '../css/style.css',
    '../css/student-dashboard.css',
    '../css/student-alerts.css'
]);

define('STUDENT_JS_FILES', [
    '../js/student-login-success.js',
    '../js/student-dashboard.js'
]);

// External Libraries
define('EXTERNAL_LIBRARIES', [
    'tailwind' => 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4',
    'sweetalert' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11'
]);

// Student Dashboard Navigation Items
define('STUDENT_NAV_ITEMS', [
    'home' => ['label' => 'Home', 'href' => '#home'],
    'goals' => ['label' => 'Our Goals', 'href' => '#goals'],
    'about' => ['label' => 'About', 'href' => '#about']
]);

// Database Query for Student Details
define('STUDENT_DETAILS_QUERY', "
    SELECT s.*, p.program_name, d.department_level, sec.section_name
    FROM Students s
    LEFT JOIN Sections sec ON s.section_id = sec.section_id
    LEFT JOIN Programs p ON sec.program_id = p.program_id
    LEFT JOIN Departments d ON p.department_id = d.department_id
    WHERE s.student_id = :student_id
");
?>
