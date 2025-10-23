<?php
session_start();
require_once 'connection.php';

echo "<h2>Database Structure Analysis & Fix</h2>";

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>Please log in first</p>";
    exit();
}

$studentId = $_SESSION['user_id'];
echo "<p><strong>Analyzing database for Student ID:</strong> $studentId</p>";

// Check actual database structure
$tableStructures = [];

// Check students table
echo "<h3>1. Students Table Analysis</h3>";
try {
    $stmt = $pdo->query("DESCRIBE students");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tableStructures['students'] = $columns;
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
    foreach ($columns as $column) {
        echo "<tr><td>" . $column['Field'] . "</td><td>" . $column['Type'] . "</td><td>" . $column['Key'] . "</td></tr>";
    }
    echo "</table>";
    
    // Get sample student data
    $stmt = $pdo->prepare("SELECT * FROM students WHERE Student_ID = :student_id");
    $stmt->execute([':student_id' => $studentId]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($studentData) {
        echo "<h4>Your Student Record:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        foreach ($studentData as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Check sections table
echo "<h3>2. Sections Table Analysis</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'sections'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("DESCRIBE sections");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tableStructures['sections'] = $columns;
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
        foreach ($columns as $column) {
            echo "<tr><td>" . $column['Field'] . "</td><td>" . $column['Type'] . "</td><td>" . $column['Key'] . "</td></tr>";
        }
        echo "</table>";
        
        // Get sample sections
        $stmt = $pdo->query("SELECT * FROM sections LIMIT 5");
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($sections) {
            echo "<h4>Sample Sections:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            $headers = array_keys($sections[0]);
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th>$header</th>";
            }
            echo "</tr>";
            foreach ($sections as $section) {
                echo "<tr>";
                foreach ($section as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Sections table does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Check programs table
echo "<h3>3. Programs Table Analysis</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'programs'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("DESCRIBE programs");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tableStructures['programs'] = $columns;
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
        foreach ($columns as $column) {
            echo "<tr><td>" . $column['Field'] . "</td><td>" . $column['Type'] . "</td><td>" . $column['Key'] . "</td></tr>";
        }
        echo "</table>";
        
        // Get sample programs
        $stmt = $pdo->query("SELECT * FROM programs LIMIT 5");
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($programs) {
            echo "<h4>Sample Programs:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            $headers = array_keys($programs[0]);
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th>$header</th>";
            }
            echo "</tr>";
            foreach ($programs as $program) {
                echo "<tr>";
                foreach ($program as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Programs table does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Check departments table
echo "<h3>4. Departments Table Analysis</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'departments'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("DESCRIBE departments");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tableStructures['departments'] = $columns;
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
        foreach ($columns as $column) {
            echo "<tr><td>" . $column['Field'] . "</td><td>" . $column['Type'] . "</td><td>" . $column['Key'] . "</td></tr>";
        }
        echo "</table>";
        
        // Get sample departments
        $stmt = $pdo->query("SELECT * FROM departments LIMIT 5");
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($departments) {
            echo "<h4>Sample Departments:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            $headers = array_keys($departments[0]);
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th>$header</th>";
            }
            echo "</tr>";
            foreach ($departments as $department) {
                echo "<tr>";
                foreach ($department as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Departments table does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Generate corrected backend handler code
echo "<h3>5. Recommended Fix</h3>";
echo "<p>Based on your database structure, here's what needs to be fixed:</p>";

// Analyze the structure and provide recommendations
$recommendations = [];

// Check students table columns
$studentColumns = array_column($tableStructures['students'], 'Field');
if (in_array('program_id', $studentColumns)) {
    $recommendations[] = "✅ Students table has program_id column";
} else {
    $recommendations[] = "❌ Students table missing program_id column";
}

if (in_array('section_id', $studentColumns)) {
    $recommendations[] = "✅ Students table has section_id column";
} else {
    $recommendations[] = "❌ Students table missing section_id column";
}

// Check name columns
$nameColumns = ['Student_Fname', 'Student_Lname', 'Student_Mname'];
$hasNameColumns = array_intersect($nameColumns, $studentColumns);
if (count($hasNameColumns) >= 2) {
    $recommendations[] = "✅ Students table has name columns: " . implode(', ', $hasNameColumns);
} else {
    $recommendations[] = "❌ Students table missing proper name columns";
}

foreach ($recommendations as $rec) {
    echo "<p>$rec</p>";
}

echo "<hr>";
echo "<p><strong>Next Step:</strong> I'll now generate a corrected backend handler based on your actual database structure.</p>";
echo "<p><a href='student-dashboard.php'>← Back to Student Dashboard</a></p>";
?>
