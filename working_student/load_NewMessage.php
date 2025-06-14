<?php
include '../../connection/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['auth_user'])) {
    echo json_encode(['error' => 'You are not logged in!']);
    exit();
}

if (!isset($_POST['receiver_id'])) {
    echo json_encode(['html' => '', 'last_message_id' => 0]);
    exit();
}

$senderId = $_SESSION['auth_user']['student_uniqueID'];
$receiverId = $_POST['receiver_id'];
$lastMessageId = isset($_POST['last_message_id']) ? (int)$_POST['last_message_id'] : 0;

$stmt = $conn->prepare("
    SELECT id, sender_id, messages, images, date_only, time_only 
    FROM chat_system 
    WHERE ((sender_id = ? AND receiver_id = ?) 
    OR (sender_id = ? AND receiver_id = ?)) 
    AND id > ? 
    ORDER BY date_only ASC, time_only ASC
");
$stmt->execute([$senderId, $receiverId, $receiverId, $senderId, $lastMessageId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = [];
$lastId = $lastMessageId;

foreach ($messages as $message) {
    $html = ($message['sender_id'] == $senderId) ? '<div class="chat-message-right">' : '<div class="chat-message-left">';
    $html .= '<div class="chat-message-text">';
    if (!empty($message['messages'])) {
        $html .= htmlspecialchars($message['messages']);
    }
    if (!empty($message['images'])) {
        $fileType = mime_content_type($_SERVER['DOCUMENT_ROOT'] . parse_url($message['images'], PHP_URL_PATH));
        if (strpos($fileType, 'image/') === 0) {
            $html .= '<br><img src="' . htmlspecialchars($message['images']) . '" alt="Image" style="max-width: 200px; border-radius: 8px;">';
        } else {
            $html .= '<br><a href="' . htmlspecialchars($message['images']) . '" target="_blank" class="btn btn-link">Download File</a>';
        }
    }
    $html .= '<small class="d-block text-muted">' . htmlspecialchars($message['date_only'] . ' ' . $message['time_only']) . '</small>';
    $html .= '</div></div>';
    $output[] = ['id' => $message['id'], 'html' => $html];
    $lastId = max($lastId, $message['id']);
}

echo json_encode(['messages' => $output, 'last_message_id' => $lastId]);
?>