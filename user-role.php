<?php
session_start();

// Check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

// Superadmin dashboard specific check
if (basename($_SERVER['PHP_SELF']) === 'superadmin-dashboard.php' && !$_SESSION['is_superadmin']) {
    header("Location: admin-dashboard.php");
    exit();
}

// Admin dashboard specific check
if (basename($_SERVER['PHP_SELF']) === 'admin-dashboard.php' && $_SESSION['is_superadmin']) {
    header("Location: superadmin-dashboard.php");
    exit();
}
?>