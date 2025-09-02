<?php
include 'connection.php';

header('Content-Type: application/json');

try {
    // Get form data
    $patient_type = $_POST['patient_type'] ?? '';
    $patient_id = $_POST['patient_id'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $treatment = $_POST['treatment'] ?? '';
    $remarks = $_POST['remarks'] ?? '';

    // Validate required fields
    if (empty($patient_type) || empty($patient_id) || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Patient type, patient ID, and reason are required']);
        exit;
    }

    // Insert new visit into database
    $stmt = $pdo->prepare("
        INSERT INTO Clinic_Visits 
        (patient_type, patient_id, visit_date, reason, diagnosis, treatment, remarks)
        VALUES (?, ?, CURDATE(), ?, ?, ?, ?)
    ");

    $stmt->execute([$patient_type, $patient_id, $reason, $diagnosis, $treatment, $remarks]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Visit added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add visit']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
}
?>
