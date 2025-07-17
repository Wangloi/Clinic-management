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
    
    // Define admin credentials (store securely in production)
    $admins = [
        'superadmin' => [
            'password' => password_hash('1234', PASSWORD_DEFAULT),
            'is_superadmin' => true
        ],
        'admin' => [
            'password' => password_hash('1234', PASSWORD_DEFAULT),
            'is_superadmin' => false
        ]
    ];

    if (array_key_exists($username, $admins) && password_verify($password, $admins[$username]['password'])) {
        // Successful login
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;
        $_SESSION['is_superadmin'] = $admins[$username]['is_superadmin'];
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
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
}

// Check for error message from failed login
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>