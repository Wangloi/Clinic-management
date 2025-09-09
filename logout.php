<?php
session_start();

// Log the logout action before destroying session
if (isset($_SESSION['logged_in'])) {
    include 'logging.php';
    logAdminAction('logout', 'User logged out of the system');
}

// Unset all session variables
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to welcome page
header("Location: index.php");
exit();
?>
