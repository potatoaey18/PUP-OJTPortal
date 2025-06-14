<?php
include '../connection/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['intern_id'])) {
    $intern_id = $_POST['intern_id'];

    $stmt = $conn->prepare("SELECT ojt_status FROM students_data WHERE student_ID = ?");
    $stmt->execute([$intern_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['status' => $result['ojt_status']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Student not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
