<?php
/**
 * Get Visit Details API
 * Returns detailed information about a specific visit for medication dispensing
 */

session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['clinic', 'head'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

$visitId = $_GET['visit_id'] ?? '';

if (empty($visitId)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Visit ID is required'
    ]);
    exit();
}

try {
    // Get visit details with patient information
    $sql = "SELECT 
                cv.*,
                CASE 
                    WHEN cv.patient_type = 'Student' THEN 
                        CONCAT(s.Student_Fname, ' ', COALESCE(s.Student_Mname, ''), ' ', s.Student_Lname)
                    WHEN cv.patient_type = 'Staff' THEN 
                        CONCAT(cs.clinic_Fname, ' ', cs.clinic_Lname)
                    ELSE 'Unknown Patient'
                END as patient_name,
                DATE_FORMAT(cv.visit_date, '%M %d, %Y') as formatted_visit_date
            FROM clinic_visits cv
            LEFT JOIN Students s ON cv.patient_type = 'Student' AND cv.patient_id = s.Student_ID
            LEFT JOIN Clinic_Staff cs ON cv.patient_type = 'Staff' AND cv.patient_id = cs.staff_id
            WHERE cv.visit_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$visitId]);
    $visit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$visit) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Visit not found'
        ]);
        exit();
    }
    
    // Format the response
    $response = [
        'success' => true,
        'visit' => [
            'visit_id' => $visit['visit_id'],
            'patient_name' => $visit['patient_name'],
            'patient_type' => $visit['patient_type'],
            'patient_id' => $visit['patient_id'],
            'visit_date' => $visit['formatted_visit_date'],
            'reason' => $visit['reason_for_visit'] ?? $visit['reason'] ?? 'N/A',
            'diagnosis' => $visit['diagnosis'] ?? 'N/A',
            'treatment' => $visit['treatment'] ?? 'N/A',
            'remarks' => $visit['remarks'] ?? 'N/A'
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error getting visit details: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>
