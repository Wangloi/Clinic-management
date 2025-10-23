<?php
/**
 * Create Medication Dispensing History Table
 * This script creates a comprehensive table to track all medication dispensing history
 */

require_once 'connection.php';

echo "<h2>Creating Medication Dispensing History Table</h2>";
echo "<pre>";

try {
    // Create the medication dispensing history table
    $sql = "CREATE TABLE IF NOT EXISTS Medication_Dispensing_History (
        history_id INT PRIMARY KEY AUTO_INCREMENT,
        dispensing_id INT,
        visit_id INT,
        student_id INT,
        medicine_id INT,
        medicine_name VARCHAR(255),
        quantity_dispensed INT NOT NULL,
        unit VARCHAR(50),
        dispensed_by INT,
        dispensed_by_name VARCHAR(255),
        dispensed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        visit_date DATE,
        student_name VARCHAR(255),
        reason_for_visit TEXT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        INDEX idx_student_id (student_id),
        INDEX idx_medicine_id (medicine_id),
        INDEX idx_dispensed_at (dispensed_at),
        INDEX idx_visit_id (visit_id),
        
        FOREIGN KEY (visit_id) REFERENCES clinic_visits(visit_id) ON DELETE SET NULL,
        FOREIGN KEY (student_id) REFERENCES Students(Student_ID) ON DELETE SET NULL,
        FOREIGN KEY (medicine_id) REFERENCES Medicine_Inventory(medicine_id) ON DELETE SET NULL
    )";
    
    $pdo->exec($sql);
    echo "✅ Medication_Dispensing_History table created successfully\n\n";
    
    // Create a trigger to automatically populate history when medication is dispensed
    $triggerSql = "
    CREATE TRIGGER IF NOT EXISTS after_medication_dispensing_insert
    AFTER INSERT ON Medicine_Dispensing
    FOR EACH ROW
    BEGIN
        INSERT INTO Medication_Dispensing_History (
            dispensing_id,
            visit_id,
            student_id,
            medicine_id,
            medicine_name,
            quantity_dispensed,
            unit,
            dispensed_by,
            dispensed_by_name,
            visit_date,
            student_name,
            reason_for_visit
        )
        SELECT 
            NEW.dispensing_id,
            NEW.visit_id,
            cv.student_id,
            NEW.medicine_id,
            mi.medicine_name,
            NEW.quantity_dispensed,
            mi.unit,
            cv.attended_by,
            CONCAT(cs.clinic_Fname, ' ', cs.clinic_Lname),
            cv.visit_date,
            CONCAT(s.Student_Fname, ' ', COALESCE(s.Student_Mname, ''), ' ', s.Student_Lname),
            cv.reason_for_visit
        FROM clinic_visits cv
        LEFT JOIN Students s ON cv.student_id = s.Student_ID
        LEFT JOIN Medicine_Inventory mi ON NEW.medicine_id = mi.medicine_id
        LEFT JOIN Clinic_Staff cs ON cv.attended_by = cs.staff_id
        WHERE cv.visit_id = NEW.visit_id;
    END";
    
    $pdo->exec($triggerSql);
    echo "✅ Trigger for automatic history logging created successfully\n\n";
    
    // Populate existing data (if any)
    $populateSql = "
    INSERT INTO Medication_Dispensing_History (
        dispensing_id,
        visit_id,
        student_id,
        medicine_id,
        medicine_name,
        quantity_dispensed,
        unit,
        dispensed_by,
        dispensed_by_name,
        dispensed_at,
        visit_date,
        student_name,
        reason_for_visit
    )
    SELECT 
        md.dispensing_id,
        md.visit_id,
        cv.student_id,
        md.medicine_id,
        mi.medicine_name,
        md.quantity_dispensed,
        mi.unit,
        cv.attended_by,
        CONCAT(COALESCE(cs.clinic_Fname, ''), ' ', COALESCE(cs.clinic_Lname, '')),
        md.dispensed_at,
        cv.visit_date,
        CONCAT(s.Student_Fname, ' ', COALESCE(s.Student_Mname, ''), ' ', s.Student_Lname),
        cv.reason_for_visit
    FROM Medicine_Dispensing md
    LEFT JOIN clinic_visits cv ON md.visit_id = cv.visit_id
    LEFT JOIN Students s ON cv.student_id = s.Student_ID
    LEFT JOIN Medicine_Inventory mi ON md.medicine_id = mi.medicine_id
    LEFT JOIN Clinic_Staff cs ON cv.attended_by = cs.staff_id
    WHERE NOT EXISTS (
        SELECT 1 FROM Medication_Dispensing_History mdh 
        WHERE mdh.dispensing_id = md.dispensing_id
    )";
    
    $stmt = $pdo->prepare($populateSql);
    $stmt->execute();
    $populated = $stmt->rowCount();
    
    echo "✅ Populated $populated existing dispensing records into history\n\n";
    
    // Show table structure
    echo "=== Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE Medication_Dispensing_History");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-25s %-20s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'] == 'YES' ? 'NULL' : 'NOT NULL'
        );
    }
    
    // Show sample data count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Medication_Dispensing_History");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nTotal history records: " . $count['count'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!</pre>";
echo "<hr>";
echo "<p><a href='admin-medication.php'>← Back to Medication Management</a></p>";
?>
