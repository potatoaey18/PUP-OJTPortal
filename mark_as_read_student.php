<?php
include '../connection/config.php';
session_start();

header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check authentication
if (!isset($_SESSION['auth_user']['coordinators_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$coordinators_id = $_SESSION['auth_user']['coordinators_id'];
$message_ids = $_POST['message_ids'] ?? [];

// Convert to array if it's not already
if (!is_array($message_ids) && !empty($message_ids)) {
    $message_ids = [$message_ids];
}

// Validate input
if (empty($message_ids)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No message IDs provided']);
    exit();
}

// Ensure all message IDs are integers
$message_ids = array_filter($message_ids, function($id) {
    return is_numeric($id) && $id > 0;
});

if (empty($message_ids)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid message IDs']);
    exit();
}

try {
    // Create placeholders for the IN clause
    $placeholders = rtrim(str_repeat('?,', count($message_ids)), ',');
    
    // First, verify these messages belong to a conversation with this student
    $sql = "UPDATE messages m 
            JOIN conversations_coordinator_student c ON m.conversation_id = c.id
            SET m.is_read = 1 
            WHERE m.id IN ($placeholders) 
            AND c.coordinators_id = ?";
    
    // Add student_id to the parameters
    $params = array_merge($message_ids, [$coordinators_id]);
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute($params);
    $affectedRows = $stmt->rowCount();
    
    if ($result) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Messages marked as read',
            'affected_rows' => $affectedRows
        ]);
    } else {
        throw new Exception('Failed to execute update query');
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database error',
        'debug' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'An error occurred',
        'debug' => $e->getMessage()
    ]);
}
?>
