<?php
// Simple test API for health questionnaire debugging
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Check session
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
        echo json_encode([
            'success' => false,
            'message' => 'Not logged in as student',
            'session_data' => [
                'user_id' => $_SESSION['user_id'] ?? 'not set',
                'user_type' => $_SESSION['user_type'] ?? 'not set'
            ]
        ]);
        exit;
    }

    // Test database connection
    require_once 'connection.php';
    
    $studentId = $_SESSION['user_id'];
    
    // Test basic query
    $stmt = $pdo->prepare("SELECT Student_ID, Student_Fname, Student_Lname FROM Students WHERE Student_ID = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    
    if (!$student) {
        echo json_encode([
            'success' => false,
            'message' => 'Student not found',
            'student_id' => $studentId
        ]);
        exit;
    }
    
    // Check if Health_Questionnaires table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'Health_Questionnaires'");
    $tableExists = $tableCheck->rowCount() > 0;
    
    echo json_encode([
        'success' => true,
        'message' => 'All tests passed',
        'data' => [
            'student_id' => $studentId,
            'student_name' => $student['Student_Fname'] . ' ' . $student['Student_Lname'],
            'table_exists' => $tableExists,
            'session_valid' => true,
            'db_connected' => true
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
