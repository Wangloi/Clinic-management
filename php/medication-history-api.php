<?php
/**
 * Medication Dispensing History API
 * Provides endpoints to retrieve medication dispensing history
 */

// Clean any output before starting
ob_start();
session_start();

// Clean the output buffer to prevent any unwanted output
ob_clean();

// Suppress PHP warnings to prevent JSON corruption
error_reporting(E_ERROR | E_PARSE);

require_once 'connection.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if user is logged in and is an admin (clinic staff or head staff)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['clinic', 'head'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Admin privileges required.',
        'error_code' => 'AUTH_REQUIRED'
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method !== 'GET' || empty($action)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request parameters',
        'error_code' => 'INVALID_REQUEST'
    ]);
    exit();
}

try {
    switch ($action) {
        case 'get_all_history':
            getAllMedicationHistory();
            break;
        case 'get_student_history':
            $studentId = $_GET['student_id'] ?? '';
            if (empty($studentId)) {
                throw new Exception('Student ID is required');
            }
            getStudentMedicationHistory($studentId);
            break;
        case 'get_medicine_history':
            $medicineId = $_GET['medicine_id'] ?? '';
            if (empty($medicineId)) {
                throw new Exception('Medicine ID is required');
            }
            getMedicineDispenseHistory($medicineId);
            break;
        case 'get_history_stats':
            getMedicationHistoryStats();
            break;
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action',
                'error_code' => 'INVALID_ACTION'
            ]);
    }
} catch (Exception $e) {
    error_log("Medication History API Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'error_code' => 'SERVER_ERROR',
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}

/**
 * Get all medication dispensing history with pagination and filters
 */
function getAllMedicationHistory() {
    global $pdo;
    
    try {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(100, intval($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';
        $medicineFilter = $_GET['medicine_id'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = "(student_name LIKE ? OR medicine_name LIKE ? OR reason_for_visit LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($medicineFilter)) {
            $whereConditions[] = "medicine_id = ?";
            $params[] = $medicineFilter;
        }
        
        if (!empty($dateFrom)) {
            $whereConditions[] = "DATE(dispensed_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $whereConditions[] = "DATE(dispensed_at) <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM Medication_Dispensing_History $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get paginated data
        $sql = "SELECT 
                    history_id,
                    student_name,
                    medicine_name,
                    quantity_dispensed,
                    unit,
                    dispensed_by_name,
                    dispensed_at,
                    visit_date,
                    reason_for_visit,
                    notes
                FROM Medication_Dispensing_History 
                $whereClause
                ORDER BY dispensed_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $history,
            'pagination' => [
                'current_page' => $page,
                'total_records' => $total,
                'records_per_page' => $limit,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error fetching medication history: " . $e->getMessage());
    }
}

/**
 * Get medication history for a specific student
 */
function getStudentMedicationHistory($studentId) {
    global $pdo;
    
    try {
        $sql = "SELECT 
                    history_id,
                    medicine_name,
                    quantity_dispensed,
                    unit,
                    dispensed_by_name,
                    dispensed_at,
                    visit_date,
                    reason_for_visit,
                    notes
                FROM Medication_Dispensing_History 
                WHERE student_id = ?
                ORDER BY dispensed_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$studentId]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $history,
            'student_id' => $studentId
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error fetching student medication history: " . $e->getMessage());
    }
}

/**
 * Get dispensing history for a specific medicine
 */
function getMedicineDispenseHistory($medicineId) {
    global $pdo;
    
    try {
        // Check if table exists
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'Medication_Dispensing_History'");
        if ($tableCheck->rowCount() == 0) {
            throw new Exception("Medication_Dispensing_History table does not exist. Please create it first.");
        }
        $sql = "SELECT 
                    history_id,
                    student_name,
                    quantity_dispensed,
                    unit,
                    dispensed_by_name,
                    dispensed_at,
                    visit_date,
                    reason_for_visit
                FROM Medication_Dispensing_History 
                WHERE medicine_id = ?
                ORDER BY dispensed_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$medicineId]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get total quantity dispensed
        $totalSql = "SELECT SUM(quantity_dispensed) as total_dispensed 
                     FROM Medication_Dispensing_History 
                     WHERE medicine_id = ?";
        $totalStmt = $pdo->prepare($totalSql);
        $totalStmt->execute([$medicineId]);
        $totalDispensed = $totalStmt->fetch(PDO::FETCH_ASSOC)['total_dispensed'] ?? 0;
        
        echo json_encode([
            'success' => true,
            'data' => $history,
            'medicine_id' => $medicineId,
            'total_dispensed' => $totalDispensed
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error fetching medicine dispensing history: " . $e->getMessage());
    }
}

/**
 * Get medication history statistics
 */
function getMedicationHistoryStats() {
    global $pdo;
    
    try {
        // Total dispensing records
        $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM Medication_Dispensing_History");
        $totalRecords = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Most dispensed medicines
        $topMedicinesStmt = $pdo->query("
            SELECT medicine_name, SUM(quantity_dispensed) as total_dispensed
            FROM Medication_Dispensing_History 
            GROUP BY medicine_id, medicine_name
            ORDER BY total_dispensed DESC 
            LIMIT 10
        ");
        $topMedicines = $topMedicinesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recent activity (last 30 days)
        $recentStmt = $pdo->query("
            SELECT COUNT(*) as recent_dispensing
            FROM Medication_Dispensing_History 
            WHERE dispensed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $recentActivity = $recentStmt->fetch(PDO::FETCH_ASSOC)['recent_dispensing'];
        
        // Monthly dispensing trend (last 6 months)
        $trendStmt = $pdo->query("
            SELECT 
                DATE_FORMAT(dispensed_at, '%Y-%m') as month,
                COUNT(*) as dispensing_count,
                SUM(quantity_dispensed) as total_quantity
            FROM Medication_Dispensing_History 
            WHERE dispensed_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(dispensed_at, '%Y-%m')
            ORDER BY month DESC
        ");
        $monthlyTrend = $trendStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'stats' => [
                'total_records' => $totalRecords,
                'recent_activity' => $recentActivity,
                'top_medicines' => $topMedicines,
                'monthly_trend' => $monthlyTrend
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error fetching medication history statistics: " . $e->getMessage());
    }
}
?>
