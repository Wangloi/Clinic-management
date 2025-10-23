<?php
/**
 * Admin Health Records API
 * Allows admins to view student health records
 */

// Clean any output before starting
ob_start();
session_start();

// Clean the output buffer to prevent any unwanted output
ob_clean();

// Suppress PHP warnings to prevent JSON corruption
error_reporting(E_ERROR | E_PARSE);

require_once 'connection.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if user is logged in and is an admin (clinic staff or head staff)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['clinic', 'head'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Admin privileges required.',
        'error_code' => 'AUTH_REQUIRED'
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$studentId = $_GET['student_id'] ?? '';

if ($method !== 'GET' || empty($action) || empty($studentId)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request parameters',
        'error_code' => 'INVALID_REQUEST'
    ]);
    exit();
}

try {
    switch ($action) {
        case 'get_student_health_record':
            getStudentHealthRecord($studentId);
            break;
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action',
                'error_code' => 'INVALID_ACTION'
            ]);
    }
} catch (Exception $e) {
    error_log("Admin Health Records API Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $e->getMessage(),
        'error_code' => 'SERVER_ERROR',
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

/**
 * Get student health record for admin view
 */
function getStudentHealthRecord($studentId) {
    global $pdo;
    
    try {
        // Get student basic info
        $studentSql = "SELECT s.*, 
                              sec.section_name, 
                              p.program_name, 
                              d.department_level
                       FROM Students s
                       LEFT JOIN Sections sec ON s.section_id = sec.section_id
                       LEFT JOIN Programs p ON sec.program_id = p.program_id
                       LEFT JOIN Departments d ON p.department_id = d.department_id
                       WHERE s.Student_ID = :student_id";
        
        $stmt = $pdo->prepare($studentSql);
        $stmt->execute([':student_id' => $studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Student not found',
                'error_code' => 'STUDENT_NOT_FOUND'
            ]);
            return;
        }
        
        // Get health record
        $healthSql = "SELECT * FROM Health_Questionnaires WHERE student_id = :student_id";
        $stmt = $pdo->prepare($healthSql);
        $stmt->execute([':student_id' => $studentId]);
        $healthRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Format student name
        $studentName = trim(($student['Student_Fname'] ?? '') . ' ' . 
                           ($student['Student_Mname'] ?? '') . ' ' . 
                           ($student['Student_Lname'] ?? ''));
        
        // Prepare response
        $response = [
            'success' => true,
            'student' => [
                'id' => $student['Student_ID'] ?? $student['student_id'] ?? null,
                'name' => $studentName,
                'first_name' => $student['Student_Fname'] ?? '',
                'middle_name' => $student['Student_Mname'] ?? '',
                'last_name' => $student['Student_Lname'] ?? '',
                'contact_number' => $student['contact_number'] ?? '',
                'section_name' => $student['section_name'] ?? 'N/A',
                'program_name' => $student['program_name'] ?? 'N/A',
                'department_level' => $student['department_level'] ?? 'N/A'
            ],
            'health_record' => $healthRecord ? [
                'exists' => true,
                'submitted_at' => $healthRecord['submitted_at'] ?? null,
                'education_level' => $healthRecord['education_level'] ?? null,
                'student_sex' => $healthRecord['student_sex'] ?? null,
                'student_birthday' => $healthRecord['student_birthday'] ?? null,
                'student_age' => $healthRecord['student_age'] ?? null,
                'home_address' => $healthRecord['home_address'] ?? null,
                'height' => $healthRecord['height'] ?? null,
                'weight' => $healthRecord['weight'] ?? null,
                'blood_pressure' => $healthRecord['blood_pressure'] ?? null,
                'heart_rate' => $healthRecord['heart_rate'] ?? null,
                'respiratory_rate' => $healthRecord['respiratory_rate'] ?? null,
                'temperature' => $healthRecord['temperature'] ?? null,
                // Add other health record fields as needed
                'has_allergies' => $healthRecord['has_allergies'] ?? null,
                'allergies_remarks' => $healthRecord['allergies_remarks'] ?? '',
                'has_asthma' => $healthRecord['has_asthma'] ?? null,
                'asthma_remarks' => $healthRecord['asthma_remarks'] ?? '',
                'has_health_problems' => $healthRecord['has_health_problems'] ?? null,
                'health_problems_remarks' => $healthRecord['health_problems_remarks'] ?? '',
                'current_medications_vitamins' => $healthRecord['current_medications_vitamins'] ?? '',
                'additional_notes' => $healthRecord['additional_notes'] ?? ''
            ] : [
                'exists' => false,
                'message' => 'No health record found for this student'
            ]
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        error_log("Error getting student health record: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred',
            'error_code' => 'DATABASE_ERROR'
        ]);
    }
}
?>
