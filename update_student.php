<?php
header('Content-Type: application/json');
include 'connection.php';

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate form data
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $section_id = trim($_POST['section'] ?? '');

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($student_id) || empty($section_id)) {
        throw new Exception('All required fields must be filled');
    }

    // Validate student_id format (assuming it's numeric)
    if (!is_numeric($student_id)) {
        throw new Exception('Student ID must be numeric');
    }

    // Check if student_id exists
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM Students WHERE student_id = ?");
    $check_stmt->execute([$student_id]);
    if ($check_stmt->fetchColumn() == 0) {
        throw new Exception('Student not found');
    }

    // Check if section_id exists
    $section_check = $pdo->prepare("SELECT COUNT(*) FROM Sections WHERE section_id = ?");
    $section_check->execute([$section_id]);
    if ($section_check->fetchColumn() == 0) {
        throw new Exception('Invalid section selected');
    }

    // Update student
    $update_stmt = $pdo->prepare("
        UPDATE Students
        SET Student_Fname = ?, Student_Mname = ?, Student_Lname = ?, section_id = ?, updated_at = NOW()
        WHERE student_id = ?
    ");

    $success = $update_stmt->execute([
        $first_name,
        $middle_name ?: null, // Allow empty middle name
        $last_name,
        $section_id,
        $student_id
    ]);

    if (!$success) {
        throw new Exception('Failed to update student in database');
    }

    // Log the action
    include 'logging.php';
    $student_name = $first_name . ' ' . ($middle_name ? $middle_name . ' ' : '') . $last_name;
    logAdminAction('edit', "Updated student: $student_name (ID: $student_id)");

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Student updated successfully',
        'student_id' => $student_id
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
