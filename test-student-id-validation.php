<?php
// Test script to verify student ID validation changes
echo "<h2>Student ID Validation Test</h2>";

// Test cases
$test_cases = [
    '123456' => 'Numeric ID (should work)',
    'STU001' => 'Alphanumeric ID (should work)', 
    '2024-001' => 'ID with hyphen (should work)',
    'ABC_123' => 'ID with underscore (should work)',
    'student@123' => 'ID with special chars (should fail)',
    '' => 'Empty ID (should fail)',
    'ABC 123' => 'ID with space (should fail)'
];

foreach ($test_cases as $student_id => $description) {
    echo "<p><strong>Testing:</strong> '$student_id' - $description<br>";
    
    // Test the validation regex
    if (empty($student_id)) {
        echo "<span style='color: red;'>❌ Failed: Empty ID</span>";
    } elseif (!preg_match('/^[a-zA-Z0-9\-_]+$/', $student_id)) {
        echo "<span style='color: red;'>❌ Failed: Invalid characters</span>";
    } else {
        echo "<span style='color: green;'>✅ Passed: Valid format</span>";
    }
    echo "</p>";
}
?>
