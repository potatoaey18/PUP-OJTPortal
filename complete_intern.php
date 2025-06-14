<?php
include '../connection/config.php';
session_start();

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['intern_id'])) {
    try {
        $intern_id = $_POST['intern_id'];


        file_put_contents(__DIR__ . '/complete_debug.log', "Completing Intern ID: $intern_id\n", FILE_APPEND);
        $conn->beginTransaction();
        $deleteStmt = $conn->prepare("DELETE FROM intern_deployments WHERE intern_id = ?");
        $deleteStmt->execute([$intern_id]);


        if ($deleteStmt->rowCount() > 0) {
            file_put_contents(__DIR__ . '/complete_debug.log', "Intern ID $intern_id deleted from intern_deployments\n", FILE_APPEND);
        } else {
            file_put_contents(__DIR__ . '/complete_debug.log', "Failed to delete Intern ID $intern_id from intern_deployments\n", FILE_APPEND);
        }


        $updateStmt = $conn->prepare("UPDATE students_data SET ojt_status = 'Completed' WHERE student_ID = ?");
        $updateStmt->execute([$intern_id]);

        $conn->commit();

        $response['status'] = 'success';
        $response['message'] = 'Intern has been marked as completed.';
    } catch (Exception $e) {
        $conn->rollBack();
        $response['status'] = 'error';
        $response['message'] = 'Failed to complete intern: ' . $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
