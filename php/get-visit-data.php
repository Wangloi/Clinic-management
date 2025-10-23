<?php
include 'connection.php';

header('Content-Type: application/json');

try {
    $visitId = $_GET['id'] ?? null;

    if (!$visitId) {
        echo json_encode(['success' => false, 'message' => 'Visit ID is required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM Clinic_Visits WHERE visit_id = ?");
    $stmt->execute([$visitId]);
    $visit = $stmt->fetch();

    if ($visit) {
        echo json_encode(['success' => true, 'visit' => $visit]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Visit not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
}
?>
