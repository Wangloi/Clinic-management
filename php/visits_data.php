<?php
// visits_data.php
function getAllClinicVisits($filters = [], $limit = 10, $offset = 0) {
    include 'connection.php';
    
    try {
        $whereClause = "";
        $params = [];
        
        // Build WHERE clause based on filters
        if (!empty($filters['search'])) {
            $whereClause .= " AND (cv.reason LIKE ? OR cv.treatment LIKE ? OR cv.diagnosis LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['patient_type'])) {
            $whereClause .= " AND cv.patient_type = ?";
            $params[] = $filters['patient_type'];
        }

        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare("
            SELECT 
                cv.visit_id,
                cv.patient_type,
                cv.patient_id,
                cv.visit_date,
                cv.reason,
                cv.diagnosis,
                cv.treatment,
                cv.remarks,
                cv.created_at,
                -- Student data
                s.Student_Fname,
                s.Student_Lname,
                s.Student_Mname,
                sec.section_name,
                p.program_name,
                d.department_level,
                -- Staff data
                st.clinic_Fname as staff_Fname,
                st.clinic_Lname as staff_Lname,
                st.clinic_Mname as staff_Mname
            FROM Clinic_Visits cv
            LEFT JOIN Students s ON cv.patient_type = 'Student' AND cv.patient_id = s.student_id
            LEFT JOIN Sections sec ON s.section_id = sec.section_id
            LEFT JOIN Programs p ON sec.program_id = p.program_id
            LEFT JOIN Departments d ON p.department_id = d.department_id
            LEFT JOIN Clinic_Staff st ON cv.patient_type = 'Staff' AND cv.patient_id = st.staff_id
            WHERE 1=1 $whereClause
            ORDER BY cv.visit_date DESC, cv.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Database error in getAllClinicVisits: " . $e->getMessage());
        return [];
    }
}

function getTotalClinicVisitsCount($filters = []) {
    include 'connection.php';
    
    try {
        $whereClause = "";
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereClause .= " AND (cv.reason LIKE ? OR cv.treatment LIKE ? OR cv.diagnosis LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['patient_type'])) {
            $whereClause .= " AND cv.patient_type = ?";
            $params[] = $filters['patient_type'];
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_count
            FROM Clinic_Visits cv
            WHERE 1=1 $whereClause
        ");
        
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_count'];
        
    } catch (PDOException $e) {
        error_log("Database error in getTotalClinicVisitsCount: " . $e->getMessage());
        return 0;
    }
}

function getPatientName($visit) {
    if ($visit['patient_type'] === 'Student') {
        $name = $visit['Student_Fname'];
        if (!empty($visit['Student_Mname'])) {
            $name .= ' ' . substr($visit['Student_Mname'], 0, 1) . '.';
        }
        $name .= ' ' . $visit['Student_Lname'];
        return $name;
    } elseif ($visit['patient_type'] === 'Staff') {
        $name = $visit['staff_Fname'];
        if (!empty($visit['staff_Mname'])) {
            $name .= ' ' . substr($visit['staff_Mname'], 0, 1) . '.';
        }
        $name .= ' ' . $visit['staff_Lname'];
        return $name;
    } else {
        return 'Visitor #' . $visit['patient_id'];
    }
}

function getPatientInfo($visit) {
    if ($visit['patient_type'] === 'Student') {
        if (!empty($visit['program_name'])) {
            return $visit['program_name'];
        } elseif (!empty($visit['section_name'])) {
            return $visit['section_name'];
        } else {
            return 'Student';
        }
    } elseif ($visit['patient_type'] === 'Staff') {
        return 'Staff Member';
    } else {
        return 'Visitor';
    }
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
?>
