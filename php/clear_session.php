<?php
/**
 * Clear Session and Cookies Utility
 * Use this page to clear all session data and cookies for a fresh start
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

// Also try to delete the custom session cookie name
setcookie('CLINICSESSID', '', time() - 42000, '/', '', false, true);

// Destroy the session
session_destroy();

// Clear any other potential cookies
$cookies = ['PHPSESSID', 'CLINICSESSID'];
foreach ($cookies as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, '', time() - 3600, '/');
        setcookie($cookie, '', time() - 3600, '/php/');
        setcookie($cookie, '', time() - 3600);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Cleared</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .success-icon {
            font-size: 60px;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .info {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Session Cleared Successfully</h1>
        <p>All session data and cookies have been cleared. You can now log in with a fresh session.</p>
        
        <a href="index.php" class="btn">Go to Login Page</a>
        
        <div class="info">
            <strong>What was cleared:</strong><br>
            • All session variables<br>
            • Session cookies (PHPSESSID, CLINICSESSID)<br>
            • CSRF tokens<br>
            • Login state
        </div>
    </div>
    
    <script>
        // Also clear any client-side storage
        if (typeof(Storage) !== "undefined") {
            localStorage.clear();
            sessionStorage.clear();
        }
        console.log("Session and cookies cleared successfully");
    </script>
</body>
</html>
