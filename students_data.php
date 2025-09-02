<?php
// students_data.php
function getAllStudents($filters = [], $limit = 10, $offset = 0) {
    include 'connection.php';
    
    try {
        $whereClause = "";
        $params = [];
        
        // Build WHERE clause based on filters
        if (!empty($filters['search'])) {
            $whereClause .= " AND (s.Student_Fname LIKE ? OR s.Student_Lname LIKE ? OR s.Student_Mname LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['department'])) {
            $whereClause .= " AND d.department_level = ?";
            $params[] = $filters['department'];
        }
        
        // Build ORDER BY clause
        $orderBy = "s.Student_Lname ASC, s.Student_Fname ASC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'name-desc':
                    $orderBy = "s.Student_Lname DESC, s.Student_Fname DESC";
                    break;
                case 'id-asc':
                    $orderBy = "s.student_id ASC";
                    break;
                case 'id-desc':
                    $orderBy = "s.student_id DESC";
                    break;
                case 'visits-asc':
                    $orderBy = "total_visits ASC";
                    break;
                case 'visits-desc':
                    $orderBy = "total_visits DESC";
                    break;
            }
        }

        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare("
            SELECT 
                s.student_id,
                s.Student_Fname,
                s.Student_Mname,
                s.Student_Lname,
                s.contact_number,
                s.created_at,
                sec.section_name,
                p.program_name,
                d.department_level,
                COUNT(cv.visit_id) as total_visits
            FROM Students s
            LEFT JOIN Sections sec ON s.section_id = sec.section_id
            LEFT JOIN Programs p ON sec.program_id = p.program_id
            LEFT JOIN Departments d ON p.department_id = d.department_id
            LEFT JOIN Clinic_Visits cv ON s.student_id = cv.patient_id AND cv.patient_type = 'Student'
            WHERE 1=1 $whereClause
            GROUP BY s.student_id
            ORDER BY $orderBy
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Database error in getAllStudents: " . $e->getMessage());
        return [];
    }
}

function getTotalStudentsCount($filters = []) {
    include 'connection.php';
    
    try {
        $whereClause = "";
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (s.Student_Fname LIKE ? OR s.Student_Lname LIKE ? OR s.Student_Mname LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['department'])) {
            $whereClause .= " AND d.department_level = ?";
            $params[] = $filters['department'];
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_count
            FROM Students s
            WHERE 1=1 $whereClause
        ");
        
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_count'];
        
    } catch (PDOException $e) {
        error_log("Database error in getTotalStudentsCount: " . $e->getMessage());
        return 0;
    }
}

function getStudentFullName($student) {
    $name = $student['Student_Fname'];
    if (!empty($student['Student_Mname'])) {
        $name .= ' ' . substr($student['Student_Mname'], 0, 1) . '.';
    }
    $name .= ' ' . $student['Student_Lname'];
    return $name;
}

function getDepartmentDisplayName($department) {
    $names = [
        'College' => 'College',
        'SHS' => 'Senior High',
        'JHS' => 'Junior High',
        'Grade School' => 'Grade School'
    ];
    return $names[$department] ?? $department;
}

// Get all programs from database
function getAllPrograms() {
    include 'connection.php';
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.program_id, p.program_name, d.department_level
            FROM Programs p
            LEFT JOIN Departments d ON p.department_id = d.department_id
            ORDER BY d.department_level, p.program_name
        ");
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Database error in getAllPrograms: " . $e->getMessage());
        return [];
    }
}

// Get all sections from database
function getAllSections() {
    include 'connection.php';
    
    try {
        $stmt = $pdo->prepare("
            SELECT s.section_id, s.section_name, p.program_id, p.program_name, d.department_level
            FROM Sections s
            LEFT JOIN Programs p ON s.program_id = p.program_id
            LEFT JOIN Departments d ON p.department_id = d.department_id
            ORDER BY d.department_level, p.program_name, s.section_name
        ");
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Database error in getAllSections: " . $e->getMessage());
        return [];
    }
}
?>
