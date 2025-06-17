<?php
include '../connection/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 in production

session_start();

if (isset($_POST['register'])) {
    // Retrieve form data
    $role = trim($_POST['role']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $id_number = trim($_POST['id_number']);
    $address = trim($_POST['address']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $custom_gender = trim($_POST['custom_gender'] ?? '');
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $verification_code = rand(100000, 999999);
    $uniqueId = uniqid() . mt_rand(1000, 9999);

    // Use custom gender if "Other" is selected
    if ($gender === 'Other' && !empty($custom_gender)) {
        $gender = $custom_gender;
    }

    // Validate required fields
    $required_fields = [
        'role' => $role,
        'first_name' => $first_name,
        'middle_name' => $middle_name,
        'last_name' => $last_name,
        'id_number' => $id_number,
        'address' => $address,
        'age' => $age,
        'gender' => $gender,
        'email' => $email,
        'password' => $password,
        'confirm_password' => $confirm_password
    ];

    foreach ($required_fields as $field => $value) {
        if (empty($value)) {
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "All fields are required.";
            $_SESSION['status-code'] = "error";
            header("Location: ../php/pending_register.php");
            exit();
        }
    }

    // Validate age
    if ($age < 18) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Age must be at least 18.";
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Invalid email format.";
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    }

    // Validate ID number format
    if ($role === 'student' && !preg_match('/^\d{4}-\d{5}$/', $id_number)) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Student ID must be in format YYYY-NNNNN (e.g., 2023-12345).";
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    } elseif ($role !== 'student' && !preg_match('/^[A-Za-z0-9-]+$/', $id_number)) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "ID number contains invalid characters.";
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    }

    // Validate password match
    if ($password !== $confirm_password) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Passwords do not match.";
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    }

    // Validate SIS document for students
    $sis_filepath = null;
    if ($role === 'student') {
        if (!isset($_FILES['sis_document']) || $_FILES['sis_document']['error'] == UPLOAD_ERR_NO_FILE) {
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "SIS document is required for students.";
            $_SESSION['status-code'] = "error";
            header("Location: ../php/pending_register.php");
            exit();
        }

        $uploadDirectory = '../student_file_documents/';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        $sis_document = $_FILES['sis_document'];
        $sis_filename = uniqid() . '-' . basename($sis_document['name']);
        $sis_filepath = $uploadDirectory . $sis_filename;

        if ($sis_document['type'] !== 'application/pdf') {
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "SIS document must be a PDF file.";
            $_SESSION['status-code'] = "error";
            header("Location: ../php/pending_register.php");
            exit();
        }

        if ($sis_document['size'] > 5000000) { // Limit to 5MB
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "SIS document is too large (max 5MB).";
            $_SESSION['status-code'] = "error";
            header("Location: ../php/pending_register.php");
            exit();
        }

        if (!move_uploaded_file($sis_document['tmp_name'], $sis_filepath)) {
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Failed to upload SIS document.";
            $_SESSION['status-code'] = "error";
            header("Location: ../php/pending_register.php");
            exit();
        }
    }

    // Check email uniqueness
    $stmt = $conn->prepare("
        SELECT 1 FROM admin_account WHERE admin_email = ?
        UNION
        SELECT 1 FROM coordinators_account WHERE coordinators_email = ?
        UNION
        SELECT 1 FROM supervisor WHERE supervisor_email = ?
        UNION
        SELECT 1 FROM students_data WHERE stud_email = ?
    ");
    $stmt->execute([$email, $email, $email, $email]);
    if ($stmt->fetch()) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Email already exists.";
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    }

    // Check ID number uniqueness
    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM admin_account WHERE id_number = ?");
        $stmt->execute([$id_number]);
    } elseif ($role === 'coordinator') {
        $stmt = $conn->prepare("SELECT * FROM coordinators_account WHERE faculty_id = ?");
        $stmt->execute([$id_number]);
    } elseif ($role === 'student') {
        $stmt = $conn->prepare("SELECT * FROM students_data WHERE student_ID = ?");
        $stmt->execute([$id_number]);
    } else {
        $stmt = null; // Supervisor doesn't require ID number uniqueness
    }
    if ($stmt && $stmt->fetch()) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "ID number already exists.";
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    }

    // Hash password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data using transaction
    try {
        $conn->beginTransaction();

        // Insert into role-specific table
        if ($role === 'admin') {
            $sql = $conn->prepare("
                INSERT INTO admin_account (
                    uniqueID, first_name, middle_name, last_name, id_number, address, age, gender, 
                    admin_email, admin_password, verification_code, verify_status, online_offlineStatus, access_level
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Verified', 'Offline', 0)
            ");
            $sql->execute([
                $uniqueId, $first_name, $middle_name, $last_name, $id_number, $address, $age, $gender,
                $email, $hashed_password, $verification_code
            ]);
            $role_label = 'Administrator';
        } elseif ($role === 'coordinator') {
            $sql = $conn->prepare("
                INSERT INTO coordinators_account (
                    uniqueID, first_name, middle_name, last_name, faculty_id, complete_address, age, gender, 
                    coordinators_email, coordinators_password, verification_code, verify_status, online_offlineStatus, access_level
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Verified', 'Offline', 0)
            ");
            $sql->execute([
                $uniqueId, $first_name, $middle_name, $last_name, $id_number, $address, $age, $gender,
                $email, $hashed_password, $verification_code
            ]);
            $role_label = 'Faculty/Coordinator';
        } elseif ($role === 'supervisor') {
            $sql = $conn->prepare("
                INSERT INTO supervisor (
                    uniqueID, first_name, middle_name, last_name, company_address, age, gender, 
                    supervisor_email, supervisor_password, verification_code, verify_status, online_offlineStatus, access_level
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Verified', 'Offline', 0)
            ");
            $sql->execute([
                $uniqueId, $first_name, $middle_name, $last_name, $address, $age, $gender,
                $email, $hashed_password, $verification_code
            ]);
            $role_label = 'Supervisor';
        } elseif ($role === 'student') {
            $sql = $conn->prepare("
                INSERT INTO students_data (
                    uniqueID, first_name, middle_name, last_name, student_ID, complete_address, age, stud_gender, 
                    stud_email, stud_password, sis_document, verification_code, verify_status, online_offlineStatus, access_level
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Verified', 'Offline', 0)
            ");
            $sql->execute([
                $uniqueId, $first_name, $middle_name, $last_name, $id_number, $address, $age, $gender,
                $email, $hashed_password, $sis_filepath, $verification_code
            ]);
            $role_label = 'Student';
        } else {
            throw new Exception("Invalid role selected.");
        }

        // Get inserted user's ID
        $user_id = $conn->lastInsertId();

        // Insert into pending_users
        $sql = $conn->prepare("
            INSERT INTO pending_users (
                uniqueID, first_name, middle_name, last_name, id_number, address, age, gender, 
                email, role, sis_document, access_level
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
        ");
        $sql->execute([
            $uniqueId, $first_name, $middle_name, $last_name, $id_number, $address, $age, $gender,
            $email, $role_label, $sis_filepath
        ]);

        $conn->commit();

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST; // Defined in config.php
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME; // Defined in config.php
            $mail->Password = SMTP_PASSWORD; // Defined in config.php
            $mail->SMTPSecure = 'ssl';
            $mail->Port = SMTP_PORT; // Defined in config.php

            $mail->setFrom(SMTP_USERNAME, 'ITECH OJT Portal');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Account';
            $mail->Body = 'Your account verification code is <h1>' . $verification_code . '</h1>';

            $mail->send();

            $_SESSION['alert'] = "Success";
            $_SESSION['status'] = "Registration successful. Please check your email for the verification code.";
            $_SESSION['status-code'] = "success";
            header("Location: ../pending/verify_account.php?id=$user_id&role=$role");
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("PHPMailer error: " . $mail->ErrorInfo);
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Failed to send verification email: " . $mail->ErrorInfo;
            $_SESSION['status-code'] = "error";
            header("Location: ../php/pending_register.php");
            exit();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Database insert error: " . $e->getMessage());
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Failed to register: " . $e->getMessage();
        $_SESSION['status-code'] = "error";
        header("Location: ../php/pending_register.php");
        exit();
    }
} else {
    $_SESSION['alert'] = "Error";
    $_SESSION['status'] = "Invalid request.";
    $_SESSION['status-code'] = "error";
    header("Location: ../php/pending_register.php");
    exit();
}
?>