<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in
if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    echo "<script>window.location.href='index.php'</script>";
    exit();
}


$supervisorID = $_SESSION['auth_user']['supervisor_id'];
$supervisor = null;

try {
    $stmt = $conn->prepare("SELECT * FROM supervisor WHERE id = ?");
    $stmt->execute([$supervisorID]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supervisor) {
        throw new Exception("Supervisor with ID $supervisorID not found in database");
    }
    
    // Log supervisor found
    error_log('Supervisor found: ID ' . $supervisorID);
    
} catch (Exception $e) {
    $error = handleError('Error fetching supervisor', $e);
    displayError($error);
}

// Debug form submission
error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);
error_log('POST data: ' . print_r($_POST, true));

// Display form data for debugging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<div style="background: #e3f2fd; border-left: 5px solid #2196f3; color: #0d47a1; padding: 15px; margin: 10px 0; font-family: Arial, sans-serif;">';
    echo '<h3 style="margin-top: 0; color: #1565c0;">Form Data Received</h3>';
    echo '<pre style="margin: 0; overflow: auto;">';
    echo htmlspecialchars(print_r($_POST, true));
    echo '</pre>';
    echo '</div>';
}

// Initialize error variable
$error = '';

// Handle form submission
// Enable error reporting and display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/moa_errors.log');

