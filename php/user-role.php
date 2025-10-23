<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../index.php");
    exit();
}

// Superadmin dashboard specific check
if (basename($_SERVER['PHP_SELF']) === 'superadmin-dashboard.php' && !$_SESSION['is_superadmin']) {
    header("Location: admin-dashboard.php");
    exit();
}

// Superadmin account page specific check
if (basename($_SERVER['PHP_SELF']) === 'superadmin-account.php' && !$_SESSION['is_superadmin']) {
    header("Location: admin-dashboard.php");
    exit();
}

// Admin dashboard specific check
if (basename($_SERVER['PHP_SELF']) === 'admin-dashboard.php' && $_SESSION['is_superadmin']) {
    header("Location: superadmin-dashboard.php");
    exit();
}

// Function to check if current user is superadmin
if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin() {
        return isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] === true;
    }
}
?>