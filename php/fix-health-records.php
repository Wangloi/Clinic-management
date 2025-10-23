<?php
/**
 * Fix Health Records - Update student information in Health_Questionnaires table
 * This script ensures that the student information in health records matches the current student data
 */

session_start();
include 'connection.php';

// Allow running without session for development/testing
// Uncomment the lines below for production security
/*
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['is_superadmin'])) {
    die('Unauthorized access');
}
*/

echo "<h1>Fixing Health Records - Updating Student Information</h1>";
echo "<pre>";

// Get all health records with their current student data
$sql = "SELECT hq.id, hq.student_id, hq.student_sex, hq.student_birthday, hq.student_age, hq.home_address,
               s.Student_Fname, s.Student_Mname, s.Student_Lname, s.contact_number,
               sec.section_name, p.program_name, d.department_level
        FROM Health_Questionnaires hq
        LEFT JOIN Students s ON hq.student_id = s.student_id
        LEFT JOIN Sections sec ON s.section_id = sec.section_id
        LEFT JOIN Programs p ON sec.program_id = p.program_id
        LEFT JOIN Departments d ON p.department_id = d.department_id
        WHERE s.student_id IS NOT NULL";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updated = 0;
$errors = 0;

foreach ($records as $record) {
    try {
        // Calculate age from birthday if available
        $age = null;
        if (!empty($record['student_birthday'])) {
            $birthDate = new DateTime($record['student_birthday']);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
        }

        // Determine education level based on current student data
        $educationLevel = 'basic';
        $deptLevel = isset($record['department_level']) ? strtolower(trim($record['department_level'])) : '';
        $programName = isset($record['program_name']) ? strtolower(trim($record['program_name'])) : '';

        // Check program name first (more reliable indicator)
        $collegeProgramIndicators = ['bs', 'ba', 'bse', 'bachelor', 'associate', 'diploma', 'certificate'];
        $isCollegeProgram = false;
        foreach ($collegeProgramIndicators as $indicator) {
            if (strpos($programName, $indicator) !== false) {
                $isCollegeProgram = true;
                break;
            }
        }

        // Also check if department_level is 'College'
        if (!$isCollegeProgram && strtolower($deptLevel) === 'college') {
            $isCollegeProgram = true;
        }

        // Check department level
        $isCollegeDepartment = (strpos($deptLevel, 'college') !== false || strpos($deptLevel, 'tertiary') !== false || strpos($deptLevel, 'higher') !== false);

        // Determine education level
        if ($isCollegeProgram || $isCollegeDepartment) {
            $educationLevel = 'college';
        }

        // Update the record with current student information
        $updateSql = "UPDATE Health_Questionnaires SET
                      education_level = ?,
                      student_age = ?,
                      updated_at = NOW()
                      WHERE id = ?";

        $updateStmt = $pdo->prepare($updateSql);
        $result = $updateStmt->execute([
            $educationLevel,
            $age,
            $record['id']
        ]);

        if ($result) {
            $updated++;
            echo "Updated record ID {$record['id']} for student {$record['student_id']} ({$record['Student_Fname']} {$record['Student_Lname']})\n";
        } else {
            $errors++;
            echo "Failed to update record ID {$record['id']}\n";
        }

    } catch (Exception $e) {
        $errors++;
        echo "Error updating record ID {$record['id']}: " . $e->getMessage() . "\n";
    }
}

echo "\nSummary:\n";
echo "Records processed: " . count($records) . "\n";
echo "Records updated: $updated\n";
echo "Errors: $errors\n";

echo "\nDone!</pre>";
?>
