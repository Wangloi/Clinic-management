<?php
include 'connection.php';
header('Content-Type: application/json');

try {
    // Count clinic visits this week as proxy for prescriptions
    $stmt = $pdo->query("SELECT COUNT(*) as weekly_visits FROM clinic_visits WHERE visit_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'weekly_prescriptions' => intval($result['weekly_visits'])
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
