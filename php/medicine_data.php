<?php
function getAllMedicines($search = '') {
    include 'connection.php';

    try {
        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT medicine_id, medicine_name, description, quantity, unit, expiration_date FROM Medicine_Inventory WHERE medicine_name LIKE ? OR description LIKE ? ORDER BY created_at DESC");
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $pdo->prepare("SELECT medicine_id, medicine_name, description, quantity, unit, expiration_date FROM Medicine_Inventory ORDER BY medicine_name ASC");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'get-all':
            $medicines = getAllMedicines();
            header('Content-Type: application/json');
            echo json_encode($medicines);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
    exit;
}
?>
