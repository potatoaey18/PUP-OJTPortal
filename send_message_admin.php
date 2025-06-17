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

// Get and validate input
$coordinator_id = $_SESSION['auth_user']['coordinators_id'];
$admin_id = isset($_POST['admin_id']) ? trim($_POST['admin_id']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validate input
if (empty($admin_id) || empty($content)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Sanitize input
$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

// Start transaction
$conn->beginTransaction();

// Initialize variables to avoid undefined variable warnings
$message_id = null;
$message_data = null;

try {
    // Find or create conversation
    error_log("Finding or creating conversation for admin_id: $admin_id, coordinator_id: $coordinator_id");
    try {
        $stmt = $conn->prepare("SELECT id FROM conversations_admin_coordinator WHERE admin_id = ? AND coordinator_id = ?");
        $stmt->execute([$admin_id, $coordinator_id]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$conversation) {
            error_log("No existing conversation found, creating new one");
            $stmt = $conn->prepare("INSERT INTO conversations_admin_coordinator (admin_id, coordinator_id, created_at) VALUES (?, ?, NOW())");
            if (!$stmt->execute([$admin_id, (string)$coordinator_id])) {
                $error = $stmt->errorInfo();
                throw new Exception("Failed to create conversation: " . ($error[2] ?? 'Unknown error'));
            }
            $conversation_id = $conn->lastInsertId();
            error_log("Created new conversation with ID: $conversation_id");
        } else {
            $conversation_id = $conversation['id'];
            error_log("Found existing conversation with ID: $conversation_id");
        }
    } catch (PDOException $e) {
        error_log("Database error in conversation handling: " . $e->getMessage());
        throw new Exception("Database error while handling conversation");
    }
    
    // First, get the supervisor's email from the supervisor table using supervisor_id
    $stmt = $conn->prepare("SELECT coordinators_email FROM coordinators_account WHERE id = ?");
    $stmt->execute([$coordinator_id]);
    $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$coordinator || empty($coordinator['coordinators_email'])) {
        throw new Exception("Coordinator email not found in database");
    }
    
    $coordinator_email = $coordinator['coordinators_email'];
    
    // Get the supervisor's user_id from the users table using their email
    $stmt = $conn->prepare("SELECT user_id FROM users_admin_coordinator WHERE email = ? AND user_type = 'coordinator'");
    $stmt->execute([$coordinator_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception("Coordinator not found in users table with email: " . $coordinator_email);
    }
    
    $user_id = $user['user_id'];
    
    // Insert message using the user_id from the users table
    try {
        error_log("Inserting new message into database");
        $stmt = $conn->prepare("INSERT INTO messages_admin_coordinator (conversation_id, sender_id, sender_type, content, created_at) VALUES (?, ?, 'coordinator', ?, NOW())");
        if (!$stmt->execute([$conversation_id, $user_id, $content])) {
            $error = $stmt->errorInfo();
            throw new Exception("Failed to insert message: " . ($error[2] ?? 'Unknown error'));
        }
        $message_id = $conn->lastInsertId();
        error_log("Message inserted with ID: $message_id");
        
        // Get the created message with proper datetime formatting
        $stmt = $conn->prepare(
            "SELECT m.*, 
            DATE_FORMAT(m.created_at, '%Y-%m-%d %H:%i:%s') as formatted_created_at,
            u.first_name, u.last_name
            FROM messages_admin_coordinator m
            LEFT JOIN users_admin_coordinator u ON m.sender_id = u.user_id
            WHERE m.id = ?"
        );
        if (!$stmt->execute([$message_id])) {
            $error = $stmt->errorInfo();
            throw new Exception("Failed to fetch created message: " . ($error[2] ?? 'Unknown error'));
        }
        $message_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$message_data) {
            throw new Exception("Failed to retrieve created message data");
        }
        
        error_log("Successfully retrieved created message data");
        
    } catch (PDOException $e) {
        error_log("Database error in message handling: " . $e->getMessage());
        throw new Exception("Database error while processing message");
    }
    
    // Try to update conversation's updated_at if the column exists
    try {
        $stmt = $conn->prepare("UPDATE conversations_admin_coordinator SET updated_at = NOW() WHERE id = ?");
        $stmt->execute([$conversation_id]);
    } catch (PDOException $e) {
        // Silently ignore if updated_at column doesn't exist
        if (strpos($e->getMessage(), 'updated_at') === false) {
            throw $e; // Re-throw if it's a different error
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Ensure we have the required data for the response
    if (empty($message_data)) {
        throw new Exception('Failed to retrieve sent message data');
    }
    
    // Prepare response data
    $response_data = [
        'id' => $message_id,
        'conversation_id' => $conversation_id,
        'sender_id' => $coordinator_id,
        'sender_type' => 'coordinator',
        'content' => $content,
        'created_at' => $message_data['formatted_created_at'],
        'sender_name' => trim($message_data['first_name'] . ' ' . $message_data['last_name'])
    ];
    
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
            
            // Get student name for the notification
            $stmt = $conn->prepare("SELECT first_name, last_name FROM admin_account WHERE id = ?");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            $admin_name = $admin ? trim($admin['first_name'] . ' ' . $admin['last_name']) : 'Admin';
            
            // Trigger event to the student's private channel
            $pusher->trigger(
                'private-admin-' . $admin_id,
                'new_message',
                [
                    'id' => $message_id,
                    'conversation_id' => $conversation_id,
                    'sender_id' => $coordinator_id,
                    'sender_type' => 'coordinator',
                    'content' => $content,
                    'created_at' => $message_data['formatted_created_at'],
                    'sender_name' => trim($message_data['first_name'] . ' ' . $message_data['last_name']),
                    'admin_id' => $admin_id,
                    'admin_name' => $admin_name
                ]
            );
            
            // Also trigger to supervisor's channel for sync across devices
            $pusher->trigger(
                'private-coordinator-' . $coordinator_id,
                'new_message',
                [
                    'id' => $message_id,
                    'conversation_id' => $conversation_id,
                    'sender_id' => $coordinator_id,
                    'sender_type' => 'coordinator',
                    'content' => $content,
                    'created_at' => $message_data['formatted_created_at'],
                    'sender_name' => 'You',
                    'admin_id' => $admin_id,
                    'admin_name' => $admin_name
                ]
            );
            
        } catch (Exception $e) {
            // Log the error but don't fail the request
            error_log('Pusher Error: ' . $e->getMessage());
            
            // Log the full exception for debugging
            error_log('Pusher Exception: ' . $e);
            
            // Also log the Pusher configuration (without sensitive data)
            error_log('Pusher Config - Key: ' . (defined('PUSHER_APP_KEY') ? 'Set' : 'Not Set') . 
                     ', Cluster: ' . (defined('PUSHER_APP_CLUSTER') ? PUSHER_APP_CLUSTER : 'Not Set'));
        }
    } else {
        error_log('Pusher not configured. PUSHER_APP_KEY: ' . (defined('PUSHER_APP_KEY') ? 'Set' : 'Not Set'));
    }
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Message sent successfully',
        'data' => $response_data
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database error',
        'debug' => $e->getMessage()
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'An error occurred',
        'debug' => $e->getMessage()
    ]);
}
?>
