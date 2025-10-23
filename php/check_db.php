<?php
include 'connection.php';

try {
    // Check if appointments table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
    $result = $stmt->fetch();
    echo "Total appointments: " . $result['count'] . "\n";

    if ($result['count'] > 0) {
        // Get a sample appointment with joins
        $stmt = $pdo->query("
            SELECT
                a.appointment_id,
                a.section_id,
                s.section_name,
                a.appointment_date,
                a.start_time,
                a.end_time,
                a.reason
            FROM appointments a
            LEFT JOIN sections s ON a.section_id = s.section_id
            LIMIT 5
        ");
        $appointments = $stmt->fetchAll();

        echo "\nSample appointments:\n";
        foreach ($appointments as $apt) {
            echo "ID: {$apt['appointment_id']}, Section ID: {$apt['section_id']}, Section Name: {$apt['section_name']}, Date: {$apt['appointment_date']}\n";
        }
    }

    // Check sections table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sections");
    $result = $stmt->fetch();
    echo "\nTotal sections: " . $result['count'] . "\n";

    if ($result['count'] > 0) {
        $stmt = $pdo->query("SELECT section_id, section_name FROM sections LIMIT 5");
        $sections = $stmt->fetchAll();
        echo "\nSample sections:\n";
        foreach ($sections as $sec) {
            echo "ID: {$sec['section_id']}, Name: {$sec['section_name']}\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
