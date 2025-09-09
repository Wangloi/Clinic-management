<?php
include 'user-role.php';
include 'verifyer.php';
include 'visits_data.php';
include 'functions.php';
include 'connection.php';

header('Content-Type: application/json');

// Check if month and patient_type are provided
if (!isset($_GET['month']) || !isset($_GET['patient_type'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$month = $_GET['month'];
$patientType = $_GET['patient_type'];

try {
    // Get all visits for the specified month and patient type
    $allVisits = getAllClinicVisits([], 1000);

    // Filter visits by month and patient type
    $filteredVisits = array_filter($allVisits, function($visit) use ($month, $patientType) {
        $visitMonth = date('Y-m', strtotime($visit['visit_date']));
        return $visitMonth === $month && $visit['patient_type'] === $patientType;
    });

    // Format the visits data for the modal
    $visitsData = array_map(function($visit) {
        return [
            'patient_name' => getPatientName($visit),
            'visit_date' => $visit['visit_date'],
            'reason' => $visit['reason'] ?? 'N/A',
            'treatment' => $visit['treatment'] ?? 'N/A',
            'program_name' => $visit['program_name'] ?? 'N/A'
        ];
    }, $filteredVisits);

    // Sort visits by date (most recent first)
    usort($visitsData, function($a, $b) {
        return strtotime($b['visit_date']) - strtotime($a['visit_date']);
    });

    echo json_encode([
        'success' => true,
        'visits' => array_values($visitsData),
        'total' => count($visitsData)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
