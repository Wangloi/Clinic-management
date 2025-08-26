<?php
// recent_visits.php
function getRecentClinicVisits($limit = 10) {
    // Include database connection
    include 'connection.php';
    
    try {
        // Check if Clinic_Visits table exists
        $tableExists = $pdo->query("SHOW TABLES LIKE 'Clinic_Visits'")->rowCount() > 0;
        
        if (!$tableExists) {
            error_log("Clinic_Visits table does not exist");
            return [];
        }

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
            ORDER BY cv.visit_date DESC, cv.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Found " . count($results) . " clinic visits");
        return $results;
        
    } catch (PDOException $e) {
        error_log("Database error in getRecentClinicVisits: " . $e->getMessage());
        return [];
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
?>