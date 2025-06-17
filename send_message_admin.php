<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error_log');

// Start output buffering to catch any unexpected output
ob_start();

include '../connection/config.php';
session_start();

// Log the start of the script
error_log("\n===== Starting send_message_admin.php =====");
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

// Function to send JSON response and exit
function sendResponse($status, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    error_log("Response: " . json_encode($response));
    exit();
}

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
if (!isset($_SESSION['auth_user']['student_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized - Student ID not found in session']);
    exit();
}

// Get and validate input
$student_id = $_SESSION['auth_user']['student_id'];
$admin_id = isset($_POST['admin_id']) ? (int)$_POST['admin_id'] : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Log received data
error_log("Processing message - Admin ID: $admin_id, Content: " . substr($content, 0, 50) . (strlen($content) > 50 ? '...' : ''));

// Validate input
if (empty($admin_id) || empty($content)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Sanitize input
$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

// Start transaction
try {
    $conn->beginTransaction();
    error_log("Database transaction started");
} catch (PDOException $e) {
    $errorMsg = "Failed to start transaction: " . $e->getMessage();
    error_log($errorMsg);
    sendResponse('error', 'Database error: ' . $e->getMessage(), null, 500);
}

// Initialize variables to avoid undefined variable warnings
$message_id = null;
$message_data = null;

try {
    // Find or create conversation
    $stmt = $conn->prepare("SELECT id FROM conversations_admin_student WHERE admin_id = ? AND student_id = ?");
    $stmt->execute([$admin_id, $student_id]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$conversation) {
        $stmt = $conn->prepare("INSERT INTO conversations_admin_student (admin_id, student_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$admin_id, $student_id]);
        $conversation_id = $conn->lastInsertId();
    } else {
        $conversation_id = $conversation['id'];
    }
    
    // Get student's information from users_admin_student table
    // First, get the student's email from students_data
    $stmt = $conn->prepare("SELECT stud_email FROM students_data WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student || empty($student['stud_email'])) {
        throw new Exception("Student with ID $student_id not found in students_data table or missing email");
    }
    
    $student_email = $student['stud_email'];
    
    // Now find the user in users_admin_student by email and user_type
    $stmt = $conn->prepare("SELECT id FROM users_admin_student WHERE email = ? AND user_type = 'student'");
    $stmt->execute([$student_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || empty($user['id'])) {
        // If user not found, try to create one
        $stmt = $conn->prepare("SELECT first_name, last_name FROM students_data WHERE id = ?");
        $stmt->execute([$student_id]);
        $student_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student_info) {
            throw new Exception("Could not retrieve student information");
        }
        
        // Insert new user into users_admin_student
        $stmt = $conn->prepare(
            "INSERT INTO users_admin_student (user_type, user_id, email, first_name, last_name, created_at) " .
            "VALUES ('student', ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $student_id,
            $student_email,
            $student_info['first_name'],
            $student_info['last_name']
        ]);
        
        $sender_id = $conn->lastInsertId();
        error_log("Created new user in users_admin_student with ID: " . $sender_id);
    } else {
        $sender_id = $user['id'];
        error_log("Found existing user in users_admin_student with ID: " . $sender_id);
    }
    
    // Insert message into the database
    try {
        $stmt = $conn->prepare("INSERT INTO messages_admin_student (conversation_id, sender_id, sender_type, content, created_at) VALUES (?, ?, 'student', ?, NOW())");
        $stmt->execute([$conversation_id, $sender_id, $content]);
        $message_id = $conn->lastInsertId();
        error_log("Message inserted successfully. Message ID: " . $message_id);
    } catch (PDOException $e) {
        $errorMsg = "Failed to insert message: " . $e->getMessage() . "\n";
        $errorMsg .= "Conversation ID: $conversation_id\n";
        $errorMsg .= "Sender ID: $sender_id\n";
        $errorMsg .= "Content: " . substr($content, 0, 100) . (strlen($content) > 100 ? '...' : '');
        error_log($errorMsg);
        throw new Exception("Failed to save message: " . $e->getMessage());
    }
    
    // Get the created message with proper datetime formatting
    try {
        $stmt = $conn->prepare(
            "SELECT m.*, 
            DATE_FORMAT(m.created_at, '%Y-%m-%d %H:%i:%s') as formatted_created_at,
            u.first_name, u.last_name
            FROM messages_admin_student m
            LEFT JOIN users_admin_student u ON u.id = m.sender_id
            WHERE m.id = ?"
        );
        $stmt->execute([$message_id]);
        $message_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$message_data) {
            throw new Exception("Failed to retrieve the sent message");
        }
        
        error_log("Retrieved message data: " . print_r($message_data, true));
        
    } catch (PDOException $e) {
        $errorMsg = "Failed to retrieve message: " . $e->getMessage() . "\n";
        $errorMsg .= "Message ID: $message_id\n";
        error_log($errorMsg);
        throw new Exception("Failed to retrieve message: " . $e->getMessage());
    }
    
    // Try to update conversation's updated_at if the column exists
    try {
        $stmt = $conn->prepare("UPDATE conversations_admin_student SET updated_at = NOW() WHERE id = ?");
        $stmt->execute([$conversation_id]);
    } catch (PDOException $e) {
        // Silently ignore if updated_at column doesn't exist
        if (strpos($e->getMessage(), 'updated_at') === false) {
            throw $e; // Re-throw if it's a different error
        }
    }
    
    // Commit transaction
    $conn->commit();
    error_log("Message sent successfully. Message ID: " . $message_id);
    
    // Ensure we have the required data for the response
    if (empty($message_data)) {
        throw new Exception('Failed to retrieve sent message data');
    }
    
    // Get student's name for the response
    $stmt = $conn->prepare("SELECT first_name, last_name FROM students_data WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    $student_name = $student ? trim($student['first_name'] . ' ' . $student['last_name']) : 'Student';
    
    // Prepare response data
    $response_data = [
        'id' => $message_id,
        'conversation_id' => $conversation_id,
        'sender_id' => $student_id,
        'sender_type' => 'student',
        'content' => $content,
        'created_at' => $message_data['formatted_created_at'],
        'sender_name' => $student_name
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
            
            // Get admin name for the notification
            $stmt = $conn->prepare("SELECT first_name, last_name FROM admin_account WHERE id = ?");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            $admin_name = $admin ? trim($admin['first_name'] . ' ' . $admin['last_name']) : 'Admin';
            
            // Trigger event to the admin's private channel
            $pusher->trigger(
                'private-admin-' . $admin_id,
                'new_message',
                [
                    'id' => $message_id,
                    'conversation_id' => $conversation_id,
                    'sender_id' => $student_id,
                    'sender_type' => 'student',
                    'content' => $content,
                    'created_at' => $message_data['formatted_created_at'],
                    'sender_name' => $student_name,
                    'admin_id' => $admin_id,
                    'admin_name' => $admin_name
                ]
            );
            
            // Also trigger to supervisor's channel for sync across devices
            $pusher->trigger(
                'private-admin-' . $admin_id,
                'new_message',
                [
                    'id' => $message_id,
                    'conversation_id' => $conversation_id,
                    'sender_id' => $student_id,
                    'sender_type' => 'student',
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
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    $errorMsg = "Database error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
    error_log($errorMsg);
    error_log("SQL error code: " . $e->getCode());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage(),
        'error_details' => [
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
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
