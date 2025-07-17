<?php include 'user-role.php'; ?>
<?php include 'verifer.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin| Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="admin.css">
    </head>

    

    <body>
        <div class="logout-section">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </body>

</html>