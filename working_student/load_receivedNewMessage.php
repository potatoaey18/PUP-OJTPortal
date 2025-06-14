<?php
include '../../connection/config.php';
session_start();

if (!isset($_SESSION['auth_user']) || !isset($_POST['receiver_id'])) {
    echo '0';
    exit();
}

$senderId = $_SESSION['auth_user']['student_uniqueID'];
$receiverId = $_POST['receiver_id'];
$status = 'Sent'; // Assuming 'Sent' means unread

$stmt = $conn->prepare("SELECT COUNT(*) AS new_message_count FROM chat_system WHERE sender_id = ? AND receiver_id = ? AND status = ?");
$stmt->execute([$receiverId, $senderId, $status]);
$count = $stmt->fetch(PDO::FETCH_ASSOC)['new_message_count'];

echo $count;
?>