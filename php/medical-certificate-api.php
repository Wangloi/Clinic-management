<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Include database connection
require_once 'connection.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['purpose']) || !isset($input['contact'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$purpose = trim($input['purpose']);
$notes = trim($input['notes'] ?? '');
$contact = trim($input['contact']);
$student_id = $input['student_id'] ?? null;

// Validate required fields
if (empty($purpose) || empty($contact)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Purpose and contact number are required']);
    exit;
}

// Generate reference number
$reference_number = 'MC-' . date('Ymd') . '-' . rand(1000, 9999);

// Prepare SQL statement
$sql = "INSERT INTO medical_certificate_requests (student_id, purpose, notes, contact_number, reference_number, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $pdo->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$stmt->bindParam(1, $student_id);
$stmt->bindParam(2, $purpose);
$stmt->bindParam(3, $notes);
$stmt->bindParam(4, $contact);
$stmt->bindParam(5, $reference_number);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Medical certificate request submitted successfully',
        'reference_number' => $reference_number
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit request']);
}

$stmt->closeCursor();
?>
