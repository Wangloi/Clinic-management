<?php
require_once 'connection.php';

echo "<h2>Medication History Debug</h2>";
echo "<pre>";

try {
    // Check if history table exists
    echo "=== CHECKING HISTORY TABLE ===\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'Medication_Dispensing_History'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Medication_Dispensing_History table EXISTS\n";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE Medication_Dispensing_History");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Table columns:\n";
        foreach ($columns as $col) {
            echo "- {$col['Field']} ({$col['Type']})\n";
        }
        
        // Check record count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Medication_Dispensing_History");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nTotal history records: " . $count['count'] . "\n";
        
        if ($count['count'] > 0) {
            echo "\n=== RECENT HISTORY RECORDS ===\n";
            $stmt = $pdo->query("SELECT * FROM Medication_Dispensing_History ORDER BY dispensed_at DESC LIMIT 5");
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($records as $record) {
                echo "ID: {$record['history_id']}, Medicine: {$record['medicine_name']}, ";
                echo "Student: {$record['student_name']}, Quantity: {$record['quantity_dispensed']}\n";
            }
        }
        
    } else {
        echo "❌ Medication_Dispensing_History table DOES NOT EXIST\n";
    }
    
    // Check Medicine_Dispensing table
    echo "\n=== CHECKING DISPENSING TABLE ===\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'Medicine_Dispensing'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Medicine_Dispensing table EXISTS\n";
        
        // Check recent dispensing records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Medicine_Dispensing");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total dispensing records: " . $count['count'] . "\n";
        
        if ($count['count'] > 0) {
            echo "\n=== RECENT DISPENSING RECORDS ===\n";
            $stmt = $pdo->query("SELECT * FROM Medicine_Dispensing ORDER BY dispensed_at DESC LIMIT 5");
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($records as $record) {
                echo "Dispensing ID: {$record['dispensing_id']}, Visit ID: {$record['visit_id']}, ";
                echo "Medicine ID: {$record['medicine_id']}, Quantity: {$record['quantity_dispensed']}\n";
            }
        }
        
    } else {
        echo "❌ Medicine_Dispensing table DOES NOT EXIST\n";
    }
    
    // Check if trigger exists
    echo "\n=== CHECKING TRIGGER ===\n";
    $stmt = $pdo->query("SHOW TRIGGERS LIKE 'after_medication_dispensing_insert'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Trigger EXISTS\n";
    } else {
        echo "❌ Trigger DOES NOT EXIST\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='simple-create-history-table.php'>→ Create History Table</a></p>";
echo "<p><a href='admin-medication.php'>← Back to Medication Management</a></p>";
?>
