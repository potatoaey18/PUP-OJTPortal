<?php
include '../connection/config.php';
session_start();

$response = array();

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_POST['intern_id']) &&
    isset($_POST['reason']) &&
    isset($_FILES['evidence_file'])
) {
    try {
        $intern_id = $_POST['intern_id'];
        $drop_reason = trim($_POST['reason']);
        $evidence_file = $_FILES['evidence_file'];

       
        file_put_contents(__DIR__ . '/drop_debug.log', "Dropping Intern ID: $intern_id\n", FILE_APPEND);

        
        $allowed_types = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png',
    'image/jpg',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
    'application/zip',
    'application/x-zip-compressed',
    'multipart/x-zip',
    'application/x-compressed',
];
        $max_size = 5 * 1024 * 1024; 
        if ($evidence_file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Evidence file upload error.');
        }
        if (!in_array($evidence_file['type'], $allowed_types)) {
            throw new Exception('Invalid file type.');
        }
        if ($evidence_file['size'] > $max_size) {
            throw new Exception('File size exceeds 5MB.');
        }

        $uploadDirectory = '../drop_evidence/';
if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
}
$uniqueFilename = uniqid() . '-' . basename($evidence_file['name']);
$evidencePath = $uploadDirectory . $uniqueFilename;
if (!move_uploaded_file($evidence_file['tmp_name'], $evidencePath)) {
    throw new Exception('Failed to move evidence file.');
}

        $conn->beginTransaction();

        $deleteStmt = $conn->prepare("DELETE FROM intern_deployments WHERE intern_id = ?");
        $deleteStmt->execute([$intern_id]);

        if ($deleteStmt->rowCount() > 0) {
            file_put_contents(__DIR__ . '/drop_debug.log', "Intern ID $intern_id deleted from intern_deployments\n", FILE_APPEND);
        } else {
            file_put_contents(__DIR__ . '/drop_debug.log', "Failed to delete Intern ID $intern_id from intern_deployments\n", FILE_APPEND);
        }

        $updateStmt = $conn->prepare("UPDATE students_data SET ojt_status = 'Dropped', drop_reason = ?, drop_evidence = ? WHERE student_ID = ?");
        $updateStmt->execute([$drop_reason, $evidencePath, $intern_id]);

        $conn->commit();

        $response['status'] = 'success';
        $response['message'] = 'Intern has been dropped with evidence.';
    } catch (Exception $e) {
        $conn->rollBack();
        $response['status'] = 'error';
        $response['message'] = 'Failed to drop intern: ' . $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
