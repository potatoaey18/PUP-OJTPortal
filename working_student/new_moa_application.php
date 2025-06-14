<?php
include '../../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id'])) {
    header('Location: index.php');
    exit;
}

$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/PUP/student/uploads/moa/";
$relative_dir = "/PUP/student/uploads/moa/";

if (isset($_POST['submit'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
        error_log("Created directory: " . $target_dir);
    }

    $original_name = basename($_FILES["moaDocument"]["name"]);
    $file_name = $original_name;
    $target_file = $target_dir . $student_id . "_" . $file_name;
    $relative_path = $relative_dir . $student_id . "_" . $file_name;

    error_log("Original file name: " . $original_name);
    error_log("Target file path: " . $target_file);

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES["moaDocument"]["tmp_name"]);
    $allowed_mimes = [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    if (!in_array($mime, $allowed_mimes)) {
        error_log("Invalid MIME type: " . $mime);
        $_SESSION['status'] = "Invalid file type. Only PDF, DOC, and DOCX are allowed.";
        $_SESSION['status-code'] = "error";
        header("Location: new_moa_application.php");
        exit;
    }

    if ($_FILES["moaDocument"]["size"] > 10000000) {
        error_log("File size exceeds limit: " . $_FILES["moaDocument"]["size"]);
        $_SESSION['status'] = "File size exceeds 10MB limit.";
        $_SESSION['status-code'] = "error";
        header("Location: new_moa_application.php");
        exit;
    }

    if (file_exists($target_file)) {
        error_log("Existing file deleted: " . $target_file);
        unlink($target_file);
    }

    if (move_uploaded_file($_FILES["moaDocument"]["tmp_name"], $target_file)) {
        error_log("File successfully moved to: " . $target_file);
        try {
            $stmt = $conn->prepare("SELECT * FROM new_moa_processing WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $existing_moa = $stmt->fetch(PDO::FETCH_ASSOC);

            $new_status = 'checking_info';
            if ($existing_moa && $existing_moa['status'] === 'signed_moa_retrieved') {
                $_SESSION['status'] = "MOA already processed. Contact support to resubmit.";
                $_SESSION['status-code'] = "warning";
                header("Location: new_moa_application.php");
                exit;
            }

            if ($existing_moa) {
                $sql = "UPDATE new_moa_processing 
                        SET moa_document_path = ?, status = ?, request_date = NOW(), 
                            checking_info_date = NOW(), ulco_review_date = NULL, 
                            returned_to_coordinator_date = NULL, dean_vpaa_signature_date = NULL, 
                            signed_moa_retrieved_date = NULL
                        WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$relative_path, $new_status, $existing_moa['id']]);
            } else {
                $sql = "INSERT INTO new_moa_processing 
                        (student_id, moa_document_path, status, request_date, checking_info_date) 
                        VALUES (?, ?, ?, NOW(), NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$student_id, $relative_path, $new_status]);
            }

            error_log("Database updated successfully. Path: " . $relative_path);
            $_SESSION['status'] = "MOA submitted successfully. Awaiting review.";
            $_SESSION['status-code'] = "success";
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['status'] = "System error. Contact support.";
            $_SESSION['status-code'] = "error";
        }
    } else {
        error_log("File move failed. Error: " . $_FILES["moaDocument"]["error"]);
        $_SESSION['status'] = "Upload failed. Error: " . $_FILES["moaDocument"]["error"];
        $_SESSION['status-code'] = "error";
    }
    
    header("Location: new_moa_application.php");
    exit;
}

