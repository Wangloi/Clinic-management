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
$medicine_name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$quantity = $_POST['quantity'] ?? '';
$unit = $_POST['unit'] ?? '';
$batch_no = trim($_POST['batch_no'] ?? '');
$expiration_date = $_POST['expiration_date'] ?? '';

try {
    // Validate required fields
    if (empty($medicine_name)) {
        echo json_encode(['success' => false, 'message' => 'Medicine name is required']);
        exit;
    }

    if (empty($quantity) || !is_numeric($quantity) || $quantity < 0) {
        echo json_encode(['success' => false, 'message' => 'Valid quantity is required']);
        exit;
    }

    if (empty($unit)) {
        echo json_encode(['success' => false, 'message' => 'Unit is required']);
        exit;
    }

    // Check if medicine exists
    $stmt = $pdo->prepare("SELECT medicine_id FROM Medicine_Inventory WHERE medicine_id = ?");
    $stmt->execute([$medicine_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Medicine not found']);
        exit;
    }

    // Update medicine
    $stmt = $pdo->prepare("UPDATE Medicine_Inventory SET medicine_name = ?, description = ?, quantity = ?, unit = ?, batch_no = ?, expiration_date = ?, updated_at = CURRENT_TIMESTAMP WHERE medicine_id = ?");
    $stmt->execute([$medicine_name, $description, $quantity, $unit, $batch_no, $expiration_date, $medicine_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Medicine updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to medicine']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
}
?>
