<?php
include 'connection.php';

// Fetch user profile data
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    if ($user_type === 'clinic') {
        $stmt = $pdo->prepare("SELECT clinic_email as email, clinic_Fname as fname, clinic_Mname as mname, clinic_Lname as lname, contact_number FROM Clinic_Staff WHERE staff_id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT head_email as email, head_Fname as fname, head_Mname as mname, head_Lname as lname FROM Head_Staff WHERE head_id = ?");
    }
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
