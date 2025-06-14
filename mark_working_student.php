<?php
ob_start();
session_start();
header('Content-Type: application/json');
include '../connection/config.php';
$response = ['success' => false, 'message' => 'Unknown error'];
error_log("Session data: " . print_r($_SESSION, true));

if (!isset($_SESSION['auth_user']['student_id']) || $_SESSION['auth_user']['student_id'] == 0) {
    $response['message'] = 'Session invalid or expired.';
    echo json_encode($response);
    exit;
}

$studID = $_SESSION['auth_user']['student_id'];
try {
    if (!($conn instanceof PDO)) {
        $response['message'] = 'Invalid database connection.';
        echo json_encode($response);
        exit;
    }
    $stmt = $conn->prepare("UPDATE students_data SET is_working_student = 'yes' WHERE id = ?");
    if ($stmt->execute([$studID])) {
        $response['success'] = true;
        $response['message'] = 'Test update successful.';
    } else {
        $response['message'] = 'Update failed.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
    error_log("Database error: " . $e->getMessage());
}
echo json_encode($response);
ob_end_flush();
?>