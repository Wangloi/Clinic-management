<?php
include 'connection.php';
include 'user-role.php';
include 'verifyer.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT
            CASE
                WHEN LOWER(reason) LIKE '%fever%' OR LOWER(reason) LIKE '%temperature%' THEN 'Fever/Infection'
                WHEN LOWER(reason) LIKE '%headache%' OR LOWER(reason) LIKE '%migraine%' THEN 'Headache'
                WHEN LOWER(reason) LIKE '%stomach%' OR LOWER(reason) LIKE '%nausea%' OR LOWER(reason) LIKE '%vomiting%' THEN 'Digestive Issues'
                WHEN LOWER(reason) LIKE '%cough%' OR LOWER(reason) LIKE '%cold%' OR LOWER(reason) LIKE '%flu%' THEN 'Respiratory'
                WHEN LOWER(reason) LIKE '%injury%' OR LOWER(reason) LIKE '%pain%' OR LOWER(reason) LIKE '%sprain%' THEN 'Pain/Injury'
                ELSE 'Other'
            END as category,
            COUNT(*) as category_count
        FROM clinic_visits
        WHERE visit_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
        AND reason IS NOT NULL AND reason != ''
        GROUP BY category
        ORDER BY category_count DESC
    ");

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
