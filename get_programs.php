<?php
include 'connection.php';
include 'students_data.php';

header('Content-Type: application/json');

try {
    $department = $_GET['department'] ?? '';
    
    if (empty($department)) {
        echo json_encode(['success' => false, 'message' => 'Department parameter is required']);
        exit;
    }

    // Get all programs and filter by department
    $allPrograms = getAllPrograms();
    $filteredPrograms = array_filter($allPrograms, function($program) use ($department) {
        return $program['department_level'] === $department;
    });

    echo json_encode(['success' => true, 'programs' => array_values($filteredPrograms)]);

} catch (Exception $e) {
    error_log("Error in get_programs.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}
?>
