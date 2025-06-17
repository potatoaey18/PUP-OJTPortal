<?php
include '../connection/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/logs/php_errors.log');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF Token validation failed");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Invalid CSRF token.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Retrieve form data
    $uniqueID = bin2hex(random_bytes(8)) . uniqid();
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $id_number = trim($_POST['id_number'] ?? null);
    $address = trim($_POST['address'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $custom_gender = trim($_POST['custom_gender'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $stud_course = trim($_POST['stud_course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $section = trim($_POST['section'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $guardian_name = trim($_POST['guardian_name'] ?? '');
    $guardian_phone = trim($_POST['guardian_phone'] ?? '');
    $access_level = 0;
    $verify_status = 'Not Verified';
    $online_offlineStatus = 'Offline';
    $verification_code = rand(100000, 999999);
    $profile_picture = '';
    $ojt_status = 'Pending';
    $stud_hte = '';
    $total_rendered_hours = 0;
    $medical_condition = '';
    $is_working_student = 'no';
    $verification_status = 'pending';
    $required_hours = 0;
    $company = null;
    $supervisor_id = null;

    // Map form role to database role
    $role_map = [
        'admin' => 'Administrator',
        'coordinator' => 'Adviser',
        'supervisor' => 'Supervisor',
        'student' => 'Student'
    ];
    $role = $role_map[$role] ?? 'Student';

    // Use custom gender if "Other" is selected
    if ($gender === 'Other' && !empty($custom_gender)) {
        $gender = $custom_gender;
    }

    // Validate required fields
    $required_fields = [
        'first_name' => $first_name,
        'middle_name' => $middle_name,
        'last_name' => $last_name,
        'address' => $address,
        'age' => $age,
        'gender' => $gender,
        'email' => $email,
        'password' => $password,
        'confirm_password' => $confirm_password,
        'role' => $role,
        'phone_number' => $phone_number
    ];

    if ($role === 'Student') {
        $required_fields['stud_course'] = $stud_course;
        $required_fields['year_level'] = $year_level;
        $required_fields['section'] = $section;
        $required_fields['department'] = $department;
        $required_fields['guardian_name'] = $guardian_name;
        $required_fields['guardian_phone'] = $guardian_phone;
    }

    foreach ($required_fields as $field => $value) {
        if (empty($value)) {
            error_log("Validation failed: Empty field '$field'");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "The '$field' field is required.";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Validation failed: Invalid email=$email");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Invalid email format.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate password match
    if ($password !== $confirm_password) {
        error_log("Validation failed: Passwords do not match");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Passwords do not match.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate age
    if (!is_numeric($age) || $age < 18 || floor($age) != $age) {
        error_log("Validation failed: Invalid age=$age");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Invalid age. Must be at least 18 and a whole number.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate gender for "Other"
    if ($gender === 'Other' && empty($custom_gender)) {
        error_log("Validation failed: Custom gender required");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Please specify your gender for 'Other'.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate phone number for all roles
    if (!preg_match('/^\d{10,12}$/', $phone_number)) {
        error_log("Validation failed: Invalid phone number=$phone_number");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Your phone number must be 10-12 digits.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate guardian phone number for students
    if ($role === 'Student' && !preg_match('/^\d{10,12}$/', $guardian_phone)) {
        error_log("Validation failed: Invalid guardian phone=$guardian_phone");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Guardian's phone number must be 10-12 digits.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Check email uniqueness in students_data
    $stmt = $conn->prepare("SELECT id FROM students_data WHERE stud_email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        error_log("Validation failed: Email already exists in students_data=$email");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Email already exists.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Check email uniqueness in pending_users for non-student roles
    if ($role !== 'Student') {
        $stmt = $conn->prepare("SELECT id FROM pending_users WHERE stud_email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            error_log("Validation failed: Email already exists in pending_users=$email");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Email already exists.";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }
    }

    // Check uniqueID and student_ID uniqueness
    $stmt = $conn->prepare("SELECT id FROM students_data WHERE uniqueID = ?");
    $stmt->execute([$uniqueID]);
    if ($stmt->fetch()) {
        error_log("Validation failed: UniqueID conflict=$uniqueID");
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Unique ID conflict. Please try again.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    if ($role === 'Student') {
        $stmt = $conn->prepare("SELECT id FROM students_data WHERE student_ID = ?");
        $stmt->execute([$id_number]);
        if ($stmt->fetch()) {
            error_log("Validation failed: Student ID conflict=$id_number");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Student ID already exists.";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }
    }

    // Handle SIS document upload for students
    $sis_document = null;
    if ($role === 'Student') {
        if (!isset($_FILES['sis_document']) || $_FILES['sis_document']['error'] === UPLOAD_ERR_NO_FILE) {
            error_log("Validation failed: SIS document required for Student");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "SIS document is required for students.";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }

        $uploadDirectory = dirname(__DIR__) . '/pending/student_file_documents/';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        $file = $_FILES['sis_document'];
        $sis_filename = $uniqueID . '_' . basename($file['name']);
        $sis_document = $uploadDirectory . $sis_filename;

        if ($file['type'] !== 'application/pdf') {
            error_log("Validation failed: SIS document not PDF");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "SIS document must be a PDF file.";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }

        if ($file['size'] > 5000000) {
            error_log("Validation failed: SIS document too large");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "SIS document is too large (max 5MB).";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }

        if (!move_uploaded_file($file['tmp_name'], $sis_document)) {
            error_log("Validation failed: SIS document upload failed");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Failed to upload SIS document.";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }
    }

    // Hash password with MD5 (insecure)
    $password_hash = md5($password); // Note: MD5 is insecure; consider password_hash() for production
    error_log("Password hashed (MD5) for email=$email: $password_hash");

    // Start transaction
    $conn->beginTransaction();
    try {
        if ($role === 'Student') {
            error_log("Inserting student: email=$email, uniqueID=$uniqueID");
            $sql = $conn->prepare("
                INSERT INTO students_data (
                    uniqueID, first_name, middle_name, last_name, student_ID, stud_dept, stud_course, stud_section,
                    complete_address, stud_gender, phone_number, stud_email, stud_password, guardians_name,
                    guardians_cpNumber, verification_code, year_level, sis_document, access_level, age
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $sql->execute([
                $uniqueID, $first_name, $middle_name, $last_name, $id_number, $department, $stud_course, $section,
                $address, $gender, $phone_number, $email, $password_hash, $guardian_name, $guardian_phone,
                $verification_code, $year_level, $sis_document, $access_level, $age
            ]);
            error_log("Student inserted: user_id=" . $conn->lastInsertId());
        } else {
            // Insert into pending_users for non-student roles
            error_log("Inserting non-student: email=$email, role=$role, uniqueID=$uniqueID");
            $sql = $conn->prepare("
                INSERT INTO pending_users (
                    uniqueID, first_name, middle_name, last_name, id_number, address, age, gender, stud_email, role,
                    phone_number, sis_document, access_level, verification_code, verify_status, password, online_offlineStatus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $sql->execute([
                $uniqueID, $first_name, $middle_name, $last_name, $id_number, $address, $age, $gender, $email, $role,
                $phone_number, $sis_document, $access_level, $verification_code, $verify_status, $password_hash, $online_offlineStatus
            ]);
            error_log("Non-student inserted: user_id=" . $conn->lastInsertId());
        }

        $user_id = $conn->lastInsertId();

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'itechojtportal@gmail.com';
            $mail->Password = 'iidkdsgmmvthssov';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('itechojtportal@gmail.com', 'ITECH OJT Portal');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Account';
            $mail->Body = 'Your account verification code is <h1>' . $verification_code . '</h1>';

            error_log("Sending email to: $email");
            $mail->send();
            error_log("Email sent successfully to: $email");

            $conn->commit();
            error_log("Registration success: UserID=$user_id, Email=$email, Role=$role");
            $_SESSION['alert'] = "Success";
            $_SESSION['status'] = "Registration successful. Please check your email for the verification code.";
            $_SESSION['status-code'] = "success";
            // Redirect based on role
            if ($role === 'Student') {
                header("Location: ../student/student_verify_account.php?id=$user_id&role=$role");
            } else {
                header("Location: ../pending/pending_verify_code.php?id=$user_id&role=$role");
            }
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("PHPMailer error: " . $mail->ErrorInfo . ", Email=$email");
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Failed to send verification email: " . $mail->ErrorInfo;
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("SQL Error: " . $e->getMessage() . ", Query: " . $sql->queryString);
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Failed to register: " . $e->getMessage();
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }
}

error_log("Invalid request: Method=" . ($_SERVER['REQUEST_METHOD'] ?? 'unknown'));
$_SESSION['alert'] = "Error";
$_SESSION['status'] = "Invalid request method.";
$_SESSION['status-code'] = "error";
header("Location: ../pending/signup.php");
exit();
?>