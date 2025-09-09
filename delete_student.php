<?php
header('Content-Type: application/json');
include 'connection.php';

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate form data
    $student_id = trim($_POST['student_id'] ?? '');

    // Validate required fields
    if (empty($student_id)) {
        throw new Exception('Student ID is required');
    }

    // Validate student_id format (assuming it's numeric)
    if (!is_numeric($student_id)) {
        throw new Exception('Student ID must be numeric');
    }

    // Check if student_id exists and get student info for logging
    $check_stmt = $pdo->prepare("SELECT Student_Fname, Student_Mname, Student_Lname FROM Students WHERE student_id = ?");
    $check_stmt->execute([$student_id]);
    $student = $check_stmt->fetch();

    if (!$student) {
        throw new Exception('Student not found');
    }

    // Delete student
    $delete_stmt = $pdo->prepare("DELETE FROM Students WHERE student_id = ?");
    $success = $delete_stmt->execute([$student_id]);

    if (!$success) {
        throw new Exception('Failed to delete student from database');
    }

    // Log the action
    include 'logging.php';
    $student_name = $student['Student_Fname'] . ' ' . ($student['Student_Mname'] ? $student['Student_Mname'] . ' ' : '') . $student['Student_Lname'];
    logAdminAction('delete', "Deleted student: $student_name (ID: $student_id)");

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Student deleted successfully'
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
