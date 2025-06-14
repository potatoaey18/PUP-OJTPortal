<?php
include '../connection/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array();
    
    try {

        $intern_id = $_POST['intern_id'];
        $status = $_POST['status'];
        $updated_by = $_SESSION['auth_user']['supervisor_id'];
        $update_date = date('Y-m-d H:i:s');

        $updateQuery = "UPDATE students_data SET status = ?, updated_by = ?, update_date = ? WHERE student_ID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$status, $updated_by, $update_date, $intern_id]);

        $response['status'] = 'success';
        $response['message'] = 'Status updated successfully';
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = 'Error updating status: ' . $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
