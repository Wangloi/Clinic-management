<?php
include 'connection.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT COUNT(*) as low_stock_count FROM Medicine_Inventory WHERE quantity < 10");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'low_stock_count' => intval($result['low_stock_count'])
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