// Function to display errors
function displayError($message, $details = '') {
    echo '<div style="background: #ffebee; border-left: 4px solid #f44336; padding: 10px; margin: 10px 0; color: #b71c1c;">';
    echo '<strong>Error:</strong> ' . htmlspecialchars($message);
    if ($details) {
        echo '<div style="margin-top: 5px; font-family: monospace; font-size: 12px;">';
        echo htmlspecialchars($details);
        echo '</div>';
    }
    echo '</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hte_name'])) {
        try {
            // Debug: Log POST data
            error_log('Form submitted with data: ' . print_r($_POST, true));
            // Get form data
            $company_name = $_POST['hte_name'] ?? '';
            $company_address = $_POST['hte_address'] ?? '';
            $nature_of_business = $_POST['nature_of_business'] ?? '';
            $contact_person_name = $_POST['contact_person'] ?? '';
            $company_position = $_POST['position'] ?? '';
            $email_address = trim($_POST['email_address'] ?? '');
            $start_date_validity = $_POST['start_validity'] ?? '';
            $end_date_validity = $_POST['end_validity'] ?? '';
            $agree_terms = $_POST['agree_terms'] ?? 'no';

            // Validate email format
            if (!filter_var($email_address, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email_address)) {
                throw new Exception('Please enter a valid email address (e.g., example@gmail.com)');
            }

            // Verify terms were agreed to
            if ($agree_terms !== 'yes') {
                throw new Exception('You must agree to the terms and conditions to submit the form.');
            }

            // Verify supervisor exists
            $stmt = $conn->prepare("SELECT id FROM supervisor WHERE id = ?");
            $stmt->execute([$supervisorID]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Supervisor not found in database.');
            }

            // Begin transaction
            if (!$conn->beginTransaction()) {
                throw new Exception('Could not begin transaction');
            }
            
            try {
                // First, check if a record already exists for this supervisor
                $checkStmt = $conn->prepare("SELECT id FROM moa_form WHERE supervisor_id = ?");
                $checkStmt->execute([$supervisorID]);
                
                if ($checkStmt->rowCount() > 0) {
                    // Update existing record
                    $sql = "UPDATE moa_form SET 
                        company_name = ?,
                        company_address = ?,
                        nature_of_business = ?,
                        contact_person_name = ?,
                        company_position = ?,
                        email_address = ?,
                        start_date_validity = ?,
                        end_date_validity = ?,
                        created_at = NOW()
                        WHERE supervisor_id = ?";
                    
                    $params = [
                        $company_name,
                        $company_address,
                        $nature_of_business,
                        $contact_person_name,
                        $company_position,
                        $email_address,
                        $start_date_validity,
                        $end_date_validity,
                        $supervisorID
                    ];
                    
                    $action = 'updated';
                } else {
                    // Insert new record
                    $sql = "INSERT INTO moa_form (
                        supervisor_id,
                        company_name, 
                        company_address, 
                        nature_of_business, 
                        contact_person_name, 
                        company_position, 
                        email_address, 
                        start_date_validity, 
                        end_date_validity,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    
                    $params = [
                        $supervisorID,
                        $company_name,
                        $company_address,
                        $nature_of_business,
                        $contact_person_name,
                        $company_position,
                        $email_address,
                        $start_date_validity,
                        $end_date_validity
                    ];
                    
                    $action = 'inserted';
                }
                
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute($params);
                
                if ($action === 'inserted') {
                    $lastInsertId = $conn->lastInsertId();
                } else {
                    $lastInsertId = $checkStmt->fetchColumn();
                }
                
                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    $errorMsg = "Failed to save form data: " . ($errorInfo[2] ?? 'Unknown error');
                    error_log($errorMsg);
                    error_log('SQL: ' . $sql);
                    error_log('Params: ' . print_r($params, true));
                    throw new Exception($errorMsg);
                }
                
                // Commit the transaction
                if (!$conn->commit()) {
                    throw new Exception('Failed to commit transaction');
                }

                // Display success message
                echo '<div style="text-align: center; padding: 20px;">';
                echo '<div style="color: #4caf50; font-size: 1.2em; margin-bottom: 20px;">';
                echo '✓ Form submitted successfully!';
                echo '</div>';
                echo '<p>Redirecting to MOA Application page...</p>';
                echo '</div>';
                
                // Clear any existing session data
                unset($_SESSION['error']);
                $_SESSION['success'] = 'MOA form submitted successfully!';
                
                // Redirect to MOA Application Renewal page
                header('Location: moa_application_renewal.php');
                exit();
                
            } catch (PDOException $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                error_log('PDO Exception: ' . $e->getMessage());
                $error = 'Database error: ' . $e->getMessage();
            } catch (Exception $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                error_log('Exception: ' . $e->getMessage());
                $error = $e->getMessage();
            }
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $error = $e->getMessage();
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Appointment</title>
    <link rel="shortcut icon" href="images/Picture1.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        @import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap');
        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            overflow-y: auto;
            margin-top: -30rem;
        }
        .content-wrap {
            width: 100%;
            margin: 0 auto;
            position: relative;
            padding-left: 19.5rem;
            padding-top: 7rem;
        }
        .profile-container {
            background-color: #fff;
            position: fixed;
            top: 7rem;
            left: 19.5rem;
            right: 0;
            bottom: 0;
            padding: 2rem;
            overflow-y: auto;
        }


        .page-title p {
            color: #000;
            margin-bottom: 4px;
        }

        .back-button {
            font-size: 1rem;
            margin-left: -10px;
            position: relative;
            top: -8px;
        }
        .back-button:hover {
            color: #700000 !important;
        }
        .back-button img {
            opacity: 0.7;
            transition: opacity 0.2s, filter 0.2s;
        }
        .back-button:hover img {
            opacity: 1;
            filter: brightness(0) saturate(100%) invert(15%) sepia(91%) saturate(7476%) hue-rotate(346deg) brightness(47%) contrast(110%);
        }
        .form-label {
            font-family: 'Source Sans Pro', Arial, sans-serif !important;
            font-size: 1.1rem !important;
            color: #000 !important;
            font-weight: 100 !important;
        }
        .form-control {
            background: #d9d9d9 !important;
            border: 1px solid #d9d9d9 !important;
        }
        .btn-agree-choice {
            padding: 6px 0 !important;
            border-radius: 10px !important;
            font-size: 18px !important;
            font-weight: 250 !important;
        }
        .btn-agree-choice:hover {
            cursor: pointer;
        }
        .force-underline {
            text-decoration: underline !important;
        }
        /* SweetAlert2 Custom Styles */
        .swal2-popup, .swal-custom-popup {
            border-radius: 40px !important;
            padding: 60px 30px 40px 30px !important;
            background-color: #700000 !important;
            position: relative;
        }
        .swal2-icon, .swal-custom-icon {
            position: absolute !important;
            left: 50% !important;
            top: 20px !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
            z-index: 2 !important;
            background-color: #700000 !important;
            border: 3px solid #ffc107 !important;
            color: #ffc107 !important;
            animation: animate 0.5s ease-in-out !important;
            transition: none !important;
        }
        .swal2-icon.swal2-warning .swal2-icon-content {
            color: #ffc107 !important;
        }
        .swal2-icon.swal2-warning {
            margin-top: -20px !important;
        }
        .swal-confirm-proceed {
            background-color:rgb(255, 255, 255) !important;
            color: #000000 !important;
            padding: 12px 50px !important;
            margin-top: 1rem;
        }
        .swal-confirm-proceed:hover {
            background-color: #ffc107 !important;
            color:rgb(0, 0, 0) !important;
        }
        .swal-text-white {
            color: #fff !important;
        }
        .move-text-up {
            margin-top: -20px !important;
        }
        .title-color {
            color: #ffc107 !important;
        }

    </style>
