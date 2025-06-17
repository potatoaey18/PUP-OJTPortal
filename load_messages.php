<?php
include '../connection/config.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['auth_user']['student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$student_id = $_SESSION['auth_user']['student_id'];
$receiver_id = $_GET['receiver_id'] ?? '';
$receiver_type = $_GET['receiver_type'] ?? 'supervisor';

// Validate input
if (empty($receiver_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Receiver ID is required']);
    exit();
}

try {
    // Mark received messages as read
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 
                           WHERE sender_id = ? AND sender_type = ? 
                           AND receiver_id = ? AND receiver_type = 'student'");
    $stmt->execute([$receiver_id, $receiver_type, $student_id]);
    
    // Get messages between the student and supervisor
    $stmt = $conn->prepare("SELECT * FROM messages 
                           WHERE ((sender_id = ? AND sender_type = 'student' AND receiver_id = ? AND receiver_type = ?)
                           OR (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = 'student'))
                           ORDER BY created_at ASC");
    $stmt->execute([
        $student_id, $receiver_id, $receiver_type,
        $receiver_id, $receiver_type, $student_id
    ]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format timestamps
    foreach ($messages as &$message) {
        $message['created_at'] = date('Y-m-d H:i:s', strtotime($message['created_at']));
        $message['is_sender'] = ($message['sender_id'] == $student_id && $message['sender_type'] == 'student');
    }
    
    echo json_encode([
        'status' => 'success',
        'messages' => $messages
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred']);
}
?>
