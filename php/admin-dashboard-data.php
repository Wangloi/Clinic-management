<?php
include 'connection.php';
include 'user-role.php';
include 'verifyer.php';
include 'student_count.php';
include 'recent_visits.php';
include 'medicine_data.php';

$recentVisits = getRecentClinicVisits(10);

$medicines = getAllMedicines() ?? [];
$totalMedicines = count($medicines);
$expiringSoon = 0;
$totalDispensed = 0;

// Get total dispensed medicines
try {
    $stmt = $pdo->query("SELECT SUM(quantity_dispensed) as total_dispensed FROM Medicine_Dispensing");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalDispensed = $result['total_dispensed'] ?? 0;
} catch (PDOException $e) {
    $totalDispensed = 0;
}

foreach ($medicines as $medicine) {
    if (!empty($medicine['expiration_date'])) {
        $expiryDate = strtotime($medicine['expiration_date']);
        $now = time();
        $daysUntilExpiry = ($expiryDate - $now) / (60 * 60 * 24);

        if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
            $expiringSoon++;
        }
    }
}
?>
