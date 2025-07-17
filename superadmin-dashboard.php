<?php include 'user-role.php'; ?>
<?php include 'verifyer.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Superadmin | Integrated Digital Clinic Management System of St. Rita's College of Balingasag</title>
        <link rel="stylesheet" href="superadmin.css">
    </head>


    <body>
        <div class="logout-section">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </body>

</html>