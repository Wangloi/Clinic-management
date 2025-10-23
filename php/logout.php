<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine redirect based on user type before destroying session
$redirect = 'student-login.php'; // Default to student login
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'student') {
        $redirect = 'student-login.php';
    } else {
        $redirect = 'index.php'; // Admin login (same directory)
    }
}

// Try to log the logout action (but don't let it break logout if it fails)
try {
    if (isset($_SESSION['logged_in'])) {
        include_once 'logging.php';
        $user_name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Unknown User';
        $user_type = $_SESSION['user_type'] ?? 'unknown';
        logAdminAction('logout', "User logged out: $user_name (Type: $user_type)");
    }
} catch (Exception $e) {
    // Continue with logout even if logging fails
    error_log("Logout logging failed: " . $e->getMessage());
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

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to appropriate page
header("Location: $redirect");
exit();
?>
