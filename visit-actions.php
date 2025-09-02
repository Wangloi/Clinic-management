<?php
include 'connection.php';

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? '';
    $visitId = $_GET['id'] ?? null;

    if (!$visitId) {
        echo json_encode(['success' => false, 'message' => 'Visit ID is required']);
        exit;
    }

    switch ($action) {
        case 'get-data':
            // Get visit data for editing
            $stmt = $pdo->prepare("
                SELECT 
                    cv.*,
                    s.Student_Fname, s.Student_Lname, s.Student_Mname,
                    sec.section_name,
                    p.program_name,
                    d.department_level,
                    st.clinic_Fname as staff_Fname, st.clinic_Lname as staff_Lname, st.clinic_Mname as staff_Mname
                FROM Clinic_Visits cv
                LEFT JOIN Students s ON cv.patient_type = 'Student' AND cv.patient_id = s.student_id
                LEFT JOIN Sections sec ON s.section_id = sec.section_id
                LEFT JOIN Programs p ON sec.program_id = p.program_id
                LEFT JOIN Departments d ON p.department_id = d.department_id
                LEFT JOIN Clinic_Staff st ON cv.patient_type = 'Staff' AND cv.patient_id = st.staff_id
                WHERE cv.visit_id = ?
            ");
            $stmt->execute([$visitId]);
            $visit = $stmt->fetch();

            if ($visit) {
                echo json_encode(['success' => true, 'visit' => $visit]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Visit not found']);
            }
            break;

        case 'delete':
            // Delete visit record
            $stmt = $pdo->prepare("DELETE FROM Clinic_Visits WHERE visit_id = ?");
            $stmt->execute([$visitId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Visit deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Visit not found or already deleted']);
            }
            break;

        case 'get-details':
            // Get detailed visit information
            $stmt = $pdo->prepare("
                SELECT 
                    cv.*,
                    s.Student_Fname, s.Student_Lname, s.Student_Mname,
                    sec.section_name,
                    p.program_name,
                    d.department_level,
                    st.clinic_Fname as staff_Fname, st.clinic_Lname as staff_Lname, st.clinic_Mname as staff_Mname
                FROM Clinic_Visits cv
                LEFT JOIN Students s ON cv.patient_type = 'Student' AND cv.patient_id = s.student_id
                LEFT JOIN Sections sec ON s.section_id = sec.section_id
                LEFT JOIN Programs p ON sec.program_id = p.program_id
                LEFT JOIN Departments d ON p.department_id = d.department_id
                LEFT JOIN Clinic_Staff st ON cv.patient_type = 'Staff' AND cv.patient_id = st.staff_id
                WHERE cv.visit_id = ?
            ");
            $stmt->execute([$visitId]);
            $visit = $stmt->fetch();

            if ($visit) {
                // Format patient name
                if ($visit['patient_type'] === 'Student') {
                    $patientName = $visit['Student_Fname'];
                    if (!empty($visit['Student_Mname'])) {
                        $patientName .= ' ' . substr($visit['Student_Mname'], 0, 1) . '.';
                    }
                    $patientName .= ' ' . $visit['Student_Lname'];
                } elseif ($visit['patient_type'] === 'Staff') {
                    $patientName = $visit['staff_Fname'];
                    if (!empty($visit['staff_Mname'])) {
                        $patientName .= ' ' . substr($visit['staff_Mname'], 0, 1) . '.';
                    }
                    $patientName .= ' ' . $visit['staff_Lname'];
                } else {
                    $patientName = 'Visitor #' . $visit['patient_id'];
                }

                echo json_encode([
                    'success' => true,
                    'visit_id' => $visit['visit_id'],
                    'patient_type' => $visit['patient_type'],
                    'patient_id' => $visit['patient_id'],
                    'patient_name' => $patientName,
                    'visit_date' => $visit['visit_date'],
                    'reason' => $visit['reason'],
                    'diagnosis' => $visit['diagnosis'],
                    'treatment' => $visit['treatment'],
                    'remarks' => $visit['remarks'],
                    'created_at' => $visit['created_at']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Visit not found']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action specified']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . htmlspecialchars($e->getMessage())]);
}
?>
