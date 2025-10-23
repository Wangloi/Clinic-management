<?php
include 'connection.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT medicine_name, SUM(quantity) as total_quantity
        FROM Medicine_Inventory
        GROUP BY medicine_name
        ORDER BY total_quantity DESC
        LIMIT 10
    ");

    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'medications' => $medications
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
