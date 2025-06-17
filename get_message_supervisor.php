<?php
include '../connection/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['auth_user']['coordinators_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$coordinator_id = $_SESSION['auth_user']['coordinators_id'];
$supervisor_id = isset($_GET['supervisor_id']) ? intval($_GET['supervisor_id']) : 0;

if (!$supervisor_id) {
    echo json_encode(['status' => 'error', 'message' => 'Supervisor ID is required']);
    exit();
}

try {
    // Find or create conversation (aligned with supervisor's send_message.php)
    $stmt = $conn->prepare("SELECT id FROM conversations_coordinator_hte WHERE coordinator_id = ? AND supervisor_id = ?");
    $stmt->execute([$coordinator_id, $supervisor_id]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$conversation) {
        $stmt = $conn->prepare("INSERT INTO conversations_coordinator_hte (coordinator_id, supervisor_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$coordinator_id, $supervisor_id]);
        $conversation_id = $conn->lastInsertId();
    } else {
        $conversation_id = $conversation['id'];
    }
    
    // Mark messages as read (similar to supervisor's approach)
    $updateStmt = $conn->prepare("
        UPDATE messages 
        SET is_read = 1 
        WHERE conversation_id = ? AND sender_id = ? AND is_read = 0
    ");
    
    // Get the student's record from students_data
    $stmt = $conn->prepare("SELECT id, coordinators_email FROM coordinators_account WHERE id = ?");
    $stmt->execute([$coordinator_id]);
    $coordinator_account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$coordinator_account) {
        throw new Exception("Student not found in students_data table");
    }
    
    // Get the student's ID to use as the user ID
    $user_id = $coordinator_account['id'];
    
    // Mark messages as read for the current student
    $updateStmt->execute([$conversation_id, $user_id]);
    
    // Fetch messages with sender information (aligned with supervisor's structure)
    $stmt = $conn->prepare("
        SELECT 
            m.id,
            m.conversation_id,
            m.sender_id,
            m.sender_type,
            m.content,
            m.is_read,
            DATE_FORMAT(m.created_at, '%Y-%m-%d %H:%i:%s') as formatted_created_at,
            CASE 
                WHEN m.sender_type = 'coordinator' THEN co.first_name
                WHEN m.sender_type = 'supervisor' THEN sv.first_name
                ELSE 'Unknown'
            END as first_name,
            CASE 
                WHEN m.sender_type = 'coordinator' THEN co.last_name
                WHEN m.sender_type = 'supervisor' THEN sv.last_name
                ELSE ''
            END as last_name,
            CASE 
                WHEN m.sender_type = 'coordinator' THEN co.coordinators_email
                WHEN m.sender_type = 'supervisor' THEN sv.supervisor_email
                ELSE ''
            END as email
        FROM messages_coordinator_supervisor m
        LEFT JOIN coordinators_account co ON m.sender_id = co.id AND m.sender_type = 'coordinator'
        LEFT JOIN supervisor sv ON m.sender_id = sv.id AND m.sender_type = 'supervisor'
        WHERE m.conversation_id = ?
        ORDER BY m.created_at ASC
    ");
    
    $stmt->execute([$conversation_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response to match supervisor's structure
    $formattedMessages = [];
    foreach ($messages as $message) {
        $formattedMessages[] = [
            'id' => $message['id'],
            'conversation_id' => $message['conversation_id'],
            'sender_id' => $message['sender_id'],
            'sender_type' => $message['sender_type'],
            'content' => $message['content'],
            'is_read' => $message['is_read'],
            'created_at' => $message['formatted_created_at'],
            'first_name' => $message['first_name'],
            'last_name' => $message['last_name'],
            'email' => $message['email']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'messages' => $formattedMessages
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_messages.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load messages: ' . $e->getMessage()
    ]);
}
?>