</head>
<body>
    <?php require_once 'templates/supervisor_navbar.php'; ?>

    <div class="content-wrap">
        <div class="profile-container">
            <div class="page-header">
                <div class="page-title">
                    <a href="javascript:history.back()" class="back-button">
                        <img src="images/less-than.png" alt="Back" style="width: 30px; height: 30px; color: #333;">
                        Back
                    </a>
                    <br><br>
                    <h1 style="font-size: 17px;"><b>Renew MOA Validity</b></h1>
                    <br><br>
                    <form method="post" action="" style="width: 100%; padding: 0;">
                        <div class="form-group mb-3">
                            <label for="hte_name" class="form-label"><b>HTE Name <span style='color:red'>*</span></b></label>
                            <input type="text" class="form-control" id="hte_name" name="hte_name" required autocomplete="off" placeholder="Ex. ABC Corp.">
                        </div>
                        <div class="form-group mb-3">
                            <label for="hte_address" class="form-label"><b>Address <span style='color:red'>*</span></b></label>
                            <input type="text" class="form-control" id="hte_address" name="hte_address" required autocomplete="off" placeholder="Ex. 88 Sampaguita St., Brgy. Malinis, Quezon City">
                        </div>
                        <div class="form-group mb-3">
                            <label for="nature_of_business" class="form-label"><b>Nature of Business <span style='color:red'>*</span></b></label>
                            <input type="text" class="form-control" id="nature_of_business" name="nature_of_business" required autocomplete="off" placeholder="Ex. Graphic Designing">
                        </div>
                        <div class="form-group mb-3">
                            <label for="contact_person" class="form-label"><b>Contact Person <span style='color:red'>*</span></b></label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" required autocomplete="off" placeholder="Ex. Juan Dela Cruz">
                        </div>
                        <div class="form-group mb-3">
                            <label for="position" class="form-label"><b>Please specify your position in the company <span style='color:red'>*</span></b></label>
                            <input type="text" class="form-control" id="position" name="position" required autocomplete="off" placeholder="Ex. Human Resources Head">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email_address" class="form-label"><b>Email Address <span style='color:red'>*</span></b></label>
                            <input type="email" class="form-control" id="email_address" name="email_address" required autocomplete="off" placeholder="Ex. abcd@gmail.com">
                        </div>
                        <div class="form-group mb-3" style="width:10%; min-width:200px;">
                            <label for="start_validity" class="form-label"><b>Starting Date of Validity <span style='color:red'>*</span></b></label>
                            <input type="date" class="form-control" id="start_validity" name="start_validity" required>
                        </div>
                        <div class="form-group mb-4" style="width:10%; min-width:200px;">
                            <label for="end_validity" class="form-label"><b>End Date of Validity <span style='color:red'>*</span></b></label>
                            <input type="date" class="form-control" id="end_validity" name="end_validity" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                            <small class="text-muted">Automatically set to 3 years from start date</small>
                        </div>
                        <br>
                        <div class="mb-4" style="font-size:1rem; color:#222;">
                            Do you agree to the Polytechnic University of the Philippines' <a href="#" class="force-underline" style="color:#0c0c9b; font-weight:bold;">Terms of Agreement</a>? <span style="color:red">*</span>
                        </div>
                        <div class="mb-4 d-flex" style="gap: 20px; justify-content: flex-start;">
                            <button type="button" class="btn-agree-choice" id="btn-no" style="border:2px solid #700000; color:#700000; background:transparent; font-weight:bold; width:90px;">No</button>
                            <button type="button" class="btn-agree-choice" id="btn-yes" style="border:2px solid #700000; color:#700000; background:transparent; font-weight:bold; width:90px;">Yes</button>
                            <input type="hidden" name="agree_terms" id="agree_terms" value="">
                        </div>
                        <div class="mb-4 d-flex align-items-center" style="color:#700000; font-size:0.85rem; gap:8px;">
                            <img src="images/warning-sign.png" alt="" id="moa-warning-img" style="width:20px; height:20px; object-fit:contain; filter: invert(14%) sepia(93%) saturate(7480%) hue-rotate(347deg) brightness(52%) contrast(123%); vertical-align:middle;" />
                            <span>Failure to agree to the University's terms of agreement renders you uneligible to renew your MOA.</span>
                        </div>
                        <br><br>
                        <button type="submit" class="btn btn-primary" id="moa-renew-submit" style="display:block; width:180px; margin:32px auto 0 auto;margin-bottom:1rem; background:#0c0c9b; border:none;font-size:1rem; font-weight:bold; padding: 16px 0;">Submit</button>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Auto-calculate end date when start date changes
                            const startDateInput = document.getElementById('start_validity');
                            const endDateInput = document.getElementById('end_validity');
                            
                            startDateInput.addEventListener('change', function() {
                                if (this.value) {
                                    const startDate = new Date(this.value);
                                    const endDate = new Date(startDate);
                                    endDate.setFullYear(endDate.getFullYear() + 3);
                                    
                                    // Format the date as YYYY-MM-DD for the date input
                                    const formattedDate = endDate.toISOString().split('T')[0];
                                    endDateInput.value = formattedDate;
                                    
                                    // Set minimum date for end date to be the same as start date
                                    endDateInput.min = this.value;
                                    
                                    // Ensure the field remains read-only after setting the value
                                    endDateInput.readOnly = true;
                                } else {
                                    endDateInput.value = '';
                                }
                            });
                            
                            // Set minimum date for start date to today
                            const today = new Date().toISOString().split('T')[0];
                            startDateInput.min = today;
                            
                            // Set minimum date for end date initially
                            endDateInput.min = today;
                            const submitBtn = document.getElementById('moa-renew-submit');
                            const form = submitBtn.closest('form');
                            submitBtn.addEventListener('click', function(e) {
                                const emailInput = document.getElementById('email_address');
                                const email = emailInput.value.trim();
                                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                                
                                if (email && !emailRegex.test(email)) {
                                    e.preventDefault();
                                    Swal.fire({
                                        icon: 'error',
                                        title: '<span style="color: ffc107">Invalid Email</span>',
                                        html: '<span style="color: white">Please enter a valid email address (e.g., example@gmail.com)</span>',
                                        customClass: {
                                            confirmButton: 'swal-confirm-proceed'
                                        }
                                    });
                                    emailInput.focus();
                                    return;
                                }

                                const requiredFields = [
                                    document.getElementById('contact_person'),
                                    document.getElementById('position'),
                                    document.getElementById('email_address'),
                                    document.getElementById('start_validity'),
                                    document.getElementById('end_validity'),
                                ];
                                let emptyField = false;
                                requiredFields.forEach(f => {
                                    if (!f.value || f.value.trim() === '') emptyField = true;
                                });
                                const agreeInput = document.getElementById('agree_terms');
                                if (!agreeInput.value) emptyField = true;
                                if (agreeInput.value === 'no') {
                                    e.preventDefault();
                                    Swal.fire({
                                        title: 'Cannot Proceed',
                                        html: `<span class=\"swal-text-white move-text-up\">You cannot proceed with the renewal if you do not agree to the University's Terms and Conditions.</span>`,
                                        icon: 'warning',
                                        customClass: {
                                            popup: 'swal-custom-popup',
                                            icon: 'swal-custom-icon',
                                            title: 'title-color',
                                            confirmButton: 'swal-confirm-proceed'
                                        }
                                    });
                                    return false;
                                }
                                if (emptyField) {
                                    e.preventDefault();
                                    Swal.fire({
                                        title: 'Incomplete Form',
                                        html: `<span class=\"swal-text-white move-text-up\">Please fill out all required fields and agree to the terms before submitting.</span>`,
                                        icon: 'warning',
                                        customClass: {
                                            popup: 'swal-custom-popup',
                                            icon: 'swal-custom-icon',
                                            title: 'title-color',
                                            confirmButton: 'swal-confirm-proceed'
                                        }
                                    });
                                    return false;
                                } else {
                                    // Show loading state
                                    submitBtn.disabled = true;
                                    submitBtn.innerHTML = 'Submitting...';
                                    
                                    // Submit the form
                                    form.submit();
                                    return true;
                                }
                            });
                            const btnNo = document.getElementById('btn-no');
                            const btnYes = document.getElementById('btn-yes');
                            const agreeInput = document.getElementById('agree_terms');
                            let selected = '';
                            function setActive(btn, value) {
                                if (selected === value) {
                                    btnNo.style.background = 'transparent';
                                    btnNo.style.color = '#700000';
                                    btnYes.style.background = 'transparent';
                                    btnYes.style.color = '#700000';
                                    btnNo.style.borderColor = '#700000';
                                    btnYes.style.borderColor = '#700000';
                                    selected = '';
                                    agreeInput.value = '';
                                } else {
                                    btnNo.style.background = 'transparent';
                                    btnNo.style.color = '#700000';
                                    btnYes.style.background = 'transparent';
                                    btnYes.style.color = '#700000';
                                    btnNo.style.borderColor = '#700000';
                                    btnYes.style.borderColor = '#700000';
                                    btn.style.background = '#700000';
                                    btn.style.color = '#fff';
                                    selected = value;
                                    agreeInput.value = value;
                                }
                            }
                            btnNo.addEventListener('click', function() { setActive(btnNo, 'no'); });
                            btnYes.addEventListener('click', function() { setActive(btnYes, 'yes'); });
                        });
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Common Scripts -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>


    <?php
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
        ?>
        <script>
            sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
        </script>
        <?php
        unset($_SESSION['status']);
    } 
    ?>
</body>
</html> 