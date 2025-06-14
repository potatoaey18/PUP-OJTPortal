<?php
session_start();
include '../connection/config.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['auth_user']['student_id']) || $_SESSION['auth_user']['student_id'] == 0) {
    $_SESSION['status'] = "Unauthorized access. Please log in.";
    header("Location: ../pending/login.php");
    exit;
}

if (isset($_POST['time_type'])) {
    $studID = $_SESSION['auth_user']['student_id'];
    $timeType = $_POST['time_type'];
    $imageData = $_POST['image'] ?? '';

    // Validate time type
    $validTimeTypes = ['AM_time_IN', 'AM_time_OUT', 'PM_time_IN', 'PM_time_OUT'];
    if (!in_array($timeType, $validTimeTypes)) {
        $_SESSION['status'] = "Invalid time type selected.";
        header("Location: DTR.php");
        exit;
    }

    // Get current date and time from server
    date_default_timezone_set('Asia/Manila');
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    // Handle image upload
    $imagePath = null;
    if (!empty($imageData)) {
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageData = base64_decode($imageData);
        $imageName = 'dtr_' . $studID . '_' . $timeType . '_' . time() . '.jpg';
        $uploadDir = 'uploads/';
        $imagePath = $uploadDir . $imageName;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!file_put_contents($imagePath, $imageData)) {
            $_SESSION['status'] = "Failed to save image.";
            header("Location: DTR.php");
            exit;
        }
    }

    try {
        // Check if record exists for the current date
        $stmt = $conn->prepare("SELECT id, AM_time_IN, AM_time_OUT, PM_time_IN, PM_time_OUT FROM stud_daily_time_records WHERE stud_id = ? AND recordDate = ?");
        $stmt->execute([$studID, $currentDate]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            // Check if the specific time type is already set
            if ($record[$timeType] && $record[$timeType] != '00:00:00') {
                $_SESSION['status'] = ucfirst(str_replace('_', ' ', $timeType)) . " already recorded for this date.";
                header("Location: DTR.php");
                exit;
            }

            // Update existing record
            $updateFields = [];
            $updateValues = [];
            $imageField = $timeType . '_pic';

            $updateFields[] = "$timeType = ?";
            $updateValues[] = $currentTime;

            if ($imagePath) {
                $updateFields[] = "$imageField = ?";
                $updateValues[] = $imagePath;
            }

            // Calculate total working hours
            $amHours = 0;
            $pmHours = 0;

            $amIn = $timeType == 'AM_time_IN' ? $currentTime : $record['AM_time_IN'];
            $amOut = $timeType == 'AM_time_OUT' ? $currentTime : $record['AM_time_OUT'];
            $pmIn = $timeType == 'PM_time_IN' ? $currentTime : $record['PM_time_IN'];
            $pmOut = $timeType == 'PM_time_OUT' ? $currentTime : $record['PM_time_OUT'];

            if ($amIn && $amOut && $amIn != '00:00:00' && $amOut != '00:00:00') {
                $amInTime = new DateTime($amIn);
                $amOutTime = new DateTime($amOut);
                if ($amOutTime > $amInTime) {
                    $amInterval = $amInTime->diff($amOutTime);
                    $amHours = $amInterval->h + ($amInterval->i / 60);
                }
            }

            if ($pmIn && $pmOut && $pmIn != '00:00:00' && $pmOut != '00:00:00') {
                $pmInTime = new DateTime($pmIn);
                $pmOutTime = new DateTime($pmOut);
                if ($pmOutTime > $pmInTime) {
                    $pmInterval = $pmInTime->diff($pmOutTime);
                    $pmHours = $pmInterval->h + ($pmInterval->i / 60);
                }
            }

            $totalHours = $amHours + $pmHours;
            $updateFields[] = "total_working_hours = ?";
            $updateValues[] = $totalHours;

            $updateValues[] = $record['id'];
            $query = "UPDATE stud_daily_time_records SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute($updateValues);

            $_SESSION['status'] = ucfirst(str_replace('_', ' ', $timeType)) . " recorded successfully.";
        } else {
            // Insert new record
            $imageField = $timeType . '_pic';
            $query = "INSERT INTO stud_daily_time_records (
                stud_id, recordDate, $timeType, total_working_hours, recordStatus, $imageField
            ) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $studID,
                $currentDate,
                $currentTime,
                0, // Initial total hours
                'Pending',
                $imagePath
            ]);

            $_SESSION['status'] = ucfirst(str_replace('_', ' ', $timeType)) . " recorded successfully.";
        }
    } catch (PDOException $e) {
        $_SESSION['status'] = "Error: " . $e->getMessage();
    }
} else {
    $_SESSION['status'] = "Invalid request.";
}

header("Location: DTR.php");
exit;
?>