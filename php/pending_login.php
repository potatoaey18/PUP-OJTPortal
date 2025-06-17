<?php
include '../connection/config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 in production

if (isset($_POST['LogIn'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check all role-specific tables
    $stmt = $conn->prepare("
        SELECT 'admin' AS role, id, uniqueID, verify_status, access_level, admin_password AS password FROM admin_account WHERE admin_email = ?
        UNION
        SELECT 'coordinator' AS role, id, uniqueID, verify_status, access_level, coordinators_password AS password FROM coordinators_account WHERE coordinators_email = ?
        UNION
        SELECT 'supervisor' AS role, id, uniqueID, verify_status, access_level, supervisor_password AS password FROM supervisor WHERE supervisor_email = ?
        UNION
        SELECT 'student' AS role, id, uniqueID, verify_status, access_level, stud_password AS password FROM students_data WHERE stud_email = ?
    ");
    $stmt->execute([$email, $email, $email, $email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data && password_verify($password, $data['password'])) {
        $user_id = $data['id'];
        $uniqueId = $data['uniqueID'];
        $role = $data['role'];
        $verify_status = $data['verify_status'];
        $access_level = $data['access_level'];

        if ($verify_status === 'Not Verified') {
            $_SESSION['alert'] = "Account Verification";
            $_SESSION['status'] = "Please verify your email before logging in.";
            $_SESSION['status-code'] = "info";
            header("Location: ../pending/verify_account.php?id=$user_id&role=$role");
            exit();
        }

        // Verified user: proceed with login
        $_SESSION['auth'] = true;
        $_SESSION['auth_user'] = [
            'user_id' => $user_id,
            'uniqueID' => $uniqueId,
            'role' => $role,
        ];

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        if ($access_level == 0) {
            $_SESSION['alert'] = "Pending Approval";
            $_SESSION['status'] = "Your account is awaiting admin approval.";
            $_SESSION['status-code'] = "info";
            header("Location: ../pending/dashboard.php");
            exit();
        } else {
            date_default_timezone_set('Asia/Manila');
            $date = date('F / d l / Y');
            $time = date('g:i A');
            $logs = 'You successfully logged in to your account.';
            $online_offline_status = 'Online';

            $sql = $conn->prepare("INSERT INTO system_notification(user_id, role, logs, logs_date, logs_time) VALUES (?, ?, ?, ?, ?)");
            $sql->execute([$user_id, $role, $logs, $date, $time]);

            // Update online_offlineStatus
            if ($role === 'admin') {
                $sql = $conn->prepare("UPDATE admin_account SET online_offlineStatus = ? WHERE id = ?");
            } elseif ($role === 'coordinator') {
                $sql = $conn->prepare("UPDATE coordinators_account SET online_offlineStatus = ? WHERE id = ?");
            } elseif ($role === 'supervisor') {
                $sql = $conn->prepare("UPDATE supervisor SET online_offlineStatus = ? WHERE id = ?");
            } else {
                $sql = $conn->prepare("UPDATE students_data SET online_offlineStatus = ? WHERE id = ?");
            }
            $sql->execute([$online_offline_status, $user_id]);

            $_SESSION['alert'] = "Success";
            $_SESSION['status'] = "Log In Success";
            $_SESSION['status-code'] = "success";
            header("Location: ../index.php"); // Adjust based on role-specific dashboards
            exit();
        }
    } else {
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Incorrect Log In Details";
        $_SESSION['status-code'] = "info";
        header("Location: pending_login.php");
        exit();
    }
} else {
    $_SESSION['alert'] = "Error";
    $_SESSION['status'] = "Invalid request.";
    $_SESSION['status-code'] = "error";
    header("Location: pending_login.php");
    exit();
}
?>