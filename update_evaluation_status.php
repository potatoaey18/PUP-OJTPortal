<?php
include '../connection/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$student_id = $_POST['student_id'] ?? '';
$status = $_POST['status'] ?? '';

if (empty($student_id) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM student_evaluations WHERE student_id = ?");
    $stmt->execute([$student_id]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $conn->prepare("UPDATE student_evaluations SET evaluation_status = ? WHERE student_id = ?");
        $result = $stmt->execute([$status, $student_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO student_evaluations (student_id, evaluation_status) VALUES (?, ?)");
        $result = $stmt->execute([$student_id, $status]);
    }
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update evaluation status']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
