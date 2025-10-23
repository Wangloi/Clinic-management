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

    // Validate student_id format (allow alphanumeric)
    if (empty($student_id) || !preg_match('/^[a-zA-Z0-9\-_]+$/', $student_id)) {
        throw new Exception('Student ID must contain only letters, numbers, hyphens, and underscores');
    }

    // Check if student_id already exists
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM Students WHERE student_id = ?");
    $check_stmt->execute([$student_id]);
    if ($check_stmt->fetchColumn() > 0) {
        throw new Exception('Student ID already exists');
    }

    // Check if section_id exists
    $section_check = $pdo->prepare("SELECT COUNT(*) FROM Sections WHERE section_id = ?");
    $section_check->execute([$section_id]);
    if ($section_check->fetchColumn() == 0) {
        throw new Exception('Invalid section selected');
    }

    // Insert new student
    $insert_stmt = $pdo->prepare("
        INSERT INTO Students (student_id, Student_Fname, Student_Mname, Student_Lname, section_id)
        VALUES (?, ?, ?, ?, ?)
    ");

    $success = $insert_stmt->execute([
        $student_id,
        $first_name,
        $middle_name ?: null, // Allow empty middle name
        $last_name,
        $section_id
    ]);

    if (!$success) {
        throw new Exception('Failed to add student to database');
    }

    // Log the action
    include 'logging.php';
    $student_name = $first_name . ' ' . ($middle_name ? $middle_name . ' ' : '') . $last_name;
    logAdminAction('add', "Added student: $student_name (ID: $student_id)");

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Student added successfully',
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
