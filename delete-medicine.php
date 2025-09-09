<?php
include 'user-role.php';
include 'verifyer.php';
include 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Medicine ID is required']);
    exit;
}

$medicine_id = $_POST['id'];

try {
    // Check if medicine exists
    $stmt = $pdo->prepare("SELECT medicine_id FROM Medicine_Inventory WHERE medicine_id = ?");
    $stmt->execute([$medicine_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Medicine not found']);
        exit;
    }

    // Delete medicine
    $stmt = $pdo->prepare("DELETE FROM Medicine_Inventory WHERE medicine_id = ?");
    $stmt->execute([$medicine_id]);

    if ($stmt->rowCount() > 0) {
        // Log the action
        include 'logging.php';
        logAdminAction('delete', "Deleted medicine ID: $medicine_id");

        echo json_encode(['success' => true, 'message' => 'Medicine deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete medicine']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
}
?>
