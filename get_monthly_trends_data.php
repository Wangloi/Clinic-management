<?php
include 'connection.php';
include 'user-role.php';
include 'verifyer.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT
            DATE_FORMAT(visit_date, '%M %Y') as month_year,
            COUNT(*) as visit_count
        FROM clinic_visits
        WHERE visit_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
        GROUP BY YEAR(visit_date), MONTH(visit_date), DATE_FORMAT(visit_date, '%M %Y')
        ORDER BY visit_date ASC
        LIMIT 6
    ");

    $trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'trends' => $trends
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
