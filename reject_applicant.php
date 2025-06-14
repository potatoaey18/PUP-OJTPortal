<?php
include '../connection/config.php';
session_start();

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['intern_id'])) {
    try {
        $intern_id = $_POST['intern_id'];
        
    
        $conn->beginTransaction();

        file_put_contents(__DIR__ . '/reject_debug.log', "Rejecting Applicant ID: $intern_id\n", FILE_APPEND);

        $stmt = $conn->prepare("DELETE FROM student_credentials WHERE student_ID = ?");
        $stmt->execute([$intern_id]);
        file_put_contents(__DIR__ . '/reject_debug.log', "Deleted from student_credentials: " . $stmt->rowCount() . " rows\n", FILE_APPEND);

        $stmt = $conn->prepare("DELETE FROM students_data WHERE student_ID = ?");
        $stmt->execute([$intern_id]);
        file_put_contents(__DIR__ . '/reject_debug.log', "Deleted from students_data: " . $stmt->rowCount() . " rows\n", FILE_APPEND);

        $stmt = $conn->prepare("DELETE FROM intern_deployments WHERE intern_id = ?");
        $stmt->execute([$intern_id]);
        file_put_contents(__DIR__ . '/reject_debug.log', "Deleted from intern_deployments: " . $stmt->rowCount() . " rows\n", FILE_APPEND);

        $stmt = $conn->prepare("DELETE FROM attendance_records WHERE student_id = ?");
        $stmt->execute([$intern_id]);
        file_put_contents(__DIR__ . '/reject_debug.log', "Deleted from attendance_records: " . $stmt->rowCount() . " rows\n", FILE_APPEND);

        $stmt = $conn->prepare("DELETE FROM weekly_accomplishment_reports WHERE student_id = ?");
        $stmt->execute([$intern_id]);
        file_put_contents(__DIR__ . '/reject_debug.log', "Deleted from weekly_accomplishment_reports: " . $stmt->rowCount() . " rows\n", FILE_APPEND);

        $conn->commit();

        $response['status'] = 'success';
        $response['message'] = 'Applicant has been rejected and all data has been deleted.';
        
    } catch (Exception $e) {
        $conn->rollBack();
        $response['status'] = 'error';
        $response['message'] = 'Failed to reject applicant: ' . $e->getMessage();
        file_put_contents(__DIR__ . '/reject_error.log', date('Y-m-d H:i:s') . ' - Error: ' . $e->getMessage() . "\n", FILE_APPEND);
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
