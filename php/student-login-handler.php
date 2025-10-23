<?php
// Student Login Success Handler
// This file handles the login success session management

function checkLoginSuccess() {
    $loginSuccess = false;
    
    if (isset($_SESSION['login_success']) && $_SESSION['login_success']) {
        $loginSuccess = true;
        
        // Clear the session variables after checking
        unset($_SESSION['login_success']);
        unset($_SESSION['login_message']);
    }
    
    return $loginSuccess;
}

function getLoginSuccessScript($studentData) {
    $loginSuccess = checkLoginSuccess();
    
    if ($loginSuccess) {
        return "window.showLoginAlert = true;";
    }
    
    return "";
}

// Generate the JavaScript for login success
function generateLoginSuccessJS($fullName, $studentId) {
    $loginSuccess = checkLoginSuccess();
    
    $script = "
    // Add login success data to existing studentData (don't overwrite)
    if (window.studentData) {
        window.studentData.fullName = " . json_encode($fullName) . ";
        window.studentData.studentId = " . json_encode($studentId) . ";
    } else {
        window.studentData = {
            fullName: " . json_encode($fullName) . ",
            studentId: " . json_encode($studentId) . "
        };
    }
    ";
    
    if ($loginSuccess) {
        $script .= "
        // Set flag for login success alert
        window.showLoginAlert = true;
        ";
    }
    
    return $script;
}
?>
