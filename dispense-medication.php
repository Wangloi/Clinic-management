<?php
include 'user-role.php';
include 'verifyer.php';
include 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    $visit_id = $input['visit_id'] ?? null;
    $medications = $input['medications'] ?? [];
    $notes = $input['notes'] ?? '';

    if (!$visit_id || empty($medications)) {
        throw new Exception('Visit ID and medications are required');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Insert dispensed medications
    $stmt = $pdo->prepare("
        INSERT INTO Medicine_Dispensing (visit_id, medicine_id, quantity_dispensed)
        VALUES (?, ?, ?)
    ");

    $total_dispensed = 0;
    foreach ($medications as $med) {
        $stmt->execute([
            $visit_id,
            $med['medicine_id'],
            $med['quantity']
        ]);

        // Update medicine inventory
        $update_stmt = $pdo->prepare("
            UPDATE Medicine_Inventory
            SET quantity = quantity - ?
            WHERE medicine_id = ? AND quantity >= ?
        ");
        $update_stmt->execute([$med['quantity'], $med['medicine_id'], $med['quantity']]);

        if ($update_stmt->rowCount() === 0) {
            throw new Exception("Insufficient stock for medicine ID: {$med['medicine_id']}");
        }

        $total_dispensed += $med['quantity'];
    }

    // Update visit record with notes if provided
    if (!empty($notes)) {
        $notes_stmt = $pdo->prepare("
            UPDATE clinic_visits
            SET remarks = CONCAT(COALESCE(remarks, ''), '\n\nMedication Notes: ', ?)
            WHERE visit_id = ?
        ");
        $notes_stmt->execute([$notes, $visit_id]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => "Successfully dispensed {$total_dispensed} medication(s)",
        'dispensed_count' => $total_dispensed
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    // Rollback transaction on database error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>
