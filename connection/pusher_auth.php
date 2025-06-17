<?php
session_start();
require_once __DIR__ . '/../config/pusher.php';
require_once __DIR__ . '/../connection/config.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is authenticated
$authUser = $_SESSION['auth_user'] ?? [];
$userId = $authUser['admin_id'] ?? $authUser['supervisor_id'] ?? $authUser['student_id'] ?? $authUser['coordinators_id'] ?? null;
$userType = isset($authUser['admin_id']) ? 'admin' : 
            (isset($authUser['supervisor_id']) ? 'supervisor' : 
            (isset($authUser['student_id']) ? 'student' : 
            (isset($authUser['coordinators_id']) ? 'coordinator' : null)));

if (!$userId || !$userType) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

// Get the socket ID and channel name from the request
$socket_id = $_POST['socket_id'] ?? '';
$channel_name = $_POST['channel_name'] ?? '';

if (empty($socket_id) || empty($channel_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit();
}

// Verify the channel name
$expected_channel = "private-{$userType}-{$userId}";
if ($channel_name !== $expected_channel) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized channel']);
    exit();
}

// Include Pusher PHP library
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Composer dependencies not installed']);
    exit();
}

require_once $autoloadPath;

try {
    $pusher = new Pusher\Pusher(
        PUSHER_APP_KEY,
        PUSHER_APP_SECRET,
        PUSHER_APP_ID,
        ['cluster' => PUSHER_APP_CLUSTER, 'useTLS' => true]
    );
    
    $auth = $pusher->authorizeChannel($channel_name, $socket_id);
    echo $auth;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Pusher authentication failed', 'message' => $e->getMessage()]);
    exit();
}