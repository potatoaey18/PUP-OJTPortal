<?php
include '../connection/config.php';
session_start();

if (!isset($_SESSION['auth_user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$senderId = $_SESSION['auth_user']['student_uniqueID'];
$receiverId = $_POST['receiver_id'] ?? '';
$message = trim($_POST['message'] ?? '');

if (empty($receiverId) || empty($message)) {
    echo json_encode(['error' => 'Missing data']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO chat_system (sender_id, receiver_id, messages, date_only, time_only) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$senderId, $receiverId, $message, date('Y-m-d'), date('H:i:s')]);

echo json_encode(['success' => 'Message sent']);
?>