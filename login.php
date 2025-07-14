<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Redirect if already logged in
if (isset($_SESSION['username']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    
    $valid_user = "admin";
    $valid_pass_hash = password_hash("1234", PASSWORD_DEFAULT);

    if ($username === $valid_user && password_verify($password, $valid_pass_hash)) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid username or password";
        sleep(1); // Throttle brute force attempts
        header("Location: index.php"); // Redirect back to login page
        exit();
    }
}

// Check for error message from failed login
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Clear the error after displaying
}
?>