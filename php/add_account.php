<?php
session_start();
include 'connection.php';

$ajax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

if ($ajax) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
        exit();
    }
} else {
    include 'verifyer.php';
}

include 'user-role.php';

// Check if user is superadmin
if (!isSuperAdmin()) {
    if ($ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    } else {
        header('Location: /Clinic-management/php/superadmin-account.php?error=' . urlencode('Unauthorized access'));
        exit();
    }
}

try {
    // Log that the script was accessed
    file_put_contents('../debug.log', "add_account.php accessed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    // Log POST data for debugging
    file_put_contents('../debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate form data
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($role) || empty($password)) {
        throw new Exception('All fields are required');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Validate role
    if (!in_array($role, ['Clinic Staff', 'Superadmin'])) {
        throw new Exception('Invalid role selected');
    }

    // Check if email already exists in both tables
    $check_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM (
            SELECT clinic_email as email FROM Clinic_Staff
            UNION ALL
            SELECT head_email as email FROM Head_Staff
        ) AS accounts WHERE email = ?
    ");
    $check_stmt->execute([$email]);
    if ($check_stmt->fetchColumn() > 0) {
        throw new Exception('Email already exists');
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Determine table and fields based on role
    if ($role === 'Clinic Staff') {
        $table = 'Clinic_Staff';
        $email_field = 'clinic_email';
        $fname_field = 'clinic_Fname';
        $mname_field = 'clinic_Mname';
        $lname_field = 'clinic_Lname';
        $contact_field = 'contact_number';
        $pass_field = 'clinic_password';

        $insert_stmt = $pdo->prepare("
            INSERT INTO $table ($email_field, $fname_field, $mname_field, $lname_field, $contact_field, $pass_field)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $success = $insert_stmt->execute([
            $email,
            $first_name,
            $middle_name,
            $last_name,
            $contact_number,
            $hashed_password
        ]);
    } else {
        $table = 'Head_Staff';
        $email_field = 'head_email';
        $fname_field = 'head_Fname';
        $mname_field = 'head_Mname';
        $lname_field = 'head_Lname';
        $pass_field = 'head_password';

        $insert_stmt = $pdo->prepare("
            INSERT INTO $table ($email_field, $fname_field, $mname_field, $lname_field, $pass_field)
            VALUES (?, ?, ?, ?, ?)
        ");

        $success = $insert_stmt->execute([
            $email,
            $first_name,
            $middle_name,
            $last_name,
            $hashed_password
        ]);
    }

    if (!$success) {
        throw new Exception('Failed to add account to database');
    }

    // Log the action
    include 'logging.php';
    $account_name = $first_name . ' ' . $last_name;
    logAdminAction('add', "Added account: $account_name ($email) as $role");

    file_put_contents('../debug.log', "Account added successfully to $table\n", FILE_APPEND);

    // Return response
    if ($ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Account added successfully']);
    } else {
        header('Location: /Clinic-management/php/superadmin-account.php?success=' . urlencode('Account added successfully'));
    }

} catch (Exception $e) {
    file_put_contents('../debug.log', "Account add failed: " . $e->getMessage() . "\n", FILE_APPEND);

    // Return error response
    if ($ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } else {
        header('Location: /Clinic-management/php/superadmin-account.php?error=' . urlencode($e->getMessage()));
    }
}
?>
