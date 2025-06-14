<?php
include '../../connection/config.php';
session_start();

if (!isset($_SESSION['auth_user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$senderId = $_SESSION['auth_user']['student_uniqueID'];
$receiverId = $_POST['receiver_id'] ?? '';

$stmt = $conn->prepare("
    SELECT 
        CASE 
            WHEN images IS NOT NULL THEN images 
            WHEN documents IS NOT NULL THEN documents 
            ELSE NULL 
        END AS file_path,
        CASE 
            WHEN images IS NOT NULL THEN 'image' 
            WHEN documents IS NOT NULL THEN 'document' 
            ELSE NULL 
        END AS file_type,
        CONCAT(date_only, ' ', time_only) AS timestamp
    FROM chat_system 
    WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) 
    AND (images IS NOT NULL OR documents IS NOT NULL)
    ORDER BY date_only DESC, time_only DESC
");
$stmt->execute([$senderId, $receiverId, $receiverId, $senderId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process rows to add file_name
$files = array_map(function($row) {
    return [
        'file_name' => basename($row['file_path']), // Extract file name from path
        'file_path' => $row['file_path'],
        'file_type' => $row['file_type'],
        'timestamp' => $row['timestamp']
    ];
}, $rows);

echo json_encode($files);
?>