<?php include 'user-role.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="admin.css">
    </head>

    <?php
    

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true 
        || $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']
        || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        header("Location: index.php");
        exit();
    }
    ?>

    <body>
        <div class="logout-section">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </body>

</html>