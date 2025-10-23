<?php
// reset_passwords.php
include 'connection.php';

// Function to change password for a specific email
function changePasswordForEmail($email, $newPassword, $userType) {
    global $pdo;
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    try {
        if ($userType === 'clinic') {
            $stmt = $pdo->prepare("UPDATE Clinic_Staff SET clinic_password = ? WHERE clinic_email = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE Head_Staff SET head_password = ? WHERE head_email = ?");
        }
        
        $stmt->execute([$hashedPassword, $email]);
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        error_log("Password change error: " . $e->getMessage());
        return false;
    }
}

// Function to change password for all users
function changeAllPasswords($newPassword) {
    global $pdo;
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    try {
        // Update all Clinic_Staff passwords
        $stmt = $pdo->prepare("UPDATE Clinic_Staff SET clinic_password = ?");
        $stmt->execute([$hashedPassword]);
        $clinicCount = $stmt->rowCount();
        
        // Update all Head_Staff passwords
        $stmt = $pdo->prepare("UPDATE Head_Staff SET head_password = ?");
        $stmt->execute([$hashedPassword]);
        $headCount = $stmt->rowCount();
        
        return [
            'clinic_updated' => $clinicCount,
            'head_updated' => $headCount,
            'total_updated' => $clinicCount + $headCount
        ];
        
    } catch (PDOException $e) {
        error_log("Mass password change error: " . $e->getMessage());
        return false;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    
    if (empty($newPassword)) {
        $message = "Error: Password cannot be empty!";
    } else {
        if ($action === 'specific') {
            // Change password for specific email
            $email = $_POST['email'] ?? '';
            $userType = $_POST['user_type'] ?? '';
            
            if (empty($email) || empty($userType)) {
                $message = "Error: Email and user type are required!";
            } else {
                $success = changePasswordForEmail($email, $newPassword, $userType);
                if ($success) {
                    $message = "Password successfully updated for $email ($userType)";
                } else {
                    $message = "Error: Failed to update password for $email. User may not exist.";
                }
            }
        } elseif ($action === 'all') {
            // Change password for all users
            $result = changeAllPasswords($newPassword);
            if ($result) {
                $message = "Passwords updated for all users!<br>";
                $message .= "Clinic Staff updated: " . $result['clinic_updated'] . "<br>";
                $message .= "Head Staff updated: " . $result['head_updated'] . "<br>";
                $message .= "Total users updated: " . $result['total_updated'];
            } else {
                $message = "Error: Failed to update all passwords";
            }
        }
    }
}

// Get list of users for the dropdown
try {
    $clinicUsers = $pdo->query("SELECT clinic_email as email, 'clinic' as type FROM Clinic_Staff ORDER BY clinic_email")->fetchAll();
    $headUsers = $pdo->query("SELECT head_email as email, 'head' as type FROM Head_Staff ORDER BY head_email")->fetchAll();
    $allUsers = array_merge($clinicUsers, $headUsers);
} catch (PDOException $e) {
    $allUsers = [];
    error_log("Error fetching users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, button { padding: 8px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .tabs { display: flex; margin-bottom: 20px; }
        .tab { padding: 10px; background: #f0f0f0; cursor: pointer; margin-right: 5px; }
        .tab.active { background: #007bff; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Reset Tool</h1>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'Error:') === 0 ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <div class="tab active" onclick="showTab('specific')">Specific User</div>
            <div class="tab" onclick="showTab('all')">All Users</div>
        </div>

        <div id="specific-tab" class="tab-content active">
            <h2>Change Password for Specific User</h2>
            <form method="POST">
                <input type="hidden" name="action" value="specific">
                
                <div class="form-group">
                    <label for="email">Select User:</label>
                    <select name="email" id="email" required>
                        <option value="">-- Select User --</option>
                        <?php foreach ($allUsers as $user): ?>
                            <option value="<?php echo htmlspecialchars($user['email']); ?>">
                                <?php echo htmlspecialchars($user['email']); ?> (<?php echo $user['type']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_type">User Type:</label>
                    <select name="user_type" id="user_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="clinic">Clinic Staff</option>
                        <option value="head">Head Staff</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="new_password_specific">New Password:</label>
                    <input type="password" name="new_password" id="new_password_specific" required 
                           placeholder="Enter new password">
                </div>

                <button type="submit">Change Password</button>
            </form>
        </div>

        <div id="all-tab" class="tab-content">
            <h2>Change Password for All Users</h2>
            <form method="POST">
                <input type="hidden" name="action" value="all">
                
                <div class="form-group">
                    <label for="new_password_all">New Password (for all users):</label>
                    <input type="password" name="new_password" id="new_password_all" required 
                           placeholder="Enter new password for all users">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" required> 
                        I understand this will change ALL user passwords to the same value
                    </label>
                </div>

                <button type="submit" style="background-color: #dc3545; color: white;">Change All Passwords</button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            document.querySelector(`.tab:nth-child(${tabName === 'specific' ? 1 : 2})`).classList.add('active');
        }

        // Auto-set user type based on email selection
        document.getElementById('email').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.textContent.includes('(clinic)')) {
                document.getElementById('user_type').value = 'clinic';
            } else if (selectedOption.textContent.includes('(head)')) {
                document.getElementById('user_type').value = 'head';
            }
        });
    </script>
</body>
</html>