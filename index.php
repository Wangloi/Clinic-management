<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $redirect = $_SESSION['is_superadmin'] ? 'php/superadmin-dashboard.php' : 'php/admin-dashboard.php';
    header("Location: $redirect");
    exit;
}
header("Location: php/index.php");
exit;
?>
