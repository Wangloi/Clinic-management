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

    // Validate student_id format (allow alphanumeric)
    if (empty($student_id) || !preg_match('/^[a-zA-Z0-9\-_]+$/', $student_id)) {
        throw new Exception('Student ID must contain only letters, numbers, hyphens, and underscores');
    }

    // Check if student_id exists and get student info for logging
    $check_stmt = $pdo->prepare("SELECT Student_Fname, Student_Mname, Student_Lname, created_at FROM Students WHERE student_id = ?");
    $check_stmt->execute([$student_id]);
    $student = $check_stmt->fetch();

    if (!$student) {
        throw new Exception('Student not found');
    }

    // Check if student was recently added (within 24 hours)
    if (isset($student['created_at'])) {
        $created_time = strtotime($student['created_at']);
        $current_time = time();
        $hours_since_creation = ($current_time - $created_time) / 3600;
        
        if ($hours_since_creation < 24) {
            throw new Exception('Cannot delete student: Student was added recently. Please wait 24 hours before deletion.');
        }
    }

    // Check if student has health records
    $health_check = $pdo->prepare("SELECT COUNT(*) FROM Health_Questionnaires WHERE student_id = ?");
    $health_check->execute([$student_id]);
    $has_health_records = $health_check->fetchColumn() > 0;

    // Check if student has clinic visits
    $visit_check = $pdo->prepare("SELECT COUNT(*) FROM clinic_visits WHERE patient_type = 'Student' AND patient_id = ?");
    $visit_check->execute([$student_id]);
    $has_visits = $visit_check->fetchColumn() > 0;

    if ($has_health_records || $has_visits) {
        throw new Exception('Cannot delete student: Student has existing health records or clinic visits. Please archive instead of deleting.');
    }

    // Disable foreign key checks to handle constraints
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

    // Start transaction for atomic operation
    $pdo->beginTransaction();

    try {
        // First, get all visit_ids for this student
        $visit_stmt = $pdo->prepare("SELECT visit_id FROM clinic_visits WHERE patient_type = 'Student' AND patient_id = ?");
        $visit_stmt->execute([$student_id]);
        $visits = $visit_stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete medicine_dispensing records for these visits
        if (!empty($visits)) {
            $placeholders = str_repeat('?,', count($visits) - 1) . '?';
            $delete_medicine_stmt = $pdo->prepare("DELETE FROM medicine_dispensing WHERE visit_id IN ($placeholders)");
            $delete_medicine_stmt->execute($visits);
        }

        // Delete clinic_visits for this student
        $delete_visits_stmt = $pdo->prepare("DELETE FROM clinic_visits WHERE patient_type = 'Student' AND patient_id = ?");
        $delete_visits_stmt->execute([$student_id]);

        // Delete student
        $delete_stmt = $pdo->prepare("DELETE FROM Students WHERE student_id = ?");
        $success = $delete_stmt->execute([$student_id]);

        if (!$success) {
            throw new Exception('Failed to delete student from database');
        }

        // Commit transaction
        $pdo->commit();

        // Re-enable foreign key checks
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        // Re-enable foreign key checks
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
        throw $e;
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
