<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Redirect if already logged in
if (isset($_SESSION['logged_in'])) {
    // Redirect based on admin type
    $redirect = $_SESSION['is_superadmin'] ? 'superadmin-dashboard.php' : 'admin-dashboard.php';
    header("Location: $redirect");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    
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
            
            if ($result && password_verify($password, $result['password'])) {
                $user = $result;
                $userType = $result['user_type'];
                break;
            }
        }
        
        if ($user) {
            // Successful login
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;
            $_SESSION['is_superadmin'] = ($userType === 'head'); // Head staff are superadmins
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['fname'] . ' ' . $user['lname'];
            $_SESSION['user_type'] = $userType;
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            
            // Update last login timestamp
            $updateTable = ($userType === 'head') ? 'Head_Staff' : 'Clinic_Staff';
            $idField = ($userType === 'head') ? 'head_id' : 'staff_id';
            
            $updateStmt = $pdo->prepare("UPDATE $updateTable SET last_login = NOW() WHERE $idField = :id");
            $updateStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $updateStmt->execute();
            
            // Redirect based on admin type
            $redirect = $_SESSION['is_superadmin'] ? 'superadmin-dashboard.php' : 'admin-dashboard.php';
            header("Location: $redirect");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid username or password";
            sleep(1); // Throttle brute force attempts
            header("Location: index.php");
            exit();
        }
        
    } catch (PDOException $e) {
        // Log the error (don't display to user for security)
        error_log("Database error: " . $e->getMessage());
        $_SESSION['login_error'] = "System error. Please try again later.";
        header("Location: index.php");
        exit();
    }
}

// Check for error message from failed login
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>