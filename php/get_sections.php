<?php
include 'connection.php';
include 'students_data.php';

header('Content-Type: application/json');

try {
    $programId = $_GET['program_id'] ?? '';
    
    if (empty($programId)) {
        echo json_encode(['success' => false, 'message' => 'Program ID parameter is required']);
        exit;
    }

    // Get all sections and filter by program
    $allSections = getAllSections();
    $filteredSections = array_filter($allSections, function($section) use ($programId) {
        return $section['program_id'] == $programId;
    });

    echo json_encode(['success' => true, 'sections' => array_values($filteredSections)]);

} catch (Exception $e) {
    error_log("Error in get_sections.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}
?>
