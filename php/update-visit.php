<?php
include 'connection.php';

header('Content-Type: application/json');

try {
    // Get form data
    $visit_id = $_POST['visit_id'] ?? '';
    $patient_type = $_POST['patient_type'] ?? '';
    $patient_id = $_POST['patient_id'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $treatment = $_POST['treatment'] ?? '';
    $remarks = $_POST['remarks'] ?? '';

    // Validate required fields
    if (empty($visit_id) || empty($patient_type) || empty($patient_id) || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Visit ID, patient type, patient ID, and reason are required']);
        exit;
    }

    // Update visit in database
    $stmt = $pdo->prepare("
        UPDATE Clinic_Visits
        SET patient_type = ?, patient_id = ?, reason = ?, diagnosis = ?, treatment = ?, remarks = ?
        WHERE visit_id = ?
    ");

    $stmt->execute([$patient_type, $patient_id, $reason, $diagnosis, $treatment, $remarks, $visit_id]);

    if ($stmt->rowCount() >= 0) {
        // Log the action
        include 'logging.php';
        logAdminAction('edit', "Updated visit ID: $visit_id for $patient_type ID: $patient_id - Reason: $reason");

        echo json_encode(['success' => true, 'message' => 'Visit updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update visit']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
}
?>
