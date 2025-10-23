<?php
header('Content-Type: application/json');
include 'connection.php';
include 'appointments_data.php';

try {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? '';

    if (empty($action)) {
        throw new Exception('Action is required');
    }

    switch ($action) {
        case 'get-all':
            // Get all appointments with pagination and filters
            $filters = [
                'search' => $_GET['search'] ?? '',
                'department' => $_GET['department'] ?? '',
                'status' => $_GET['status'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? ''
            ];

            $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $rows_per_page = isset($_GET['rows']) ? max(1, intval($_GET['rows'])) : 10;
            $offset = ($current_page - 1) * $rows_per_page;

            $total_appointments = getTotalAppointmentsCount($filters);
            $appointments = getAllAppointments($filters, $rows_per_page, $offset);
            $total_pages = ceil($total_appointments / $rows_per_page);

            echo json_encode([
                'success' => true,
                'appointments' => $appointments,
                'total_count' => $total_appointments,
                'total_pages' => $total_pages,
                'current_page' => $current_page
            ]);
            break;

        case 'get-details':
            // Get appointment details
            if (empty($id)) {
                throw new Exception('Appointment ID is required');
            }

            $appointment = getAppointmentById($id);
            if (!$appointment) {
                throw new Exception('Appointment not found');
            }

            echo json_encode([
                'success' => true,
                'appointment' => $appointment
            ]);
            break;

        case 'get-by-date-range':
            // Get appointments within date range
            $start_date = $_GET['start_date'] ?? '';
            $end_date = $_GET['end_date'] ?? '';

            if (empty($start_date) || empty($end_date)) {
                throw new Exception('Start date and end date are required');
            }

            $appointments = getAppointmentsByDateRange($start_date, $end_date);

            echo json_encode([
                'success' => true,
                'appointments' => $appointments
            ]);
            break;

        case 'create':
            // Create new appointment
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method for create');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                throw new Exception('Invalid JSON data');
            }

            // Validate required fields
            $required_fields = ['section_id', 'program_id', 'department', 'appointment_date', 'start_time', 'end_time', 'reason'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            $appointment_id = createAppointment($data);
            if (!$appointment_id) {
                throw new Exception('Failed to create appointment');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Appointment created successfully',
                'appointment_id' => $appointment_id
            ]);
            break;

        case 'update':
            // Update appointment
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                throw new Exception('Invalid request method for update');
            }

            if (empty($id)) {
                throw new Exception('Appointment ID is required');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                throw new Exception('Invalid JSON data');
            }

            // Validate required fields
            $required_fields = ['section_id', 'program_id', 'department', 'appointment_date', 'start_time', 'end_time', 'reason'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            $success = updateAppointment($id, $data);
            if (!$success) {
                throw new Exception('Failed to update appointment');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Appointment updated successfully'
            ]);
            break;

        case 'delete':
            // Delete appointment
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Invalid request method for delete');
            }

            if (empty($id)) {
                throw new Exception('Appointment ID is required');
            }

            $success = deleteAppointment($id);
            if (!$success) {
                throw new Exception('Failed to delete appointment');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
            break;

        case 'update-status':
            // Update appointment status
            if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
                throw new Exception('Invalid request method for status update');
            }

            if (empty($id)) {
                throw new Exception('Appointment ID is required');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['status'])) {
                throw new Exception('Status is required');
            }

            $valid_statuses = ['scheduled', 'completed', 'cancelled'];
            if (!in_array($data['status'], $valid_statuses)) {
                throw new Exception('Invalid status value');
            }

            $success = updateAppointmentStatus($id, $data['status']);
            if (!$success) {
                throw new Exception('Failed to update appointment status');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Appointment status updated successfully'
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
