<?php
include '../connection/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['auth_user']['supervisor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$supervisor_id = $_SESSION['auth_user']['supervisor_id'];

$stmt = $conn->prepare("SELECT company_name FROM supervisor WHERE id = ?");
$stmt->execute([$supervisor_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company || empty($company['company_name'])) {
    echo json_encode(['success' => false, 'message' => 'Company not found']);
    exit;
}

try {

    $updateStmt = $conn->prepare("UPDATE company_moa SET renewal_status = 'completed' WHERE company_name = ?");
    $updateStmt->execute([$company['company_name']]);
    
    if ($updateStmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'MOA status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No records updated']);
    }
} catch (PDOException $e) {
    error_log("Error updating MOA status: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
