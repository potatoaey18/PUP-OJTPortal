<?php
session_start();
require_once '../connection/config.php';

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Prevent any output before headers
if (headers_sent($filename, $linenum)) {
    die("Headers already sent in $filename on line $linenum");
}

// Check if user is logged in
if (!isset($_SESSION['auth_user']['student_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get input data
$student_id = $_SESSION['auth_user']['student_id'];
$supervisor_id = $_POST['supervisor_id'] ?? '';
$content = trim($_POST['content'] ?? '');

// Validate input
if (empty($supervisor_id) || empty($content)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Supervisor ID and message content are required',
        'debug' => [
            'supervisor_id' => $supervisor_id,
            'content_length' => strlen($content)
        ]
    ]);
    exit();
}

try {
    // Start transaction
    $conn->beginTransaction();

    // 1. Get or create user in users table
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->execute([$student_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        try {
            // First try to insert with username if column exists
            $stmt = $conn->prepare("INSERT INTO users (user_id, username) VALUES (?, ?)");
            $stmt->execute([$student_id, 'student_' . $student_id]);
        } catch (PDOException $e) {
            // If username column doesn't exist, try without it
            if (strpos($e->getMessage(), 'username') !== false) {
                $stmt = $conn->prepare("INSERT INTO users (user_id) VALUES (?)");
                $stmt->execute([$student_id]);
            } else {
                throw $e; // Re-throw if it's a different error
            }
        }
    }
    
    // 2. Get or create conversation
    $stmt = $conn->prepare("
        SELECT id FROM conversations 
        WHERE student_id = ? AND supervisor_id = ?
    ");
    $stmt->execute([$student_id, $supervisor_id]);
    $conversation = $stmt->fetch();
    
    if ($conversation) {
        $conversation_id = $conversation['id'];
    } else {
        try {
            // First try with both created_at and updated_at
            $stmt = $conn->prepare("
                INSERT INTO conversations (student_id, supervisor_id, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ");
            $stmt->execute([$student_id, $supervisor_id]);
        } catch (PDOException $e) {
            // If updated_at column doesn't exist, try without it
            if (strpos($e->getMessage(), 'updated_at') !== false) {
                $stmt = $conn->prepare("
                    INSERT INTO conversations (student_id, supervisor_id, created_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt->execute([$student_id, $supervisor_id]);
            } else {
                throw $e; // Re-throw if it's a different error
            }
        }
        $conversation_id = $conn->lastInsertId();
    }
    
    // 3. Insert the message
    try {
        // First try with both created_at and updated_at
        $stmt = $conn->prepare("
            INSERT INTO messages (conversation_id, sender_id, content, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$conversation_id, $student_id, $content]);
    } catch (PDOException $e) {
        // If updated_at column doesn't exist, try without it
        if (strpos($e->getMessage(), 'updated_at') !== false) {
            $stmt = $conn->prepare("
                INSERT INTO messages (conversation_id, sender_id, content, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$conversation_id, $student_id, $content]);
        } else {
            throw $e; // Re-throw if it's a different error
        }
    }
    $message_id = $conn->lastInsertId();
    
    // Try to update conversation's updated_at if the column exists
    try {
        $stmt = $conn->prepare("
            UPDATE conversations 
            SET updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$conversation_id]);
    } catch (PDOException $e) {
        // Silently fail if updated_at column doesn't exist
        if (strpos($e->getMessage(), 'updated_at') === false) {
            throw $e; // Re-throw if it's a different error
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Get the created message with sender info
    try {
        $stmt = $conn->prepare("
            SELECT m.*, 
            DATE_FORMAT(m.created_at, '%Y-%m-%d %H:%i:%s') as formatted_created_at,
            COALESCE(
                (SELECT CONCAT(first_name, ' ', last_name) FROM students_data WHERE id = ?),
                'Unknown Sender'
            ) as sender_name
            FROM messages m
            WHERE m.id = ?
        ");
        $stmt->execute([$student_id, $message_id]);
        $message_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$message_data) {
            throw new Exception('Failed to retrieve sent message');
        }
        
        // Prepare response data
        $response_data = [
            'id' => $message_id,
            'conversation_id' => $conversation_id,
            'sender_id' => $student_id,
            'sender_type' => 'student',
            'content' => $content,
            'created_at' => $message_data['formatted_created_at'],
            'sender_name' => $message_data['sender_name']
        ];
    } catch (PDOException $e) {
        // If there's an error with the detailed query, return basic message data
        $response_data = [
            'id' => $message_id,
            'conversation_id' => $conversation_id,
            'sender_id' => $student_id,
            'sender_type' => 'student',
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'sender_name' => 'You'
        ];
    }
    
    // Send real-time notification
    if (defined('PUSHER_APP_KEY') && !empty(PUSHER_APP_KEY)) {
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            $pusher = new Pusher\Pusher(
                PUSHER_APP_KEY,
                PUSHER_APP_SECRET,
                PUSHER_APP_ID,
                [
                    'cluster' => PUSHER_APP_CLUSTER,
                    'useTLS' => true,
                    'encrypted' => true
                ]
            );
            
            // Trigger to supervisor's private channel
            $pusher->trigger(
                'private-supervisor-' . $supervisor_id, // Channel name matches supervisor's subscription
                'new_message',
                [
                    'id' => $message_id,
                    'conversation_id' => $conversation_id,
                    'sender_id' => $student_id,
                    'sender_type' => 'student',
                    'content' => $content,
                    'created_at' => $response_data['created_at'],
                    'sender_name' => $response_data['sender_name']
                ]
            );
            
            // Also trigger to student's channel if needed
            $pusher->trigger(
                'private-student-' . $student_id,
                'message_sent',
                [
                    'status' => 'delivered',
                    'message_id' => $message_id,
                    'conversation_id' => $conversation_id
                ]
            );
            
        } catch (Exception $e) {
            // Log Pusher error but don't fail the request
            error_log("Pusher error: " . $e->getMessage());
        }
    }
    
    // Prepare and send success response
    $response = [
        'status' => 'success',
        'message' => 'Message sent successfully',
        'data' => [
            'message_id' => $message_id,
            'created_at' => $response_data['created_at']
        ]
    ];
    
    // Clear any previous output
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    
    error_log("Database error: " . $e->getMessage());
    // Clear any previous output
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database error',
        'debug' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Error: " . $e->getMessage());
    
    // Clear any previous output
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'An error occurred',
        'debug' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}