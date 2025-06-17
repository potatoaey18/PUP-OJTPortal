<?php
include '../connection/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($conn) || is_null($conn)) {
    $_SESSION['alert'] = "Error";
    $_SESSION['status'] = "Database connection not established.";
    $_SESSION['status-code'] = "error";
    error_log("Database connection not established in stud_registerCode.php");
    header("Location: ../pending/signup.php");
    exit();
}

if (isset($_POST['register'])) {
    // Retrieve form data
    $first_name = trim($_POST['f_name'] ?? '');
    $middle_name = trim($_POST['m_name'] ?? '');
    $last_name = trim($_POST['l_name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $student_course = trim($_POST['student_course'] ?? '');
    $student_section = trim($_POST['student_section'] ?? '');
    $year_lvl = trim($_POST['year_lvl'] ?? '');
    $stud_dept = trim($_POST['stud_dept'] ?? '');
    $complete_address = trim($_POST['complete_address'] ?? '');
    $stud_gender = trim($_POST['stud_gender'] ?? '');
    $custom_gender = trim($_POST['custom_gender'] ?? '');
    $age = filter_var($_POST['age'] ?? 0, FILTER_VALIDATE_INT);
    $phone_number = trim($_POST['phone_number'] ?? '');
    $stud_email = trim($_POST['stud_email'] ?? '');
    $pword = $_POST['pword'] ?? '';
    $c_pword = $_POST['cpword'] ?? '';
    $guardians_name = trim($_POST['guardians_name'] ?? '');
    $guardians_cpNumber = trim($_POST['guardians_cpNumber'] ?? '');
    $verification_code = rand(100000, 999999);
    $uniqueId = uniqid() . mt_rand(1000, 9999);

    // Handle custom gender
    $final_gender = $stud_gender === 'Other' ? $custom_gender : $stud_gender;

    // Validate required fields
    $required_fields = [
        'f_name' => $first_name,
        'l_name' => $last_name,
        'student_id' => $student_id,
        'student_course' => $student_course,
        'student_section' => $student_section,
        'year_lvl' => $year_lvl,
        'stud_dept' => $stud_dept,
        'complete_address' => $complete_address,
        'stud_gender' => $final_gender,
        'phone_number' => $phone_number,
        'stud_email' => $stud_email,
        'pword' => $pword,
        'cpword' => $c_pword,
        'guardians_name' => $guardians_name,
        'guardians_cpNumber' => $guardians_cpNumber
    ];

    foreach ($required_fields as $field => $value) {
        if (empty($value)) {
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "All fields are required.";
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }
    }

    // Validate age
    if ($age < 18) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Age must be at least 18.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate email format
    if (!filter_var($stud_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Invalid email format.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10,12}$/', $phone_number)) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Invalid phone number format.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate guardian's phone number
    if (!preg_match('/^[0-9]{10,12}$/', $guardians_cpNumber)) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Invalid guardian's phone number format.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate SIS document
    if (!isset($_FILES['sis_document']) || $_FILES['sis_document']['error'] == UPLOAD_ERR_NO_FILE) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "SIS document is required.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM students_data WHERE stud_email = ?");
    $stmt->execute([$stud_email]);
    if ($stmt->fetch()) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Email already exists.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Check if phone number already exists
    $stmt = $conn->prepare("SELECT * FROM students_data WHERE phone_number = ?");
    $stmt->execute([$phone_number]);
    if ($stmt->fetch()) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Phone number already exists.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Validate password match
    if ($pword !== $c_pword) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Passwords do not match.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Hash password using MD5 (as requested)
    $hashed_password = md5($pword);

    // Handle SIS document upload
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
        header("Location: ../pending/signup.php");
        exit();
    }

    if (!move_uploaded_file($sis_document['tmp_name'], $sis_filepath)) {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Failed to upload SIS document.";
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }

    // Insert data into database
    $sql = $conn->prepare("
        INSERT INTO students_data (
            uniqueID, first_name, middle_name, last_name, student_ID, stud_course, 
            stud_section, year_level, stud_department, complete_address, stud_gender, 
            age, phone_number, stud_email, stud_password, guardians_name, guardians_cpNumber, 
            sis_document, verification_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    try {
        $sql->execute([
            $uniqueId, $first_name, $middle_name, $last_name, $student_id, $student_course,
            $student_section, $year_lvl, $stud_dept, $complete_address, $final_gender,
            $age, $phone_number, $stud_email, $hashed_password, $guardians_name, $guardians_cpNumber,
            $sis_filepath, $verification_code
        ]);

        // Get the inserted student's ID
        $student_id = $conn->lastInsertId();

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
            $mail->addAddress($stud_email);
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Account';
            $mail->Body = 'Your account verification code is <h1>' . $verification_code . '</h1>';

            $mail->send();

            $_SESSION['alert'] = "Success";
            $_SESSION['status'] = "Student registered successfully. Please check your email for the verification code.";
            $_SESSION['status-code'] = "success";
            header("Location: ../student/student_verify_account.php?id=" . $student_id);
            exit();
        } catch (Exception $e) {
            error_log("PHPMailer error: " . $mail->ErrorInfo);
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Failed to send verification email: " . $mail->ErrorInfo;
            $_SESSION['status-code'] = "error";
            header("Location: ../pending/signup.php");
            exit();
        }
    } catch (Exception $e) {
        error_log("Database insert error: " . $e->getMessage());
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Failed to register student: " . $e->getMessage();
        $_SESSION['status-code'] = "error";
        header("Location: ../pending/signup.php");
        exit();
    }
} else {
    $_SESSION['alert'] = "Error";
    $_SESSION['status'] = "Invalid request.";
    $_SESSION['status-code'] = "error";
    header("Location: ../pending/signup.php");
    exit();
}
?>