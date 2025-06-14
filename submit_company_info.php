<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id'])) {
    echo "<script>window.location.href='student_login.php'</script>";
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name'] ?? '');
    $nature_of_business = trim($_POST['nature_of_business'] ?? '');
    $company_address = trim($_POST['company_address'] ?? '');

    // Validation
    if (empty($company_name)) {
        $errors[] = "Company name is required.";
    }
    if (empty($nature_of_business)) {
        $errors[] = "Nature of business is required.";
    }
    if (empty($company_address)) {
        $errors[] = "Company address is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO new_moa_processing (
                    student_id, 
                    company_name, 
                    nature_of_business, 
                    company_address, 
                    status, 
                    request_date, 
                    created_at, 
                    updated_at
                ) VALUES (
                    :student_id, 
                    :company_name, 
                    :nature_of_business, 
                    :company_address, 
                    'checking_info', 
                    CURRENT_TIMESTAMP, 
                    CURRENT_TIMESTAMP, 
                    CURRENT_TIMESTAMP
                )
            ");
            $stmt->execute([
                'student_id' => $_SESSION['auth_user']['student_id'],
                'company_name' => $company_name,
                'nature_of_business' => $nature_of_business,
                'company_address' => $company_address
            ]);
            $success = true;
        } catch (PDOException $e) {
            error_log("Insert error: " . $e->getMessage());
            $errors[] = "Failed to submit company information: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Submit Company Information</title>
    <link rel="shortcut icon" href="images/pupLogo.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 6rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #700000;
            font-weight: 600;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .submit-btn {
            background-color: #8B0000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .submit-btn:hover {
            background-color: #700000;
        }
        .error, .success {
            margin-bottom: 1rem;
            padding: 10px;
            border-radius: 4px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <?php require_once 'templates/student_navbar.php'; ?>

    <div class="content-wrap" style="width: 100%; margin: 0 auto;">
        <div class="form-container">
            <div class="page-header">
                <div class="page-title"><br>
                    <h1>Submit Company Information</h1><br>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="success">Company information submitted successfully!</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="submit_company_info.php">
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>" aria-label="Company Name">
                </div>
                <div class="form-group">
                    <label for="nature_of_business">Nature of Business</label>
                    <input type="text" id="nature_of_business" name="nature_of_business" value="<?php echo isset($_POST['nature_of_business']) ? htmlspecialchars($_POST['nature_of_business']) : ''; ?>" aria-label="Nature of Business">
                </div>
                <div class="form-group">
                    <label for="company_address">Company Address</label>
                    <textarea id="company_address" name="company_address" aria-label="Company Address"><?php echo isset($_POST['company_address']) ? htmlspecialchars($_POST['company_address']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="submit-btn" aria-label="Submit company information">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            <?php if ($success): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Company information submitted successfully!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'student_dashboard.php';
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>