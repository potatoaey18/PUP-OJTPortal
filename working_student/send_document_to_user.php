<?php
include '../../connection/config.php';
session_start();

if (!isset($_SESSION['auth_user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$senderId = $_SESSION['auth_user']['student_uniqueID'];
$receiverId = $_POST['receiver_id'] ?? '';
$uploadDir = 'uploads/documents/';

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    echo json_encode(['error' => 'Upload directory creation failed']);
    exit();
}

$files = $_FILES['doc_toSEND'] ?? [];
if (empty($files['name'][0])) {
    echo json_encode(['error' => 'No documents selected']);
    exit();
}

$allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/vnd.ms-powerpoint', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
$responses = [];
foreach (array_keys($files['name']) as $i) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        $responses[] = ['error' => "Upload error for {$files['name'][$i]}"];
        continue;
    }

    if ($files['size'][$i] > 5 * 1024 * 1024) {
        $responses[] = ['error' => "File {$files['name'][$i]} too large"];
        continue;
    }

    $mime = mime_content_type($files['tmp_name'][$i]);
    if (!in_array($mime, $allowedMimes)) {
        $responses[] = ['error' => "Invalid type for {$files['name'][$i]}"];
        continue;
    }

    $fileName = uniqid('doc_', true) . '.' . pathinfo($files['name'][$i], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
        $stmt = $conn->prepare("INSERT INTO chat_system (sender_id, receiver_id, messages, documents, date_only, time_only) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$senderId, $receiverId, 'Document shared', $filePath, date('Y-m-d'), date('H:i:s')]);
        $responses[] = ['success' => "Document {$files['name'][$i]} uploaded", 'path' => $filePath];
    } else {
        $responses[] = ['error' => "Failed to move {$files['name'][$i]}"];
    }
}

echo json_encode($responses);
?>