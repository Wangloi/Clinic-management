<?php
// Include necessary data files
include 'visits_data.php';
include 'medicine_data.php';
include 'functions.php';
include 'connection.php';

// Get data for reports
$allVisits = getAllClinicVisits([], 1000); // Get more visits for summary
$medicines = getAllMedicines();
$limitOptions = [10, 25, 50, 100, 250];
$limit = 10;
if (isset($_GET['limit']) && in_array((int)$_GET['limit'], $limitOptions)) {
    $limit = (int)$_GET['limit'];
}
$logsTable = generateLogsTable($pdo, $_GET['sort'] ?? 'timestamp-desc', $limit);

// Filter student visits
$studentVisits = array_filter($allVisits, function($visit) {
    return $visit['patient_type'] === 'Student';
});

// Filter staff visits
$staffVisits = array_filter($allVisits, function($visit) {
    return $visit['patient_type'] === 'Staff';
});

// Calculate student visit statistics and monthly aggregation
$totalStudentVisits = count($studentVisits);
$programStats = [];
$reasonStats = [];
$monthlyStudentVisits = [];

foreach ($studentVisits as $visit) {
    $program = $visit['program_name'] ?? 'Unknown';
    $reason = $visit['reason'] ?? 'Not specified';

    if (!isset($programStats[$program])) {
        $programStats[$program] = 0;
    }
    $programStats[$program]++;

    if (!isset($reasonStats[$reason])) {
        $reasonStats[$reason] = 0;
    }
    $reasonStats[$reason]++;

    // Aggregate visits by month (format: YYYY-MM)
    $month = date('Y-m', strtotime($visit['visit_date']));
    if (!isset($monthlyStudentVisits[$month])) {
        $monthlyStudentVisits[$month] = 0;
    }
    $monthlyStudentVisits[$month]++;
}

// Sort monthly visits by date ascending
ksort($monthlyStudentVisits);

$monthlyStaffVisits = [];
foreach ($staffVisits as $visit) {
    $month = date('Y-m', strtotime($visit['visit_date']));
    if (!isset($monthlyStaffVisits[$month])) {
        $monthlyStaffVisits[$month] = 0;
    }
    $monthlyStaffVisits[$month]++;
}
ksort($monthlyStaffVisits);

// Calculate medicine statistics
$totalMedicines = count($medicines);
$lowStockCount = 0;
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
    if (($medicine['quantity'] ?? 0) < 10) {
        $lowStockCount++;
    }

    if (!empty($medicine['expiration_date'])) {
        $expiryDate = strtotime($medicine['expiration_date']);
        $now = time();
        $daysUntilExpiry = ($expiryDate - $now) / (60 * 60 * 24);

        if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
            $expiringSoon++;
        }
    }
}

// Helper functions for frontend
function getDepartmentDisplayName($department) {
    $displayNames = [
        'college' => 'College',
        'senior_high' => 'Senior High School',
        'junior_high' => 'Junior High School',
        'elementary' => 'Elementary',
        'others' => 'Others'
    ];
    return $displayNames[$department] ?? ucfirst($department);
}

function getPatientName($visit) {
    return $visit['patient_name'] ?? 'Unknown Patient';
}
?>
