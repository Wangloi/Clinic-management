<?php
include 'security.php';

// Start secure session
start_secure_session();

// Set security headers
set_security_headers();

// Redirect if already logged in as student
if (isset($_SESSION['logged_in']) && $_SESSION['user_type'] === 'student') {
    header("Location: student-dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');

    if (empty($student_id)) {
        $error = "Please enter your student ID";
    } else {
        try {
            include 'connection.php';

            // Check if student exists
            $stmt = $pdo->prepare("
                SELECT s.*, p.program_name, d.department_level, sec.section_name
                FROM Students s
                LEFT JOIN Sections sec ON s.section_id = sec.section_id
                LEFT JOIN Programs p ON sec.program_id = p.program_id
                LEFT JOIN Departments d ON p.department_id = d.department_id
                WHERE s.student_id = :student_id
            ");
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
            $stmt->execute();

            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                // Successful "login"
                $_SESSION['username'] = $student_id;
                $_SESSION['logged_in'] = true;
                $_SESSION['is_superadmin'] = false; // Students are not superadmin
                $_SESSION['user_id'] = $student['student_id'];
                $_SESSION['full_name'] = $student['Student_Fname'] . ' ' . ($student['Student_Mname'] ? $student['Student_Mname'] . ' ' : '') . $student['Student_Lname'];
                $_SESSION['user_type'] = 'student';
                $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

                // Log the login action
                include 'logging.php';
                logAdminAction('student_login', 'Student logged into the system: ' . $_SESSION['full_name']);

                // Set success flag for SweetAlert
                $_SESSION['login_success'] = true;
                $_SESSION['login_message'] = 'Welcome, ' . $_SESSION['full_name'] . '!';

                // Redirect to student dashboard
                header("Location: student-dashboard.php");
                exit();
            } else {
                $error = "Invalid student ID";
                sleep(1);
            }

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "System error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Login | Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="../css/style.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            <?php if (!empty($error)): ?>
            window.loginError = <?php echo json_encode($error); ?>;
            <?php endif; ?>
        </script>
    </head>

    <body>
        <div class="context">
            <div class="Logos">
                <div class="SRCB-logo"><img src="../images/SRCB.png" alt="SRCB logo"></div>
                <div class="clinic-logo"><img src="../images/clinic.png" alt="clinic logo"></div>
            </div>
            <div class="SRCB-clinic">SRCB Clinic</div>

            <div class="login">
                <div id="admin-error-message" style="display: none;"><?php echo isset($error) ? htmlspecialchars($error) : ''; ?></div>


                <form action="student-login.php" method="POST" class="login-form" id="studentLoginForm">
                    <div class="error-message" id="errorContainer" style="display: none;"></div>

                    <div class="form-group">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" id="student_id" class="form-input user" name="student_id" required placeholder="Enter your student ID">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="login-button">Login</button>
                    </div>
                </form>
            </div>
            <script src="../success-mess.js"></script>
        </div>
    </body>
</html>
