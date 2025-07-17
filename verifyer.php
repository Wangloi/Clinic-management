    <?php

        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true 
            || $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']
            || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            header("Location: index.php");
            exit();
    }
    ?>