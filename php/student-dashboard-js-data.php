<?php
// Student Dashboard JavaScript Data Generator
// This file generates JavaScript variables with student and dropdown data

// Function to generate student data JavaScript
function generateStudentDataJS($student_id, $full_name, $student, $all_programs, $all_sections) {
    // Debug: Log the data being passed to JavaScript
    error_log("Generating JS data for student: " . print_r($student, true));
    
    $js_code = "
        // Pass student data to JavaScript
        window.studentData = " . json_encode([
            'student_id' => $student_id,
            'full_name' => $full_name,
            'fname' => $student['Student_Fname'] ?? '',
            'mname' => $student['Student_Mname'] ?? '',
            'lname' => $student['Student_Lname'] ?? '',
            'contact_number' => $student['contact_number'] ?? '',
            'section_id' => $student['section_id'] ?? '',
            'section_name' => $student['section_name'] ?? '',
            'program_name' => $student['program_name'] ?? '',
            'program_id' => $student['program_id'] ?? '',
            'department_level' => $student['department_level'] ?? ''
        ], JSON_PRETTY_PRINT) . ";
        
        // Pass programs and sections data to JavaScript
        window.allPrograms = " . json_encode($all_programs, JSON_PRETTY_PRINT) . ";
        window.allSections = " . json_encode($all_sections, JSON_PRETTY_PRINT) . ";
    ";
    
    return $js_code;
}

// Note: generateLoginSuccessJS() function is already defined in student-login-handler.php
// Using the existing function to avoid redeclaration
?>
