<?php
include '../connection/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['auth_user']['student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$student_id = $_SESSION['auth_user']['student_id'];
$supervisor_id = isset($_GET['supervisor_id']) ? intval($_GET['supervisor_id']) : 0;

if (!$supervisor_id) {
    echo json_encode(['status' => 'error', 'message' => 'Supervisor ID is required']);
    exit();
}

try {
    // Find or create conversation (aligned with supervisor's send_message.php)
    $stmt = $conn->prepare("SELECT id FROM conversations WHERE student_id = ? AND supervisor_id = ?");
    $stmt->execute([$student_id, $supervisor_id]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$conversation) {
        $stmt = $conn->prepare("INSERT INTO conversations (student_id, supervisor_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$student_id, $supervisor_id]);
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
    $stmt = $conn->prepare("SELECT id, stud_email FROM students_data WHERE id = ?");
    $stmt->execute([$student_id]);
    $student_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student_data) {
        throw new Exception("Student not found in students_data table");
    }
    
    // Get the student's ID to use as the user ID
    $user_id = $student_data['id'];
    
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
                WHEN m.sender_type = 'student' THEN sd.first_name
                WHEN m.sender_type = 'supervisor' THEN sv.first_name
                ELSE 'Unknown'
            END as first_name,
            CASE 
                WHEN m.sender_type = 'student' THEN sd.last_name
                WHEN m.sender_type = 'supervisor' THEN sv.last_name
                ELSE ''
            END as last_name,
            CASE 
                WHEN m.sender_type = 'student' THEN sd.stud_email
                WHEN m.sender_type = 'supervisor' THEN sv.supervisor_email
                ELSE ''
            END as email
        FROM messages m
        LEFT JOIN students_data sd ON m.sender_id = sd.id AND m.sender_type = 'student'
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