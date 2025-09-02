<?php
include 'connection.php';

header('Content-Type: application/json');

try {
    // Get form data
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $unit = $_POST['unit'] ?? '';
    $expiration_date = $_POST['expiration_date'] ?? '';

    // Validate required fields
    if (empty($name) || empty($quantity) || empty($unit)) {
        echo json_encode(['success' => false, 'message' => 'Medicine name, quantity, and unit are required']);
        exit;
    }

    // Validate quantity is a positive number
    if (!is_numeric($quantity) || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be a positive number']);
        exit;
    }

    // Validate expiration date is in the future
    if (!empty($expiration_date) && strtotime($expiration_date) <= time()) {
        echo json_encode(['success' => false, 'message' => 'Expiration date must be in the future']);
        exit;
    }

    // Insert new medicine into database
    $stmt = $pdo->prepare("
        INSERT INTO Medicine_Inventory
        (medicine_name, description, quantity, unit, expiration_date)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([$name, $description, $quantity, $unit, $expiration_date]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Medicine added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add medicine']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
}
?>
