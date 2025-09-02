<?php
function getAllMedicines($search = '') {
    include 'connection.php';

    try {
        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT * FROM Medicine_Inventory WHERE medicine_name LIKE ? OR description LIKE ? ORDER BY created_at DESC");
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $pdo->query("SELECT * FROM Medicine_Inventory ORDER BY created_at DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>
