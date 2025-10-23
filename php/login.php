<?php
include 'security.php';

// Start secure session
start_secure_session();

// Set security headers
set_security_headers();

// Redirect if already logged in
if (isset($_SESSION['logged_in'])) {
    $redirect = $_SESSION['is_superadmin'] ? 'superadmin-dashboard.php' : 'admin-dashboard.php';
    header("Location: $redirect");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    try {
        include 'connection.php';

        // Check in both Clinic_Staff and Head_Staff tables
        $queries = [
            // Check Clinic_Staff (regular admin)
            "SELECT clinic_email as email, clinic_password as password, 'clinic' as user_type,
                    clinic_Fname as fname, clinic_Lname as lname, staff_id as id
             FROM Clinic_Staff
             WHERE clinic_email = :username",

            // Check Head_Staff (superadmin)
            "SELECT head_email as email, head_password as password, 'head' as user_type,
                    head_Fname as fname, head_Lname as lname, head_id as id
             FROM Head_Staff
             WHERE head_email = :username"
        ];

        $user = null;
        $userType = null;

        foreach ($queries as $query) {
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $stored_password = $result['password'];
                $password_valid = false;

                // Check if hashed
                if (password_verify($password, $stored_password)) {
                    $password_valid = true;
                } elseif ($password === $stored_password) {
                    // Plain text match, rehash for security
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $updateTable = ($result['user_type'] === 'head') ? 'Head_Staff' : 'Clinic_Staff';
                    $pass_field = ($result['user_type'] === 'head') ? 'head_password' : 'clinic_password';
                    $id_field = ($result['user_type'] === 'head') ? 'head_id' : 'staff_id';

                    $update_stmt = $pdo->prepare("UPDATE $updateTable SET $pass_field = :hash WHERE $id_field = :id");
                    $update_stmt->bindParam(':hash', $new_hash, PDO::PARAM_STR);
                    $update_stmt->bindParam(':id', $result['id'], PDO::PARAM_INT);
                    $update_stmt->execute();

                    $password_valid = true;
                }

                if ($password_valid) {
                    $user = $result;
                    $userType = $result['user_type'];
                    break;
                }
            }
        }

        if ($user) {
            // Successful login
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;
            $_SESSION['is_superadmin'] = ($userType === 'head');
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['fname'] . ' ' . $user['lname'];
            $_SESSION['user_type'] = $userType;
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Update last login timestamp
            $updateTable = ($userType === 'head') ? 'Head_Staff' : 'Clinic_Staff';
            $idField = ($userType === 'head') ? 'head_id' : 'staff_id';

            $updateStmt = $pdo->prepare("UPDATE $updateTable SET last_login = NOW() WHERE $idField = :id");
            $updateStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $updateStmt->execute();

            // Log the login action
            include 'logging.php';
            logAdminAction('login', 'User logged into the system');

            // Set success flag for SweetAlert
            $_SESSION['login_success'] = true;
            $_SESSION['login_message'] = 'Welcome, ' . $_SESSION['full_name'] . '!';

            // Redirect based on admin type
            $redirect = $_SESSION['is_superadmin'] ? 'superadmin-dashboard.php' : 'admin-dashboard.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = "Invalid username or password";
            sleep(1);
        }

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = "System error. Please try again later.";
    }
}
?>
