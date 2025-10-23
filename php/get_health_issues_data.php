<?php
include 'connection.php';
include 'user-role.php';
include 'verifyer.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT reason, COUNT(*) as issue_count
        FROM clinic_visits
        WHERE visit_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
        AND reason IS NOT NULL AND reason != ''
        GROUP BY reason
        ORDER BY issue_count DESC
        LIMIT 5
    ");

    $issues = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'issues' => $issues
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
