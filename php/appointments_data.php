<?php
// appointments_data.php

function getAllAppointments($filters = [], $limit = 10, $offset = 0) {
    include 'connection.php';

    try {
        $whereClause = "";
        $params = [];

        // Build WHERE clause based on filters
        if (!empty($filters['search'])) {
            $whereClause .= " AND (a.reason LIKE ? OR a.notes LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        if (!empty($filters['department'])) {
            $whereClause .= " AND a.department = ?";
            $params[] = $filters['department'];
        }

        if (!empty($filters['status'])) {
            $whereClause .= " AND a.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $whereClause .= " AND a.appointment_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereClause .= " AND a.appointment_date <= ?";
            $params[] = $filters['date_to'];
        }

        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare("
            SELECT
                a.appointment_id,
                a.section_id,
                a.program_id,
                a.department,
                a.appointment_date,
                a.start_time,
                a.end_time,
                a.reason,
                a.notes,
                a.status,
                a.created_at,
                a.updated_at,
                -- Section and program data
                s.section_name,
                p.program_name
            FROM appointments a
            LEFT JOIN sections s ON a.section_id = s.section_id
            LEFT JOIN programs p ON a.program_id = p.program_id
            WHERE 1=1 $whereClause
            ORDER BY a.appointment_date DESC, a.start_time ASC
            LIMIT ? OFFSET ?
        ");

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Database error in getAllAppointments: " . $e->getMessage());
        return [];
    }
}

function getTotalAppointmentsCount($filters = []) {
    include 'connection.php';

    try {
        $whereClause = "";
        $params = [];

        if (!empty($filters['search'])) {
            $whereClause .= " AND (a.reason LIKE ? OR a.notes LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        if (!empty($filters['department'])) {
            $whereClause .= " AND a.department = ?";
            $params[] = $filters['department'];
        }

        if (!empty($filters['status'])) {
            $whereClause .= " AND a.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $whereClause .= " AND a.appointment_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereClause .= " AND a.appointment_date <= ?";
            $params[] = $filters['date_to'];
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_count
            FROM appointments a
            WHERE 1=1 $whereClause
        ");

        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_count'];

    } catch (PDOException $e) {
        error_log("Database error in getTotalAppointmentsCount: " . $e->getMessage());
        return 0;
    }
}

function getAppointmentById($appointment_id) {
    include 'connection.php';

    try {
        $stmt = $pdo->prepare("
            SELECT
                a.appointment_id,
                a.section_id,
                a.program_id,
                a.department,
                a.appointment_date,
                a.start_time,
                a.end_time,
                a.reason,
                a.notes,
                a.status,
                a.created_at,
                a.updated_at,
                -- Section and program data
                s.section_name,
                p.program_name
            FROM appointments a
            LEFT JOIN sections s ON a.section_id = s.section_id
            LEFT JOIN programs p ON a.program_id = p.program_id
            WHERE a.appointment_id = ?
        ");

        $stmt->execute([$appointment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Database error in getAppointmentById: " . $e->getMessage());
        return null;
    }
}

function getAppointmentsByDateRange($start_date, $end_date) {
    include 'connection.php';

    try {
        $stmt = $pdo->prepare("
            SELECT
                a.appointment_id,
                a.section_id,
                a.program_id,
                a.department,
                a.appointment_date,
                a.start_time,
                a.end_time,
                a.reason,
                a.notes,
                a.status,
                -- Section data
                s.section_name,
                p.program_name
            FROM appointments a
            LEFT JOIN sections s ON a.section_id = s.section_id
            LEFT JOIN programs p ON a.program_id = p.program_id
            WHERE a.appointment_date BETWEEN ? AND ?
            ORDER BY a.appointment_date, a.start_time
        ");

        $stmt->execute([$start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Database error in getAppointmentsByDateRange: " . $e->getMessage());
        return [];
    }
}

function getTodayAppointments() {
    $today = date('Y-m-d');
    return getAppointmentsByDateRange($today, $today);
}

function createAppointment($data) {
    include 'connection.php';

    try {
        $stmt = $pdo->prepare("
            INSERT INTO appointments (
                section_id, program_id, department, appointment_date,
                start_time, end_time, reason, notes, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['section_id'],
            $data['program_id'],
            $data['department'],
            $data['appointment_date'],
            $data['start_time'],
            $data['end_time'],
            $data['reason'],
            $data['notes'] ?? null,
            $data['status'] ?? 'scheduled'
        ]);

        return $pdo->lastInsertId();

    } catch (PDOException $e) {
        error_log("Database error in createAppointment: " . $e->getMessage());
        return false;
    }
}

function updateAppointment($appointment_id, $data) {
    include 'connection.php';

    try {
        $stmt = $pdo->prepare("
            UPDATE appointments SET
                section_id = ?,
                program_id = ?,
                department = ?,
                appointment_date = ?,
                start_time = ?,
                end_time = ?,
                reason = ?,
                notes = ?,
                status = ?
            WHERE appointment_id = ?
        ");

        $stmt->execute([
            $data['section_id'],
            $data['program_id'],
            $data['department'],
            $data['appointment_date'],
            $data['start_time'],
            $data['end_time'],
            $data['reason'],
            $data['notes'] ?? null,
            $data['status'] ?? 'scheduled',
            $appointment_id
        ]);

        return $stmt->rowCount() > 0;

    } catch (PDOException $e) {
        error_log("Database error in updateAppointment: " . $e->getMessage());
        return false;
    }
}

function deleteAppointment($appointment_id) {
    include 'connection.php';

    try {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);
        return $stmt->rowCount() > 0;

    } catch (PDOException $e) {
        error_log("Database error in deleteAppointment: " . $e->getMessage());
        return false;
    }
}

function updateAppointmentStatus($appointment_id, $status) {
    include 'connection.php';

    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
        $stmt->execute([$status, $appointment_id]);
        return $stmt->rowCount() > 0;

    } catch (PDOException $e) {
        error_log("Database error in updateAppointmentStatus: " . $e->getMessage());
        return false;
    }
}
?>
