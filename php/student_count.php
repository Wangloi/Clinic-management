<?php
// student_count.php
function getStudentCount() {
    // Include database connection
    include 'connection.php';
    
    try {
        // Query to count total students
        $stmt = $pdo->query("SELECT COUNT(*) as total_students FROM Students");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Return the count
        return number_format($result['total_students']);
    } catch (PDOException $e) {
        // Log error and return error message
        error_log("Database error: " . $e->getMessage());
        return "Error";
    }
}

// Count students by section
function getStudentsBySection($section_id) {
    include 'connection.php';
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_students FROM Students WHERE section_id = ?");
        $stmt->execute([$section_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return number_format($result['total_students']);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return "Error";
    }
}

// Get all sections with student counts
function getSectionsWithCounts() {
    include 'connection.php';
    
    try {
        $stmt = $pdo->query("
            SELECT s.section_id, s.section_name, COUNT(st.student_id) as student_count 
            FROM Sections s 
            LEFT JOIN Students st ON s.section_id = st.section_id 
            GROUP BY s.section_id, s.section_name 
            ORDER BY s.section_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}
?>