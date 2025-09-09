<?php
include 'connection.php';

// Function to get total students count based on filters
function getTotalStudentsCount($filters) {
    global $pdo;

    $sql = "SELECT COUNT(*) FROM Students s
            LEFT JOIN Sections sec ON s.section_id = sec.section_id
            LEFT JOIN Programs p ON sec.program_id = p.program_id
            LEFT JOIN Departments d ON p.department_id = d.department_id
            WHERE 1=1";

    $params = [];

    if (!empty($filters['search'])) {
        $sql .= " AND (s.Student_Fname LIKE ? OR s.Student_Lname LIKE ? OR s.student_id LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if (!empty($filters['department'])) {
        $sql .= " AND d.department_level = ?";
        $params[] = $filters['department'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

// Function to get all students with filters, pagination
function getAllStudents($filters, $limit, $offset) {
    global $pdo;

    $sql = "SELECT s.*, p.program_name, d.department_level, sec.section_name, sec.section_id,
            (SELECT COUNT(*) FROM Clinic_Visits cv WHERE cv.patient_type = 'Student' AND cv.patient_id = s.student_id) AS total_visits
            FROM Students s
            LEFT JOIN Sections sec ON s.section_id = sec.section_id
            LEFT JOIN Programs p ON sec.program_id = p.program_id
            LEFT JOIN Departments d ON p.department_id = d.department_id
            WHERE 1=1";

    $params = [];

    if (!empty($filters['search'])) {
        $sql .= " AND (s.Student_Fname LIKE ? OR s.Student_Lname LIKE ? OR s.student_id LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if (!empty($filters['department'])) {
        $sql .= " AND d.department_level = ?";
        $params[] = $filters['department'];
    }

    // Sorting
    switch ($filters['sort']) {
        case 'name-desc':
            $sql .= " ORDER BY s.Student_Lname DESC, s.Student_Fname DESC";
            break;
        case 'visits-asc':
            $sql .= " ORDER BY total_visits ASC";
            break;
        case 'visits-desc':
            $sql .= " ORDER BY total_visits DESC";
            break;
        case 'name-asc':
        default:
            $sql .= " ORDER BY s.Student_Lname ASC, s.Student_Fname ASC";
            break;
    }

    $sql .= " LIMIT ? OFFSET ?";
    $params[] = (int)$limit;
    $params[] = (int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Helper function to get full student name
function getStudentFullName($student) {
    $fullName = $student['Student_Fname'];
    if (!empty($student['Student_Mname'])) {
        $fullName .= ' ' . $student['Student_Mname'];
    }
    $fullName .= ' ' . $student['Student_Lname'];
    return $fullName;
}

// Helper function to get department display name
function getDepartmentDisplayName($departmentLevel) {
    switch ($departmentLevel) {
        case 'College':
            return 'College';
        case 'SHS':
            return 'Senior High School';
        case 'JHS':
            return 'Junior High School';
        case 'Grade School':
            return 'Grade School';
        default:
            return 'N/A';
    }
}

// Function to get all programs
function getAllPrograms() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, d.department_level FROM Programs p LEFT JOIN Departments d ON p.department_id = d.department_id ORDER BY p.program_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all sections
function getAllSections() {
    global $pdo;
    $stmt = $pdo->query("SELECT sec.*, p.program_name FROM Sections sec LEFT JOIN Programs p ON sec.program_id = p.program_id ORDER BY sec.section_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
