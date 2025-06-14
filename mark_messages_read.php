<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in as supervisor
if (!isset($_SESSION['auth_user']) || $_SESSION['auth_user']['user_type'] !== 'supervisor') {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get the database connection
require_once '../connection/config.php';

try {
    // Get the raw POST data
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    // Validate input
    if (empty($input['message_ids']) || !is_array($input['message_ids'])) {
        throw new Exception('Invalid message IDs');
    }
    
    $messageIds = array_map('intval', $input['message_ids']);
    $studentId = isset($input['student_id']) ? intval($input['student_id']) : 0;
    $supervisorId = $_SESSION['auth_user']['supervisor_id'];
    
    if (empty($messageIds)) {
        throw new Exception('No message IDs provided');
    }
    
    // Prepare the SQL query
    $placeholders = rtrim(str_repeat('?,', count($messageIds)), ',');
    $query = "UPDATE messages 
              SET is_read = 1 
              WHERE id IN ($placeholders) 
              AND sender_type = 'student' 
              AND (SELECT supervisor_id FROM conversations WHERE id = messages.conversation_id) = ?";
    
    // Add supervisor_id to the parameters
    $params = array_merge($messageIds, [$supervisorId]);
    
    // Execute the query
    $stmt = $conn->prepare($query);
    $types = str_repeat('i', count($messageIds)) . 'i'; // 'i' for integer
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        // Update the last read timestamp for this conversation
        if ($studentId) {
            $updateQuery = "UPDATE conversations 
                           SET last_read_at = NOW() 
                           WHERE student_id = ? AND supervisor_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('ii', $studentId, $supervisorId);
            $updateStmt->execute();
            $updateStmt->close();
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Messages marked as read',
            'count' => $stmt->affected_rows
        ]);
    } else {
        throw new Exception('Failed to update messages');
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
