<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';

/**
 * Log an admin action
 * @param string $action The action performed (add, edit, delete)
 * @param string $entity The entity type (user, student, etc.) - optional
 * @param int $entity_id The ID of the entity - optional
 * @param string $details Additional details about the action
 */
function logAdminAction($action, $entity = '', $entity_id = null, $details = '') {
    global $pdo;

    // Determine user_type based on session
    $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'Clinic_Staff';
    // Convert 'head' to 'Head_Staff' and 'clinic' to 'Clinic_Staff' for compatibility
    if ($user_type === 'head') {
        $user_type = 'Head_Staff';
    } elseif ($user_type === 'clinic') {
        $user_type = 'Clinic_Staff';
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if not set
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $admin_username = isset($_SESSION['username']) ? $_SESSION['username'] : 'unknown';

    // Build details with entity information if provided
    $full_details = $details;
    if (!empty($entity)) {
        $full_details = "Entity: $entity";
        if ($entity_id !== null) {
            $full_details .= " (ID: $entity_id)";
        }
        $full_details .= " - " . $details;
    }

    // Append admin username to details
    $details_with_user = $full_details . " (Performed by: $admin_username)";

    try {
        $stmt = $pdo->prepare("
            INSERT INTO System_Logs (user_type, user_id, action, details, ip_address, timestamp)
            VALUES (:user_type, :user_id, :action, :details, :ip_address, NOW())
        ");

        $stmt->execute([
            ':user_type' => $user_type,
            ':user_id' => $user_id,
            ':action' => $action,
            ':details' => $details_with_user,
            ':ip_address' => $ip_address
        ]);

        return true;
    } catch (PDOException $e) {
        // Log error but don't fail the login process
        error_log("Error logging admin action: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all system logs
 * @param int $limit Number of logs to retrieve
 * @return array Array of log entries
 */
function getSystemLogs($limit = 100) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT * FROM System_Logs
            ORDER BY timestamp DESC
            LIMIT :limit
        ");

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error retrieving system logs: " . $e->getMessage());
        return [];
    }
}

/**
 * Get logs for a specific entity type
 * @param string $entity The entity type (student, visit, medicine)
 * @param int $limit Number of logs to retrieve
 * @return array Array of log entries
 */
function getLogsByEntity($entity, $limit = 50) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT * FROM System_Logs
            WHERE details LIKE :entity
            ORDER BY timestamp DESC
            LIMIT :limit
        ");

        $likeEntity = '%' . $entity . '%';
        $stmt->bindParam(':entity', $likeEntity, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error retrieving logs by entity: " . $e->getMessage());
        return [];
    }
}

/**
 * Get logs for a specific admin
 * @param string $admin_username The admin username
 * @param int $limit Number of logs to retrieve
 * @return array Array of log entries
 */
function getLogsByAdmin($admin_username, $limit = 50) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT * FROM System_Logs
            WHERE details LIKE :admin_username
            ORDER BY timestamp DESC
            LIMIT :limit
        ");

        $likeUsername = '%(Performed by: ' . $admin_username . ')%';
        $stmt->bindParam(':admin_username', $likeUsername, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error retrieving logs by admin: " . $e->getMessage());
        return [];
    }
}

/**
 * Create the system_logs table if it doesn't exist
 */
function createLogsTable() {
    global $pdo;

    try {
        $sql = "
            CREATE TABLE IF NOT EXISTS System_Logs (
                log_id INT PRIMARY KEY AUTO_INCREMENT,
                user_type ENUM('Clinic_Staff', 'Head_Staff') NOT NULL,
                user_id INT NOT NULL,
                action VARCHAR(255) NOT NULL,
                details TEXT,
                ip_address VARCHAR(45),
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Error creating logs table: " . $e->getMessage());
        return false;
    }
}

// Create the table when this file is included
createLogsTable();


?>
