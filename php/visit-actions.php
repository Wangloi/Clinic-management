<?php
header('Content-Type: application/json');
include 'connection.php';

try {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? '';

    if (empty($action) || empty($id)) {
        throw new Exception('Action and ID are required');
    }

    switch ($action) {
        case 'get-data':
            // Get visit data for editing
            $stmt = $pdo->prepare("
                SELECT cv.*
                FROM Clinic_Visits cv
                WHERE cv.visit_id = ?
            ");
            $stmt->execute([$id]);
            $visit = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$visit) {
                throw new Exception('Visit not found');
            }

            echo json_encode([
                'success' => true,
                'visit_id' => $visit['visit_id'],
                'patient_type' => $visit['patient_type'],
                'patient_id' => $visit['patient_id'],
                'reason' => $visit['reason'],
                'diagnosis' => $visit['diagnosis'],
                'treatment' => $visit['treatment'],
                'remarks' => $visit['remarks']
            ]);
            break;

        case 'get-details':
            // Get visit details for viewing
            $stmt = $pdo->prepare("
                SELECT cv.*,
                       s.Student_Fname, s.Student_Lname, s.Student_Mname,
                       st.clinic_Fname as staff_Fname, st.clinic_Lname as staff_Lname, st.clinic_Mname as staff_Mname
                FROM Clinic_Visits cv
                LEFT JOIN Students s ON cv.patient_type = 'Student' AND cv.patient_id = s.student_id
                LEFT JOIN Clinic_Staff st ON cv.patient_type = 'Staff' AND cv.patient_id = st.staff_id
                WHERE cv.visit_id = ?
            ");
            $stmt->execute([$id]);
            $visit = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$visit) {
                throw new Exception('Visit not found');
            }

            // Build patient name
            $patient_name = '';
            if ($visit['patient_type'] === 'Student') {
                $patient_name = $visit['Student_Fname'];
                if (!empty($visit['Student_Mname'])) {
                    $patient_name .= ' ' . substr($visit['Student_Mname'], 0, 1) . '.';
                }
                $patient_name .= ' ' . $visit['Student_Lname'];
            } elseif ($visit['patient_type'] === 'Staff') {
                $patient_name = $visit['staff_Fname'];
                if (!empty($visit['staff_Mname'])) {
                    $patient_name .= ' ' . substr($visit['staff_Mname'], 0, 1) . '.';
                }
                $patient_name .= ' ' . $visit['staff_Lname'];
            } else {
                $patient_name = 'Visitor #' . $visit['patient_id'];
            }

            // Get dispensed medications
            $stmt_med = $pdo->prepare("
                SELECT md.quantity_dispensed, mi.medicine_name, mi.description
                FROM Medicine_Dispensing md
                JOIN Medicine_Inventory mi ON md.medicine_id = mi.medicine_id
                WHERE md.visit_id = ?
            ");
            $stmt_med->execute([$id]);
            $dispensed_medications = $stmt_med->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'patient_name' => $patient_name,
                'patient_id' => $visit['patient_id'],
                'patient_type' => $visit['patient_type'],
                'visit_date' => $visit['visit_date'],
                'reason' => $visit['reason'],
                'diagnosis' => $visit['diagnosis'],
                'treatment' => $visit['treatment'],
                'remarks' => $visit['remarks'],
                'created_at' => $visit['created_at'],
                'dispensed_medications' => $dispensed_medications
            ]);
            break;

        case 'delete':
            // Delete visit
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Invalid request method for delete');
            }

            // Disable foreign key checks to handle constraints
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            // Start transaction for atomic operation
            $pdo->beginTransaction();

            try {
                // Delete medicine_dispensing records for this visit
                $delete_medicine_stmt = $pdo->prepare("DELETE FROM medicine_dispensing WHERE visit_id = ?");
                $delete_medicine_stmt->execute([$id]);

                // Delete clinic_visit
                $delete_visit_stmt = $pdo->prepare("DELETE FROM Clinic_Visits WHERE visit_id = ?");
                $success = $delete_visit_stmt->execute([$id]);

                if (!$success) {
                    throw new Exception('Failed to delete visit from database');
                }

                // Commit transaction
                $pdo->commit();

                // Re-enable foreign key checks
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

            } catch (Exception $e) {
                // Rollback on error
                $pdo->rollBack();
                // Re-enable foreign key checks
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
                throw $e;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Visit deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