try {
    $stmt = $conn->prepare("SELECT *, DATE_FORMAT(request_date, '%M %d, %Y %H:%i') AS formatted_date 
                           FROM new_moa_processing 
                           WHERE student_id = ?");
    $stmt->execute([$_SESSION['auth_user']['student_id']]);
    $existing_moa = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$existing_moa) {
        error_log("No existing MOA found for student_id: " . $_SESSION['auth_user']['student_id']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $existing_moa = null;
}

$steps = [
    'checking_info' => 1,
    'ulco_review' => 2,
    'returned_to_coordinator' => 3,
    'dean_vpaa_signature' => 4,
    'signed_moa_retrieved' => 5,
    'rejected' => 0
];

// Sync status with dates
if ($existing_moa && $existing_moa['status'] !== 'rejected') {
    $status_from_dates = 'checking_info';
    if (!empty($existing_moa['signed_moa_retrieved_date'])) $status_from_dates = 'signed_moa_retrieved';
    elseif (!empty($existing_moa['dean_vpaa_signature_date'])) $status_from_dates = 'dean_vpaa_signature';
    elseif (!empty($existing_moa['returned_to_coordinator_date'])) $status_from_dates = 'returned_to_coordinator';
    elseif (!empty($existing_moa['ulco_review_date'])) $status_from_dates = 'ulco_review';
    elseif (!empty($existing_moa['checking_info_date'])) $status_from_dates = 'checking_info';

    if ($existing_moa['status'] !== $status_from_dates) {
        $stmt = $conn->prepare("UPDATE new_moa_processing SET status = ? WHERE id = ?");
        $stmt->execute([$status_from_dates, $existing_moa['id']]);
        $existing_moa['status'] = $status_from_dates;
    }
}

// Determine current step based on status and dates
$current_step = 0;
if ($existing_moa) {
    $status_step = $steps[$existing_moa['status']] ?? 0;
    $date_step = 0;
    if (!empty($existing_moa['checking_info_date'])) $date_step = max($date_step, 1);
    if (!empty($existing_moa['ulco_review_date'])) $date_step = max($date_step, 2);
    if (!empty($existing_moa['returned_to_coordinator_date'])) $date_step = max($date_step, 3);
    if (!empty($existing_moa['dean_vpaa_signature_date'])) $date_step = max($date_step, 4);
    if (!empty($existing_moa['signed_moa_retrieved_date'])) $date_step = max($date_step, 5);
    $current_step = ($existing_moa['status'] === 'rejected') ? 0 : max($status_step, $date_step);
    error_log("Status: " . ($existing_moa['status'] ?? 'none') . ", Status Step: $status_step, Date Step: $date_step, Current Step: $current_step");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OJT Web Portal: Student Profile</title>
    <!-- ================= Favicon ================== -->
    <link rel="shortcut icon" href="images/Picture1.png">
    
    <!-- Common -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="endorsement-css/endorsement-moa.css" rel="stylesheet">
    <style>
        body {
            background-color: #F8F8FF;
            font-family: source sans pro, sans-serif;
            color: #000;    
        }
        .btn-download {
            display: block;
            margin-bottom: 1rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .hidden {
            display: none;
        }
        .document-area {
            width: 100%;
            height: 500px;
            overflow-y: auto; 
            overflow-x: hidden; 
            position: relative;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        .document-area iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        .document-area.enlarged {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            height: 80%;
            z-index: 1000;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }   
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .overlay.active {
            display: block;
        }
        .timeline-container {
            border: 4px solid #8B0000;
            border-radius: 10px;
            padding: 40px;
            background-color: #fff;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .timeline-title {
            text-align: left;
            font-size: 20px;
            font-weight: bold;
            color: #800020;
        }
        .timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #ddd;
            z-index: 0;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 20%;
            position: relative;
            z-index: 1;
        }
        .step-icon {
            width: 50px;
            height: 50px;
            background-color: #8B0000;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .step-label {
            text-align: center;
            font-size: 14px;
            color: #333;
            max-width: 120px;
            line-height: 1.3;
        }
        .step.completed .step-icon {
            background-color: #8B0000;
        }
        .step.active .step-icon {
            background-color: #8B0000;
            box-shadow: 0 0 10px rgba(139, 0, 0, 0.5);
        }
        .step.incomplete .step-icon {
            background-color: #ccc;
        }
        .timeline-progress {
            position: absolute;
            top: 25px;
            left: 0;
            height: 2px;
            background-color: #8B0000;
            z-index: 0;
            transition: width 0.3s ease;
        }
        .timeline-footer {
            text-align: center;
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        .notification {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #333;
        }
        .notification.success {
            background-color: #e0f7e0;
            color: #2e7d32;
        }
        .notification.warning {
            background-color: #repealed;
        }
        .notification.error {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .instructions {
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .doc-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        .btn2-download-template {
            background-color: #8B0000;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!---------NAVIGATION BAR-------->
    <?php
    require_once 'templates/stud_navbar.php';
    ?>
    <!---------NAVIGATION BAR ENDS-------->

    <div class="content-wrap" style="height: 80%; width: 100%;margin: 0 auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div>
                <div>
                    <a href="partner_companies.php" class="back-button">
                        <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                        Back
                    </a>
                </div>
                <div class="page-header">
                    <div class="page-title">
                        <h1 style="font-size: 16px;"><b>Application for New Memorandum of Agreement</b></h1>
                        <p>You can monitor your MOA's Application here.</p><br>
                    </div>
                </div>
                <?php if ($existing_moa): ?>
                    <div class="timeline-container">
                        <div class="timeline-title">Process Timeline</div><br>
                        <div class="timeline">
                            <div class="timeline-progress" style="width: <?php echo $current_step * 20; ?>%"></div>
                            
                            <div class="step <?php echo $current_step >= 1 ? 'completed' : 'incomplete'; ?> <?php echo $current_step == 1 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <?php if ($current_step > 1): ?>
                                        <i class="fa fa-check" style="color: white;"></i>
                                    <?php else: ?>
                                        <i class="fa fa-refresh" style="color: white;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-label">Checking the Informations</div>
                            </div>
                            
                            <div class="step <?php echo $current_step >= 2 ? 'completed' : 'incomplete'; ?> <?php echo $current_step == 2 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <?php if ($current_step > 2): ?>
                                        <i class="fa fa-check" style="color: white;"></i>
                                    <?php else: ?>
                                        <i class="fa fa-search" style="color: white;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-label">For ULCO's review and approval</div>
                            </div>
                            
                            <div class="step <?php echo $current_step >= 3 ? 'completed' : 'incomplete'; ?> <?php echo $current_step == 3 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <?php if ($current_step > 3): ?>
                                        <i class="fa fa-check" style="color: white;"></i>
                                    <?php else: ?>
                                        <i class="fa fa-reply" style="color: white;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-label">MOA returned to OJT coordinator</div>
                            </div>
                            
                            <div class="step <?php echo $current_step >= 4 ? 'completed' : 'incomplete'; ?> <?php echo $current_step == 4 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <?php if ($current_step > 4): ?>
                                        <i class="fa fa-check" style="color: white;"></i>
                                    <?php else: ?>
                                        <i class="fa fa-pencil" style="color: white;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-label">For Dean and VPAA's signature</div>
                            </div>
                            
                            <div class="step <?php echo $current_step >= 5 ? 'completed' : 'incomplete'; ?> <?php echo $current_step == 5 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <i class="fa fa-thumbs-up" style="color: white;"></i>
                                </div>
                                <div class="step-label">Retrieve the signed MOA</div>
                            </div>
                        </div>
                </div>
                
                        
                <div class="timeline-footer">
                            <p>Once you reach "retrieve signed MOA," you can now go to ITECH. <br>
                            ITECH's working hours: <br>
                            Monday to Friday <br>
                            7 AM - 6 PM
                        </div>
                
                <?php if (!empty($document_status)): ?>
                    <div class="notification <?php echo $_SESSION['status-code'] ?? ''; ?>">
                        <?php echo $document_status; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="page-header">
                    <div class="page-title">
                        <h1 style="font-size: 16px;"><b>Application for New Memorandum of Agreement</b></h1><br>
                    </div>
                </div>

                <div class="instructions" style="font-size: 14px; padding-left: 20px;">
                    <p>Step 1: Download the attached template MOA file.<br>
                       Step 2: The desired HTE shall review the proposed PUP MOA to consider applicable terms and conditions and incorporate other terms deemed appropriate.<br>
                       Step 3: After filling out the MOA template, submit it accompanied by the <b>HTE's SEC registration or DTI registration</b>.<br>
                    </p>
                    <br>
                    <p>
                        <b>Note: Make sure to submit the filled-out MOA template along with the HTE's SEC or DTI registration to avoid errors and repeating the process.</b>
                    </p>
                </div>
                <br><br>

                <div>
                    <h1 class="moa-title">Memorandum of Agreement Document Template</h1>
                </div>

                <div class="template-box">
                    <a href="templates/endorsement/New_MOA_Template.docx" download="New_MOA_Template.docx" class="btn1-download-template"><img src="images/doc.png" alt="" class="doc-icon" ><u>New_MOA_Template.docx</u></a>
                    <a href="templates/endorsement/New_MOA_Template.docx" download="New_MOA_Template.docx" class="btn2-download-template btn-download">
                        <i class="fa fa-download"></i>
                    </a>
                </div>

                <div>
                    <h1 class="title">Memorandum of Agreement</h1>
                </div>

                <div class="box">
                    <div class="content">
                        <div class="document-area" id="documentArea">
                            <div id="placeholderText">No uploads to show</div>
                            <img id="documentImage" class="hidden">
                        </div>

                        <div class="action-buttons">
                            <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
                                <input type="file" id="fileInput" name="moaDocument" accept=".pdf,.doc,.docx" class="hidden">
                                <button type="button" class="btn btn-upload" id="uploadBtn">
                                    <i class="fa fa-upload btn-icon"></i> Upload
                                </button>
                                <button type="button" class="btn btn-disabled" id="viewBtn" disabled>
                                    <i class="fa fa-eye btn-icon"></i> View
                                </button>
                                <button type="button" class="btn btn-disabled" id="editBtn" disabled>
                                    <i class="fa fa-edit btn-icon"></i> Edit
                                </button>
                                <button type="button" class="btn btn-disabled" id="printBtn" disabled>
                                    <i class="fa fa-print btn-icon"></i> Print
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const viewBtn = document.getElementById('viewBtn');
        const editBtn = document.getElementById('editBtn');
        const printBtn = document.getElementById('printBtn');
        const documentImage = document.getElementById('documentImage');
        const placeholderText = document.getElementById('placeholderText');
        const uploadForm = document.getElementById('uploadForm');
        const uploadBtn = document.getElementById('uploadBtn');
        const documentArea = document.getElementById('documentArea');

        if (uploadBtn) {
            uploadBtn.addEventListener('click', function() {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function(event) {
                if (event.target.files.length > 0) {
                    const file = event.target.files[0];

                    if (placeholderText) {
                        placeholderText.classList.add('hidden');
                    }

                    if (uploadBtn) {
                        uploadBtn.style.display = 'none';
                    }

                    viewBtn.removeAttribute('disabled');
                    viewBtn.classList.remove('btn-disabled');
                    viewBtn.classList.add('btn-actions');
                    viewBtn.innerHTML = '<i class="fa fa-eye btn-icon"></i> View';

                    editBtn.removeAttribute('disabled');
                    editBtn.classList.remove('btn-disabled');
                    editBtn.classList.add('btn-actions');
                    editBtn.innerHTML = '<i class="fa fa-edit btn-icon"></i> Edit';

                    printBtn.removeAttribute('disabled');
                    printBtn.classList.remove('btn-disabled');
                    printBtn.classList.add('btn-actions');
                    printBtn.innerHTML = '<i class="fa fa-print btn-icon"></i> Print';

                    let submitBtn = document.getElementById('submitBtn');
                    if (!submitBtn) {
                        submitBtn = document.createElement('button');
                        submitBtn.className = 'btn btn-submit';
                        submitBtn.id = 'submitBtn';
                        submitBtn.name = 'submit';
                        submitBtn.type = 'submit';
                        submitBtn.innerHTML = '<i class="fa fa-paper-plane btn-icon"></i> Submit';
                        uploadForm.appendChild(submitBtn);
                    }
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('disabled');

                    if (file.type === 'application/pdf') {
                        clearPreviewArea();
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const pdfIframe = document.createElement('iframe');
                            pdfIframe.src = e.target.result;
                            pdfIframe.frameBorder = '0';
                            documentArea.appendChild(pdfIframe);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        documentImage.classList.add('hidden');
                        clearPreviewArea();
                        const placeholder = document.createElement('div');
                        placeholder.textContent = 'Preview not available for this file type.';
                        documentArea.appendChild(placeholder);
                    }
                }
            });
        }

        function clearPreviewArea() {
            if (documentArea) {
                while (documentArea.firstChild) {
                    documentArea.removeChild(documentArea.firstChild);
                }
            }
        }

        console.log("Current step: <?php echo $current_step; ?>");
    });
    </script>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['status']) && $_SESSION['status'] != '') { ?>
            swal({
                title: "Notice",
                text: "<?php echo $_SESSION['status']; ?>",
                type: "<?php echo $_SESSION['status-code']; ?>",
                confirmButtonText: "OK"
            });
        <?php unset($_SESSION['status']); unset($_SESSION['status-code']); ?>
        <?php } ?>
    });
    </script>
</body>
</html>