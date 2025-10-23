<?php
// Very simple health questionnaire API for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Clean output buffer
if (ob_get_level()) {
    ob_clean();
}

header('Content-Type: application/json');

try {
    session_start();
    
    // Check session
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Not authenticated'
        ]);
        exit;
    }
    
    // Check request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'POST') {
        // Get POST data
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON: ' . json_last_error_msg()
            ]);
            exit;
        }
        
        // Save data to database
        require_once 'connection.php';
        
        $studentId = $_SESSION['user_id'];
        $healthData = $data['data'] ?? [];
        
        try {
            // First check if table exists and what columns it has
            $tableCheck = $pdo->query("SHOW TABLES LIKE 'Health_Questionnaires'");
            if (!$tableCheck->fetch()) {
                // Try alternative table name
                $tableCheck = $pdo->query("SHOW TABLES LIKE 'health_questionnaires'");
                if (!$tableCheck->fetch()) {
                    throw new Exception("Health questionnaires table not found");
                }
                $tableName = 'health_questionnaires';
            } else {
                $tableName = 'Health_Questionnaires';
            }
            
            // Check what columns exist
            $columnsStmt = $pdo->query("DESCRIBE $tableName");
            $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Determine primary key column
            $primaryKey = 'id';
            if (in_array('questionnaire_id', $columns)) {
                $primaryKey = 'questionnaire_id';
            } elseif (in_array('health_id', $columns)) {
                $primaryKey = 'health_id';
            }
            
            // Check if record exists
            $checkStmt = $pdo->prepare("SELECT $primaryKey FROM $tableName WHERE student_id = ?");
            $checkStmt->execute([$studentId]);
            $exists = $checkStmt->fetch();
            
            if ($exists) {
                // Update existing record
                $updateFields = [];
                $updateValues = [];
                
                foreach ($healthData as $key => $value) {
                    if (!empty($value) && $value !== null && in_array($key, $columns)) {
                        $updateFields[] = "`$key` = ?";
                        $updateValues[] = $value;
                    }
                }
                
                if (!empty($updateFields)) {
                    $updateValues[] = $studentId; // for WHERE clause
                    // Check if last_updated column exists
                    $lastUpdatedField = in_array('last_updated', $columns) ? ', last_updated = NOW()' : '';
                    $sql = "UPDATE $tableName SET " . implode(', ', $updateFields) . "$lastUpdatedField WHERE student_id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($updateValues);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Health questionnaire updated successfully',
                    'action' => 'updated',
                    'student_id' => $studentId,
                    'fields_updated' => count($updateFields)
                ]);
            } else {
                // Insert new record
                $healthData['student_id'] = $studentId;
                
                // Add timestamp fields if they exist
                if (in_array('submission_date', $columns)) {
                    $healthData['submission_date'] = date('Y-m-d H:i:s');
                }
                if (in_array('created_at', $columns)) {
                    $healthData['created_at'] = date('Y-m-d H:i:s');
                }
                if (in_array('is_completed', $columns)) {
                    $healthData['is_completed'] = 1;
                }
                
                // Only include fields that exist in the table
                $validData = [];
                foreach ($healthData as $key => $value) {
                    if (in_array($key, $columns)) {
                        $validData[$key] = $value;
                    }
                }
                
                $fields = array_keys($validData);
                $placeholders = array_fill(0, count($fields), '?');
                
                $sql = "INSERT INTO $tableName (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($validData));
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Health questionnaire saved successfully',
                    'action' => 'inserted',
                    'student_id' => $studentId,
                    'fields_saved' => count($fields),
                    'table_used' => $tableName,
                    'primary_key' => $primaryKey,
                    'total_columns' => count($columns)
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'error_details' => $e->getFile() . ':' . $e->getLine()
            ]);
        }
        
    } else {
        // GET request - return simple response
        echo json_encode([
            'success' => true,
            'message' => 'API is working',
            'method' => $method,
            'user_id' => $_SESSION['user_id']
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Fatal error: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
?>
