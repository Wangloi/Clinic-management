<?php include 'login.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login | Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
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


                <form action="index.php" method="POST" class="login-form" id="loginForm">
                    <div class="error-message" id="errorContainer" style="display: none;"></div>

                    <div class="form-group">
                        <label for="username" class="form-label">Email</label>
                        <input type="email" id="username" class="form-input user" name="username" required placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-input pass" name="password" required placeholder="Enter your password">
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