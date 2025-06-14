<?php
include '../connection/config.php';

if (isset($_GET['intern_id'])) {
    $intern_id = $_GET['intern_id'];

    $query = "SELECT department, assigned_supervisor FROM intern_deployments WHERE intern_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$intern_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode(['status' => 'success', 'department' => $data['department'], 'supervisor' => $data['assigned_supervisor']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No deployment found.']);
    }
}
?>
