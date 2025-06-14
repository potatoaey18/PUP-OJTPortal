<?php
include '../connection/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $intern_id = $_POST['intern_id'] ?? '';
    $status = $_POST['status'] ?? '';

  
    if (empty($intern_id) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    try {
     
        $query = "UPDATE students_data SET ojt_status = ? WHERE student_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$status, $intern_id]);

      
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
       
            $check = $conn->prepare("SELECT 1 FROM students_data WHERE student_ID = ? AND ojt_status = ?");
            $check->execute([$intern_id, $status]);
            if ($check->fetch()) {
                echo json_encode(['success' => true, 'message' => 'Status was already set']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No rows affected']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
