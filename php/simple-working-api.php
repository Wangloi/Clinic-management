<?php
// Simple working medication history API
header('Content-Type: application/json');

try {
    $medicineId = $_GET['medicine_id'] ?? '';
    
    if (empty($medicineId)) {
        echo json_encode(['success' => false, 'message' => 'Medicine ID required']);
        exit;
    }
    
    require_once 'connection.php';
    
    // First check what columns exist
    $columnsStmt = $pdo->query("DESCRIBE Medicine_Dispensing");
    $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Use only columns that exist
    $selectColumns = ['dispensing_id', 'visit_id', 'medicine_id', 'quantity_dispensed'];
    $orderColumn = 'dispensing_id'; // Default order
    
    if (in_array('created_at', $columns)) {
        $selectColumns[] = 'created_at';
        $orderColumn = 'created_at';
    } elseif (in_array('date_created', $columns)) {
        $selectColumns[] = 'date_created';
        $orderColumn = 'date_created';
    } elseif (in_array('dispensed_at', $columns)) {
        $selectColumns[] = 'dispensed_at';
        $orderColumn = 'dispensed_at';
    }
    
    // Query with student names
    $sql = "SELECT md." . implode(', md.', $selectColumns) . ",
                   CASE 
                       WHEN cv.patient_type = 'Student' THEN 
                           CONCAT(s.Student_Fname, ' ', COALESCE(s.Student_Mname, ''), ' ', s.Student_Lname)
                       ELSE 'Non-Student Patient'
                   END as student_name
            FROM Medicine_Dispensing md
            LEFT JOIN clinic_visits cv ON md.visit_id = cv.visit_id
            LEFT JOIN Students s ON cv.patient_type = 'Student' AND cv.patient_id = s.Student_ID
            WHERE md.medicine_id = ? 
            ORDER BY md.$orderColumn DESC 
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medicineId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get medicine name
    $medicineStmt = $pdo->prepare("SELECT medicine_name, unit FROM Medicine_Inventory WHERE medicine_id = ?");
    $medicineStmt->execute([$medicineId]);
    $medicine = $medicineStmt->fetch(PDO::FETCH_ASSOC);
    
    // Get total
    $totalStmt = $pdo->prepare("SELECT SUM(quantity_dispensed) as total FROM Medicine_Dispensing WHERE medicine_id = ?");
    $totalStmt->execute([$medicineId]);
    $total = $totalStmt->fetchColumn() ?: 0;
    
    // Format data
    $data = [];
    foreach ($records as $record) {
        // Find the date column
        $dateValue = $record['created_at'] ?? $record['date_created'] ?? $record['dispensed_at'] ?? date('Y-m-d H:i:s');
        
        $data[] = [
            'dispensing_id' => $record['dispensing_id'],
            'visit_id' => $record['visit_id'],
            'medicine_id' => $record['medicine_id'],
            'quantity_dispensed' => $record['quantity_dispensed'],
            'dispensed_at' => $dateValue,
            'visit_date' => date('Y-m-d', strtotime($dateValue)),
            'medicine_name' => $medicine['medicine_name'] ?? 'Unknown',
            'unit' => $medicine['unit'] ?? 'units',
            'student_name' => $record['student_name'] ?? 'Unknown Student',
            'dispensed_by_name' => 'Staff',
            'reason_for_visit' => 'Medical consultation'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'medicine_id' => (int)$medicineId,
        'total_dispensed' => (int)$total,
        'count' => count($data)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'line' => $e->getLine()
    ]);
}
?>
