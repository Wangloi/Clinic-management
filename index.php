<?php include 'login.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <div class="login">
                <div class="layer-1">
                    <div class="black-BG">
                        <div class="Logos">
                            <div class="SRCB-Logo">
                                <img src="images/SRCB.png" alt="SRCB Logo">
                            </div>
                            <div class="Example-Logo">
                                <img src="images/SFASS.png" alt="example Logo">
                            </div>
                        </div>
                            <br><br><br><br>
                        <div class="Text-1">Hello,</div>
                        <div class="Text-2">Welcome!</div>
                        <div class="mess-1">
                            Integrated Digital Clinic 
                            <br>Management System of
                            <br>St. Rita’s College of 
                            Balingasag
                        </div>
                    </div>
                </div>
                <div class="layer-2">
                    <div class="mess-2">
                        <div class="Text-3">Login</div>
                        <div class="Text-4">Ready to help students today? Log in!</div>
                    </div>

                    <form action="login.php" method="POST" class="login-form">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" class="form-input user" name="username" required placeholder="Enter your username">
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
            </div>
        </div>
    </body>

</html>