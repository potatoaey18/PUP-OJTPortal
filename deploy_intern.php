<?php
include '../connection/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_entry = date('Y-m-d H:i:s') . "\n";
    $log_entry .= "Incoming POST: " . print_r($_POST, true) . "\n";
    $log_entry .= "Session Supervisor ID: " . (isset($_SESSION['auth_user']['supervisor_id']) ? $_SESSION['auth_user']['supervisor_id'] : 'N/A') . "\n";
    file_put_contents(__DIR__ . '/deploy_debug.log', $log_entry, FILE_APPEND);

    $response = array();

    try {
        $intern_id = $_POST['intern_id'];
        $department = $_POST['department'];
        $supervisor = $_POST['supervisor'];
        $hours = isset($_POST['hours']) ? $_POST['hours'] : null;
        $mode = isset($_POST['mode']) ? $_POST['mode'] : 'new';
        $deployed_by = $_SESSION['auth_user']['supervisor_id'];
        $deployment_date = date('Y-m-d H:i:s');

        $conn->beginTransaction();

        file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | After input extraction: intern_id=$intern_id, dept=$department, supervisor=$supervisor, mode=$mode\n", FILE_APPEND);

        $checkQuery = "SELECT COUNT(*) FROM students_data WHERE student_ID = ?";
        file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | Executing: $checkQuery with intern_id=$intern_id\n", FILE_APPEND);
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$intern_id]);
        if (!$checkStmt->fetchColumn()) {
            file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | ERROR: Intern not found for intern_id=$intern_id\n", FILE_APPEND);
            throw new Exception('Intern not found.');
        }

        if ($mode === 'new') {
            $deploymentCheck = "SELECT COUNT(*) FROM intern_deployments WHERE intern_id = ?";
            $deploymentStmt = $conn->prepare($deploymentCheck);
            $deploymentStmt->execute([$intern_id]);
            if ($deploymentStmt->fetchColumn()) {
                throw new Exception('This intern has already been deployed.EDITABLE');
            }

            $insertQuery = "INSERT INTO intern_deployments (intern_id, department, assigned_supervisor, required_hours, deployed_by, deployment_date)
                            VALUES (?, ?, ?, ?, ?, ?)";
            file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | Executing: $insertQuery with [$intern_id, $department, $supervisor, $hours, $deployed_by, $deployment_date]\n", FILE_APPEND);
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->execute([$intern_id, $department, $supervisor, $hours, $deployed_by, $deployment_date]);
        } else {
            $updateQuery = "UPDATE intern_deployments 
                            SET department = ?, assigned_supervisor = ?, required_hours = ?, deployed_by = ?, deployment_date = ?
                            WHERE intern_id = ?";
            file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | Executing: $updateQuery with [$department, $supervisor, $hours, $deployed_by, $deployment_date, $intern_id]\n", FILE_APPEND);
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([$department, $supervisor, $hours, $deployed_by, $deployment_date, $intern_id]);
        }

        $statusUpdate = "UPDATE students_data SET ojt_status = 'Deployed' WHERE student_ID = ?";
        file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | Executing: $statusUpdate with intern_id=$intern_id\n", FILE_APPEND);
        $statusStmt = $conn->prepare($statusUpdate);
        $statusStmt->execute([$intern_id]);

        $conn->commit();
        file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | Transaction committed successfully for intern_id=$intern_id\n", FILE_APPEND);
        $response['status'] = 'success';
        $response['message'] = ($mode === 'edit') ? 'Deployment info updated.' : 'Intern successfully deployed.';
    } catch (Exception $e) {
        $conn->rollBack();
        file_put_contents(__DIR__ . '/deploy_debug.log', date('Y-m-d H:i:s') . " | ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        $response['status'] = 'error';
        $response['message'] = 'Error deploying intern: ' . $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
