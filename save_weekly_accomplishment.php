<?php
ob_start();
session_start();
include '../connection/config.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log("Session ID: " . session_id());
error_log("Session auth_user: " . print_r($_SESSION['auth_user'] ?? [], true));

// Check if user is logged in and student_id is valid
if (!isset($_SESSION['auth_user']['student_id']) || !is_numeric($_SESSION['auth_user']['student_id']) || $_SESSION['auth_user']['student_id'] <= 0) {
    error_log("Session missing student_id or invalid: " . print_r($_SESSION['auth_user'] ?? [], true));
    $_SESSION['status'] = "Unauthorized access. Please log in.";
    $_SESSION['status-code'] = "error";
    header("Location: ../pending/login.php");
    exit;
}

$studID = $_SESSION['auth_user']['student_id'];
// Validate stud_id exists in students_data
try {
    $stmt_check = $conn->prepare("SELECT id FROM students_data WHERE id = ?");
    $stmt_check->execute([$studID]);
    if ($stmt_check->rowCount() == 0) {
        error_log("Invalid stud_id: $studID not found in students_data");
        $_SESSION['status'] = "Invalid student account.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/login.php");
        exit;
    }
    error_log("Validated student_id: $studID");
} catch (PDOException $e) {
    error_log("Database error validating student_id: " . $e->getMessage());
    $_SESSION['status'] = "Error validating account: " . htmlspecialchars($e->getMessage());
    $_SESSION['status-code'] = "error";
    header("Location: ../pending/login.php");
    exit;
}

if (isset($_POST['save'])) {
    error_log("POST data: " . print_r($_POST, true));

    // Sanitize and validate form inputs
    $week_number = filter_input(INPUT_POST, 'week_number', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 20]]);
    $selected_date = filter_input(INPUT_POST, 'selected_date', FILTER_SANITIZE_STRING);
    $selected_time = filter_input(INPUT_POST, 'selected_time', FILTER_SANITIZE_STRING);
    $accomplishment = filter_input(INPUT_POST, 'accomplishment', FILTER_SANITIZE_STRING);
    $coworkers = filter_input(INPUT_POST, 'coworkers', FILTER_SANITIZE_STRING);
    $working_hours = filter_input(INPUT_POST, 'working_hours', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    error_log("Validated inputs - week_number: " . ($week_number === false ? 'invalid' : $week_number) . 
              ", selected_date: " . ($selected_date ?: 'empty') . 
              ", selected_time: " . ($selected_time ?: 'empty') . 
              ", accomplishment: " . ($accomplishment ?: 'empty') . 
              ", coworkers: " . ($coworkers ?: 'empty') . 
              ", working_hours: " . ($working_hours === false ? 'invalid' : $working_hours));

    if ($week_number === false || empty($selected_date) || empty($selected_time) || empty($accomplishment) || empty($coworkers) || $working_hours === false) {
        error_log("Validation failed");
        $_SESSION['status'] = "All fields are required and must be valid!";
        $_SESSION['status-code'] = "error";
    } else {
        try {
            // Check if a record with the same stud_id and week_number already exists
            $stmt_check = $conn->prepare("SELECT id FROM weekly_accomplishment WHERE stud_id = ? AND week_number = ?");
            $stmt_check->execute([$studID, $week_number]);
            if ($stmt_check->rowCount() > 0) {
                error_log("Duplicate week_number: $week_number for stud_id: $studID");
                $_SESSION['status'] = "A report for week $week_number already exists!";
                $_SESSION['status-code'] = "error";
            } else {
                // Insert new record
                error_log("Attempting to insert record for stud_id: $studID, week_number: $week_number");
                $stmt = $conn->prepare("
                    INSERT INTO weekly_accomplishment (
                        stud_id, week_number, date, time, accomplishment, coworkers, working_hours
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $result = $stmt->execute([
                    $studID,
                    $week_number,
                    $selected_date,
                    $selected_time,
                    $accomplishment,
                    $coworkers,
                    $working_hours
                ]);
                error_log("Insert result: " . ($result ? 'success' : 'failed'));
                if ($result) {
                    $_SESSION['status'] = "Weekly accomplishment saved successfully!";
                    $_SESSION['status-code'] = "success";
                } else {
                    error_log("Insert failed without throwing PDOException");
                    $_SESSION['status'] = "Failed to save accomplishment. Please try again.";
                    $_SESSION['status-code'] = "error";
                }
            }
        } catch (PDOException $e) {
            error_log("Database error saving accomplishment: " . $e->getMessage());
            $_SESSION['status'] = "Error saving accomplishment: " . htmlspecialchars($e->getMessage());
            $_SESSION['status-code'] = "error";
        }
    }
} else {
    error_log("Invalid form submission: save not set");
    $_SESSION['status'] = "Invalid form submission.";
    $_SESSION['status-code'] = "error";
}

header("Location: weekly_accomplishment.php");
exit;
ob_end_flush();
?>