<?php
require_once 'connection.php';

echo "<h2>Fix Medicine Dispensing Table Structure</h2>";
echo "<pre>";

try {
    // Check if table exists and its current structure
    echo "=== CHECKING CURRENT TABLE ===\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'Medicine_Dispensing'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Medicine_Dispensing table exists\n";
        
        // Show current structure
        $stmt = $pdo->query("DESCRIBE Medicine_Dispensing");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Current columns:\n";
        foreach ($columns as $col) {
            echo "- {$col['Field']} ({$col['Type']})\n";
        }
        
        // Check if we need to rename columns
        $columnNames = array_column($columns, 'Field');
        $needsUpdate = false;
        
        if (in_array('dispense_id', $columnNames) && !in_array('dispensing_id', $columnNames)) {
            echo "\n⚠️  Need to rename 'dispense_id' to 'dispensing_id'\n";
            $needsUpdate = true;
        }
        
        if (in_array('created_at', $columnNames) && !in_array('dispensed_at', $columnNames)) {
            echo "⚠️  Need to rename 'created_at' to 'dispensed_at'\n";
            $needsUpdate = true;
        }
        
        if ($needsUpdate) {
            echo "\n=== UPDATING TABLE STRUCTURE ===\n";
            
            // Rename columns to match system expectations
            if (in_array('dispense_id', $columnNames)) {
                $pdo->exec("ALTER TABLE Medicine_Dispensing CHANGE dispense_id dispensing_id INT PRIMARY KEY AUTO_INCREMENT");
                echo "✅ Renamed 'dispense_id' to 'dispensing_id'\n";
            }
            
            if (in_array('created_at', $columnNames)) {
                $pdo->exec("ALTER TABLE Medicine_Dispensing CHANGE created_at dispensed_at DATETIME DEFAULT CURRENT_TIMESTAMP");
                echo "✅ Renamed 'created_at' to 'dispensed_at'\n";
            }
            
            // Show updated structure
            echo "\n=== UPDATED TABLE STRUCTURE ===\n";
            $stmt = $pdo->query("DESCRIBE Medicine_Dispensing");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $col) {
                echo "- {$col['Field']} ({$col['Type']})\n";
            }
        } else {
            echo "\n✅ Table structure is already correct\n";
        }
        
    } else {
        echo "❌ Medicine_Dispensing table does not exist\n";
        echo "Creating table with correct structure...\n";
        
        $createSql = "CREATE TABLE Medicine_Dispensing (
            dispensing_id INT PRIMARY KEY AUTO_INCREMENT,
            visit_id INT,
            medicine_id INT,
            quantity_dispensed INT,
            dispensed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (visit_id) REFERENCES clinic_visits(visit_id),
            FOREIGN KEY (medicine_id) REFERENCES Medicine_Inventory(medicine_id)
        )";
        
        $pdo->exec($createSql);
        echo "✅ Medicine_Dispensing table created with correct structure\n";
    }
    
    // Check record count
    echo "\n=== CHECKING RECORDS ===\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Medicine_Dispensing");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Total dispensing records: $count\n";
    
    if ($count > 0) {
        echo "\n=== RECENT DISPENSING RECORDS ===\n";
        $stmt = $pdo->query("SELECT * FROM Medicine_Dispensing ORDER BY dispensed_at DESC LIMIT 5");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($records as $record) {
            echo "ID: {$record['dispensing_id']}, Visit: {$record['visit_id']}, ";
            echo "Medicine: {$record['medicine_id']}, Quantity: {$record['quantity_dispensed']}\n";
        }
    }
    
    echo "\n✅ Medicine_Dispensing table is now ready for the system!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='populate-medication-history.php'>→ Now Setup History Tracking</a></p>";
echo "<p><a href='admin-medication.php'>← Back to Medication Management</a></p>";
?>
