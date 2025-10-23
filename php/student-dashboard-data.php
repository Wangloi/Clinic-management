<?php
// Student Dashboard Data Handler
// This file handles all database queries for the student dashboard

// Ensure connection is available
if (!isset($pdo)) {
    die('Database connection not available');
}

// Get student details with program and section information
try {
    $student_details_query = "
        SELECT s.*, p.program_name, d.department_level, sec.section_name, p.program_id,
            (SELECT COUNT(*) FROM Clinic_Visits cv WHERE cv.patient_type = 'Student' AND cv.patient_id = s.student_id) AS total_visits
        FROM Students s
        LEFT JOIN Sections sec ON s.section_id = sec.section_id
        LEFT JOIN Programs p ON sec.program_id = p.program_id
        LEFT JOIN Departments d ON p.department_id = d.department_id
        WHERE s.student_id = :student_id
    ";
    
    $stmt = $pdo->prepare($student_details_query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug: Log the student data
    error_log("Student data retrieved: " . print_r($student, true));
    
    // If no student found or missing department info, try a simpler query
    if (!$student || !$student['department_level']) {
        error_log("Student not found or missing department info, trying basic query");
        
        // Try basic student query first
        $basic_query = "SELECT * FROM Students WHERE student_id = :student_id";
        $basic_stmt = $pdo->prepare($basic_query);
        $basic_stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $basic_stmt->execute();
        $basic_student = $basic_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($basic_student) {
            // Get section and program info separately
            if ($basic_student['section_id']) {
                $section_query = "
                    SELECT sec.section_name, p.program_id, p.program_name, p.department_level
                    FROM Sections sec
                    JOIN Programs p ON sec.program_id = p.program_id
                    WHERE sec.section_id = :section_id
                ";
                $section_stmt = $pdo->prepare($section_query);
                $section_stmt->bindParam(':section_id', $basic_student['section_id'], PDO::PARAM_INT);
                $section_stmt->execute();
                $section_info = $section_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($section_info) {
                    $student = array_merge($basic_student, $section_info, ['total_visits' => 0]);
                } else {
                    $student = array_merge($basic_student, [
                        'section_name' => null,
                        'program_id' => null,
                        'program_name' => null,
                        'department_level' => null,
                        'total_visits' => 0
                    ]);
                }
            } else {
                $student = array_merge($basic_student, [
                    'section_name' => null,
                    'program_id' => null,
                    'program_name' => null,
                    'department_level' => null,
                    'total_visits' => 0
                ]);
            }
        } else {
            // Last resort - use session data
            $student = [
                'student_id' => $student_id,
                'Student_Fname' => '',
                'Student_Mname' => '',
                'Student_Lname' => '',
                'contact_number' => '',
                'section_id' => null,
                'section_name' => null,
                'program_id' => null,
                'program_name' => null,
                'department_level' => null,
                'total_visits' => 0
            ];
        }
        
        error_log("Final student data: " . print_r($student, true));
    }
    
} catch (PDOException $e) {
    error_log("Error fetching student details: " . $e->getMessage());
    $student = [
        'student_id' => $student_id,
        'Student_Fname' => '',
        'Student_Mname' => '',
        'Student_Lname' => '',
        'contact_number' => '',
        'section_id' => null,
        'section_name' => null,
        'program_id' => null,
        'program_name' => null,
        'department_level' => null,
        'total_visits' => 0
    ];
}

// Get all programs for dropdowns (using same query as admin system)
try {
    $programs_query = "SELECT p.*, d.department_level FROM Programs p LEFT JOIN Departments d ON p.department_id = d.department_id ORDER BY p.program_name ASC";
    $programs_stmt = $pdo->prepare($programs_query);
    $programs_stmt->execute();
    $all_programs = $programs_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching programs: " . $e->getMessage());
    $all_programs = [];
}

// Get all sections for dropdowns (using same query as admin system)
try {
    $sections_query = "SELECT sec.*, p.program_name, d.department_level FROM Sections sec LEFT JOIN Programs p ON sec.program_id = p.program_id LEFT JOIN Departments d ON p.department_id = d.department_id ORDER BY sec.section_name ASC";
    $sections_stmt = $pdo->prepare($sections_query);
    $sections_stmt->execute();
    $all_sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching sections: " . $e->getMessage());
    $all_sections = [];
}

// Helper function to get student full name
function getStudentFullName($student) {
    $name_parts = array_filter([
        $student['Student_Fname'] ?? '',
        $student['Student_Mname'] ?? '',
        $student['Student_Lname'] ?? ''
    ]);
    return implode(' ', $name_parts) ?: 'Unknown Student';
}

// Helper function to get department display name
function getDepartmentDisplayName($department_level) {
    $department_names = [
        'College' => 'College',
        'SHS' => 'Senior High School',
        'JHS' => 'Junior High School',
        'Grade School' => 'Grade School'
    ];
    return $department_names[$department_level] ?? $department_level ?? 'N/A';
}
?>
