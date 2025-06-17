<?php
include '../connection/config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log'); // Update to writable path

// Function to handle successful login (reduce code duplication)
function handleSuccessfulLogin($conn, $user_id, $notification_table, $notification_id_column, $table, $dashboard) {
    date_default_timezone_set('Asia/Manila');
    $date = date('F / d / Y');
    $time = date('g:i A');
    $logs = 'You successfully logged in to your account.';
    $online_offline_status = 'Online';

    try {
        $sql = $conn->prepare("INSERT INTO $notification_table ($notification_id_column, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
        $sql->execute([$user_id, $logs, $date, $time]);

        $sql2 = $conn->prepare("UPDATE $table SET online_offlineStatus = ? WHERE id = ?");
        $sql2->execute([$online_offline_status, $user_id]);

        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Log In Success";
        $_SESSION['status-code'] = "success";
        header("Location: $dashboard");
        exit();
    } catch (PDOException $e) {
        error_log("Login success error: Table=$table, UserID=$user_id, Error=" . $e->getMessage());
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Login failed. Please try again.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/login.php");
        exit();
    }
}

if (isset($_POST['LogIn'])) {
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $password_md5 = md5($password); // Using MD5 as per your setup

    // Log attempt (avoid logging sensitive data)
    error_log("Login attempt: Email=$email");

    if (empty($email) || empty($password)) {
        error_log("Missing input: Email=" . ($email ?: 'empty') . ", Password=" . ($password ? 'provided' : 'empty'));
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Email or password cannot be empty";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/login.php");
        exit();
    }

    try {
        // Step 1: Check students_data table
        $stmt = $conn->prepare("SELECT id, uniqueID, stud_password, verify_status, stud_course, access_level FROM students_data WHERE stud_email = ?");
        $stmt->execute([$email]);
        $student_data = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("Students_data query result: Email=$email, Data=" . print_r($student_data, true));

        if ($student_data) {
            $student_id = $student_data['id'];
            $unique_id = $student_data['uniqueID'];
            $stored_password = $student_data['stud_password'];
            $verify_status = $student_data['verify_status'];
            $stud_course = $student_data['stud_course'];
            $access_level = $student_data['access_level'];

            if ($password_md5 !== $stored_password) {
                error_log("Password mismatch: Email=$email, StudentID=$student_id");
                $_SESSION['alert'] = "Oppss...";
                $_SESSION['status'] = "Incorrect Log In Details";
                $_SESSION['status-code'] = "info";
                header("Location: ../pending/login.php");
                exit();
            }

            // Optional: Migrate to bcrypt
            /*
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = $conn->prepare("UPDATE students_data SET stud_password = ? WHERE id = ?");
            $sql->execute([$new_hash, $student_id]);
            error_log("Migrated student password to bcrypt: StudentID=$student_id");
            */

            $_SESSION['auth'] = true;
            $_SESSION['auth_user'] = [
                'student_id' => $student_id,
                'unique_id' => $unique_id,
                'course' => $stud_course,
                'user_type' => 'student',
                'access_level' => $access_level
            ];
            session_regenerate_id(true); // Prevent session fixation

            if ($verify_status === 'Not Verified') {
                error_log("Unverified student or access_level=0: StudentID=$student_id, Email=$email, Access Level=$access_level");
                $_SESSION['alert'] = "Account Verification";
                $_SESSION['status'] = "Verify your Account";
                $_SESSION['status-code'] = "info";
                header("Location: ../student/student_verify_account.php?id=$student_id");
                exit();
            } else {
                handleSuccessfulLogin($conn, $student_id, 'system_notification', 'student_id', 'students_data', '../student/dashboard.php');
            }
        }

        // Step 2: Check pending_users table for access_level = 0
        $stmt = $conn->prepare("SELECT id, uniqueID, password, verify_status, role, first_name, last_name, access_level FROM pending_users WHERE stud_email = ? AND access_level = 0");
        $stmt->execute([$email]);
        $pending_data = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("Pending_users query result: Email=$email, Data=" . print_r($pending_data, true));

        if ($pending_data) {
            $stored_password = $pending_data['password'];
            if ($password_md5 !== $stored_password) {
                error_log("Password mismatch: Email=$email");
                $_SESSION['alert'] = "Oppss...";
                $_SESSION['status'] = "Incorrect Log In Details";
                $_SESSION['status-code'] = "info";
                header("Location: ../pending/login.php");
                exit();
            }

            // Optional: Migrate to bcrypt
            /*
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = $conn->prepare("UPDATE pending_users SET password = ? WHERE id = ?");
            $sql->execute([$new_hash, $pending_data['id']]);
            error_log("Migrated pending user password to bcrypt: UserID=" . $pending_data['id']);
            */

            $user_id = $pending_data['id'];
            $unique_id = $pending_data['uniqueID'];
            $verify_status = $pending_data['verify_status'];
            $role = $pending_data['role'];
            $access_level = $pending_data['access_level'];
            $full_name = $pending_data['first_name'] . ' ' . $pending_data['last_name'];

            error_log("Role detected: $role, Access Level: $access_level, Verify Status: $verify_status");

            $_SESSION['auth'] = true;
            session_regenerate_id(true);

            // Define role-specific settings
            $notification_table = '';
            $notification_id_column = '';
            $dashboard = '../pending/dashboard.php';
            $id_key = '';
            switch (strtolower($role)) {
                case 'administrator':
                case 'admin':
                    $id_key = 'admin_id';
                    $_SESSION['auth_user'] = [
                        'admin_id' => $user_id,
                        'unique_id' => $unique_id,
                        'full_name' => $full_name,
                        'user_type' => 'admin',
                        'access_level' => $access_level
                    ];
                    $notification_table = 'admin_system_notification';
                    $notification_id_column = 'admin_id';
                    break;

                case 'adviser':
                case 'coordinator':
                    $id_key = 'coordinators_id';
                    $_SESSION['auth_user'] = [
                        'coordinators_id' => $user_id,
                        'unique_id' => $unique_id,
                        'full_name' => $full_name,
                        'user_type' => 'coordinator',
                        'access_level' => $access_level
                    ];
                    $notification_table = 'coordinatorsystemnotification';
                    $notification_id_column = 'coordinator_id';
                    break;

                case 'supervisor':
                    $id_key = 'supervisor_id';
                    $_SESSION['auth_user'] = [
                        'supervisor_id' => $user_id,
                        'unique_id' => $unique_id,
                        'full_name' => $full_name,
                        'user_type' => 'supervisor',
                        'access_level' => $access_level
                    ];
                    $notification_table = 'supervisor_system_notification';
                    $notification_id_column = 'supervisor_id';
                    break;

                default:
                    error_log("Unknown role in pending_users: Role=$role, UserID=$user_id, Email=$email");
                    $_SESSION['alert'] = "Error";
                    $_SESSION['status'] = "Unknown role";
                    $_SESSION['status-code'] = "error";
                    header("Location: ../pending/login.php");
                    exit();
            }

            if ($verify_status === 'Not Verified') {
                error_log("Unverified user: Role=$role, {$id_key}=$user_id, Email=$email, Access Level=$access_level");
                $_SESSION['alert'] = "Account Verification";
                $_SESSION['status'] = "Verify your Account";
                $_SESSION['status-code'] = "info";
                header("Location: ../pending/pending_verify_code.php?id=$user_id");
                exit();
            }

            handleSuccessfulLogin($conn, $user_id, $notification_table, $notification_id_column, 'pending_users', $dashboard);
        }

        // Step 3: Check other tables for access_level != 0
        $tables = [
            'admin_account' => [
                'email_column' => 'admin_email',
                'password_column' => 'admin_password',
                'user_type' => 'admin',
                'dashboard' => '../adminportal/dashboard.php',
                'notification_table' => 'admin_system_notification',
                'notification_id_column' => 'admin_id',
                'access_level' => 1,
                'id_key' => 'admin_id'
            ],
            'coordinators_account' => [
                'email_column' => 'coordinators_email',
                'password_column' => 'coordinators_password',
                'user_type' => 'coordinator',
                'dashboard' => '../coordinator/dashboard.php',
                'notification_table' => 'coordinatorsystemnotification',
                'notification_id_column' => 'coordinator_id',
                'access_level' => 2,
                'id_key' => 'coordinators_id'
            ],
            'supervisor' => [
                'email_column' => 'supervisor_email',
                'password_column' => 'supervisor_password',
                'user_type' => 'supervisor',
                'dashboard' => '../supervisor/dashboard.php',
                'notification_table' => 'supervisor_system_notification',
                'notification_id_column' => 'supervisor_id',
                'access_level' => 3,
                'id_key' => 'supervisor_id'
            ],
            'students_data' => [
                'email_column' => 'stud_email',
                'password_column' => 'stud_password',
                'user_type' => 'student',
                'dashboard' => '../student/dashboard.php',
                'notification_table' => 'system_notification',
                'notification_id_column' => 'student_id',
                'access_level' => 4,
                'id_key' => 'student_id'
            ]
        ];

        $user_found = false;
        foreach ($tables as $table => $config) {
            $stmt = $conn->prepare("SELECT id, uniqueID, {$config['password_column']} AS password, verify_status, first_name, last_name, access_level FROM $table WHERE {$config['email_column']} = ? AND access_level = ?");
            $stmt->execute([$email, $config['access_level']]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("$table query result: Email=$email, Data=" . print_r($user_data, true));

            if ($user_data) {
                $stored_password = $user_data['password'];
                if ($password_md5 !== $stored_password) {
                    error_log("Password mismatch in $table: Email=$email");
                    continue; // Try next table
                }

                // Optional: Migrate to bcrypt
                /*
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = $conn->prepare("UPDATE $table SET {$config['password_column']} = ? WHERE id = ?");
                $sql->execute([$new_hash, $user_data['id']]);
                error_log("Migrated $table password to bcrypt: UserID=" . $user_data['id']);
                */

                $user_found = true;
                $user_id = $user_data['id'];
                $unique_id = $user_data['uniqueID'];
                $verify_status = $user_data['verify_status'];
                $access_level = $user_data['access_level'];
                $full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];

                $_SESSION['auth'] = true;
                session_regenerate_id(true);

                $_SESSION['auth_user'] = [
                    $config['id_key'] => $user_id,
                    'unique_id' => $unique_id,
                    'full_name' => $full_name,
                    'user_type' => $config['user_type'],
                    'access_level' => $access_level
                ];

                if ($verify_status === 'Not Verified' || $verify_status === 'pending') {
                    error_log("Unverified user in $table: {$config['id_key']}=$user_id, Email=$email, Access Level=$access_level");
                    $_SESSION['alert'] = "Account Verification";
                    $_SESSION['status'] = "Verify your Account";
                    $_SESSION['status-code'] = "info";
                    header("Location: ../pending/pending_verify_code.php?id=$user_id");
                    exit();
                }

                handleSuccessfulLogin($conn, $user_id, $config['notification_table'], $config['notification_id_column'], $table, $config['dashboard']);
            }
        }

        // No user found in any table
        if (!$user_found) {
            error_log("No user found: Email=$email");
            $_SESSION['alert'] = "Oppss...";
            $_SESSION['status'] = "Incorrect Log In Details";
            $_SESSION['status-code'] = "info";
            header("Location: ../pending/login.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage() . ", Email=$email");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Login failed. Please try again.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/login.php");
        exit();
    }
}

error_log("Invalid request: Method=" . ($_SERVER['REQUEST_METHOD'] ?? 'unknown'));
$_SESSION['alert'] = "Error";
$_SESSION['status'] = "Invalid request.";
$_SESSION['status-code'] = "error";
header("Location: ../pending/login.php");
exit();
?>