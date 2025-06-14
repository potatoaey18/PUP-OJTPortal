<?php
include '../connection/config.php';
session_start();

if (!isset($_SESSION['auth_user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$senderId = $_SESSION['auth_user']['student_uniqueID'];
$receiverId = $_POST['receiver_id'] ?? '';
$uploadDir = 'uploads/images/';

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    echo json_encode(['error' => 'Upload directory creation failed']);
    exit();
}

$files = $_FILES['img_toSEND'] ?? [];
if (empty($files['name'][0])) {
    echo json_encode(['error' => 'No images selected']);
    exit();
}

$responses = [];
foreach (array_keys($files['name']) as $i) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        $responses[] = ['error' => "Upload error for {$files['name'][$i]}"];
        continue;
    }

    if ($files['size'][$i] > 5 * 1024 * 1024) { // 5MB limit
        $responses[] = ['error' => "File {$files['name'][$i]} too large"];
        continue;
    }

    $mime = mime_content_type($files['tmp_name'][$i]);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
        $responses[] = ['error' => "Invalid type for {$files['name'][$i]}"];
        continue;
    }

    $fileName = uniqid('img_', true) . '.' . pathinfo($files['name'][$i], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
        $stmt = $conn->prepare("INSERT INTO chat_system (sender_id, receiver_id, messages, images, date_only, time_only) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$senderId, $receiverId, 'Image shared', $filePath, date('Y-m-d'), date('H:i:s')]);
        $responses[] = ['success' => "Image {$files['name'][$i]} uploaded", 'path' => $filePath];
    } else {
        $responses[] = ['error' => "Failed to move {$files['name'][$i]}"];
    }
}

echo json_encode($responses);
?>