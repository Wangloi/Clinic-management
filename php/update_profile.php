<?php
include 'connection.php';
include 'verifyer.php';
include 'user-role.php';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    // Get and validate form data
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email)) {
        throw new Exception('First name, last name, and email are required');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if email already exists for another user
    if ($user_type === 'clinic') {
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM Clinic_Staff WHERE clinic_email = ? AND staff_id != ?");
        $check_stmt->execute([$email, $user_id]);
    } else {
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM Head_Staff WHERE head_email = ? AND head_id != ?");
        $check_stmt->execute([$email, $user_id]);
    }
    if ($check_stmt->fetchColumn() > 0) {
        throw new Exception('Email already exists');
    }

    // Handle password change
    $update_password = false;
    if (!empty($new_password)) {
        if (empty($current_password)) {
            throw new Exception('Current password is required to change password');
        }

        if ($new_password !== $confirm_password) {
            throw new Exception('New passwords do not match');
        }

        // Validate new password strength
        $hasUpperCase = preg_match('/[A-Z]/', $new_password);
        $hasLowerCase = preg_match('/[a-z]/', $new_password);
        $hasNumbers = preg_match('/\d/', $new_password);
        $hasSpecialChar = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password);
        $isLongEnough = strlen($new_password) >= 8;

        if (!$hasUpperCase || !$hasLowerCase || !$hasNumbers || !$hasSpecialChar || !$isLongEnough) {
            throw new Exception('New password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
        }

        // Verify current password
        if ($user_type === 'clinic') {
            $stmt = $pdo->prepare("SELECT clinic_password FROM Clinic_Staff WHERE staff_id = ?");
        } else {
            $stmt = $pdo->prepare("SELECT head_password FROM Head_Staff WHERE head_id = ?");
        }
        $stmt->execute([$user_id]);
        $current_hash = $stmt->fetchColumn();

        if (!password_verify($current_password, $current_hash)) {
            throw new Exception('Current password is incorrect');
        }

        $update_password = true;
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
    }

    // Update user data
    if ($user_type === 'clinic') {
        $table = 'Clinic_Staff';
        $email_field = 'clinic_email';
        $fname_field = 'clinic_Fname';
        $mname_field = 'clinic_Mname';
        $lname_field = 'clinic_Lname';
        $contact_field = 'contact_number';
        $pass_field = 'clinic_password';
        $id_field = 'staff_id';

        if ($update_password) {
            $update_stmt = $pdo->prepare("
                UPDATE $table SET $email_field = ?, $fname_field = ?, $mname_field = ?, $lname_field = ?, $contact_field = ?, $pass_field = ?
                WHERE $id_field = ?
            ");
            $success = $update_stmt->execute([
                $email,
                $first_name,
                $middle_name,
                $last_name,
                $contact_number,
                $hashed_new_password,
                $user_id
            ]);
        } else {
            $update_stmt = $pdo->prepare("
                UPDATE $table SET $email_field = ?, $fname_field = ?, $mname_field = ?, $lname_field = ?, $contact_field = ?
                WHERE $id_field = ?
            ");
            $success = $update_stmt->execute([
                $email,
                $first_name,
                $middle_name,
                $last_name,
                $contact_number,
                $user_id
            ]);
        }
    } else {
        $table = 'Head_Staff';
        $email_field = 'head_email';
        $fname_field = 'head_Fname';
        $mname_field = 'head_Mname';
        $lname_field = 'head_Lname';
        $pass_field = 'head_password';
        $id_field = 'head_id';

        if ($update_password) {
            $update_stmt = $pdo->prepare("
                UPDATE $table SET $email_field = ?, $fname_field = ?, $mname_field = ?, $lname_field = ?, $pass_field = ?
                WHERE $id_field = ?
            ");
            $success = $update_stmt->execute([
                $email,
                $first_name,
                $middle_name,
                $last_name,
                $hashed_new_password,
                $user_id
            ]);
        } else {
            $update_stmt = $pdo->prepare("
                UPDATE $table SET $email_field = ?, $fname_field = ?, $mname_field = ?, $lname_field = ?
                WHERE $id_field = ?
            ");
            $success = $update_stmt->execute([
                $email,
                $first_name,
                $middle_name,
                $last_name,
                $user_id
            ]);
        }
    }

    if (!$success) {
        throw new Exception('Failed to update profile');
    }

    // Update session data if email or name changed
    $_SESSION['username'] = $email;
    $_SESSION['full_name'] = $first_name . ' ' . $last_name;

    // Log the action
    include 'logging.php';
    logAdminAction('update', "Updated own profile");

    // Redirect back with success message
    header('Location: admin-dashboard.php?profile_updated=1');

} catch (Exception $e) {
    // Redirect back with error message
    header('Location: admin-dashboard.php?error=' . urlencode($e->getMessage()));
}
?>
