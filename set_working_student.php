<?php
include '../connection/config.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_ID']) && isset($_POST['is_working_student'])) {
    $student_id = $_POST['student_ID'];
    $is_working_student = $_POST['is_working_student'];

    error_log("Received student_ID: " . $student_id);

    // Validate input
    if ($is_working_student !== 'yes') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status value']);
        exit;
    }

    try {
        // Check if student exists
        $check_stmt = $conn->prepare("SELECT student_ID FROM students_data WHERE student_ID = ?");
        $check_stmt->execute([$student_id]);
        $student_exists = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student_exists) {
            error_log("No student found for student_ID: " . $student_id);
            echo json_encode(['status' => 'error', 'message' => 'No student found with the provided ID']);
            exit;
        }

        // Update working student status
        $stmt = $conn->prepare("UPDATE students_data SET is_working_student = ? WHERE student_ID = ?");
        $stmt->execute([$is_working_student, $student_id]);

        if ($stmt->rowCount() > 0) {
            error_log("Successfully updated student_ID: " . $student_id);
            echo json_encode(['status' => 'success', 'message' => 'Working student status updated successfully']);
        } else {
            error_log("Update failed for student_ID: " . $student_id);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
        }
    } catch (PDOException $e) {
        error_log("Database error for student_ID: " . $student_id . " - " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    error_log("Invalid request: " . json_encode($_POST));
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>