<?php
session_start();
include 'user-role.php';
include 'verifyer.php';
include 'connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    // Determine which table to query based on user type
    $table = '';
    $idField = '';
    $emailField = '';
    $fnameField = '';
    $lnameField = '';
    $role = '';

    if ($_SESSION['user_type'] === 'head') {
        $table = 'Head_Staff';
        $idField = 'head_id';
        $emailField = 'head_email';
        $fnameField = 'head_Fname';
        $lnameField = 'head_Lname';
        $role = 'Super Administrator';
    } else {
        $table = 'Clinic_Staff';
        $idField = 'staff_id';
        $emailField = 'clinic_email';
        $fnameField = 'clinic_Fname';
        $lnameField = 'clinic_Lname';
        $role = 'Clinic Administrator';
    }

    // Get user information from the appropriate table
    $stmt = $pdo->prepare("SELECT $idField as id, $emailField as email, $fnameField as fname, $lnameField as lname, last_login FROM $table WHERE $idField = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Format the response
        $response = [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $_SESSION['username'],
                'name' => trim($user['fname'] . ' ' . $user['lname']),
                'email' => $user['email'] ?: 'N/A',
                'role' => $role,
                'created_at' => 'N/A', // Creation date not stored in these tables
                'last_login' => $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'N/A'
            ]
        ];

        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }

} catch (PDOException $e) {
    error_log("Database error in get_profile.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
