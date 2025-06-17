<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$has_moa = false;
$moa_file_url = '';

$company_name = '';
if (isset($_SESSION['auth_user']['supervisor_id'])) {
    $supervisor_id = $_SESSION['auth_user']['supervisor_id'];
    $stmt = $conn->prepare("SELECT company_name FROM supervisor WHERE id = ?");
    $stmt->execute([$supervisor_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && !empty($result['company_name'])) {
        $company_name = $result['company_name'];
        
        $stmt = $conn->prepare("SELECT id, moa_file, renewal_status FROM company_moa WHERE company_name = ?");
        $stmt->execute([$company_name]);
        $moa_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $renewal_status = $moa_data['renewal_status'] ?? ''; 
        
        if ($moa_data && !empty($moa_data['moa_file'])) {
            $has_moa = true;
            $moa_file_url = '../' . ltrim($moa_data['moa_file'], '/');
            
            $local_file_path = realpath(__DIR__ . '/../' . ltrim($moa_data['moa_file'], '/'));
            
            if ($local_file_path && file_exists($local_file_path)) {
                $pdf_data = file_get_contents($local_file_path);
                $pdf_base64 = base64_encode($pdf_data);
                $pdf_data_url = 'data:application/pdf;base64,' . $pdf_base64;
            }
        }
        
        $_SESSION['auth_user']['company_name'] = $company_name;
    }
}

error_log("Supervisor ID: " . ($_SESSION['auth_user']['supervisor_id'] ?? 'Not set'));
error_log("Company Name: " . $company_name);
error_log("Has MOA: " . ($has_moa ? 'Yes' : 'No'));
error_log("MOA File URL: " . $moa_file_url);

$show_success = false;
$evaluation_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['moa_file'])) {
    $file = $_FILES['moa_file'];
    $errors = [];

    if (empty($company_name)) {
        $company_name = $_SESSION['auth_user']['supervisor_company'] ?? 'Unnamed_Company_' . time();
    }

    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = 'Only PDF, DOC, and DOCX files are allowed.';
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error: ' . $file['error'];
    }

    $uploadDir = __DIR__ . '/moa_files/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $filename = 'moa_' . preg_replace('/[^a-zA-Z0-9]/', '_', $company_name) . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $targetPath = $uploadDir . $filename;
    $relativePath = 'supervisor/moa_files/' . $filename;

    if (empty($errors)) {
        try {
            $conn->beginTransaction();
            
            $checkStmt = $conn->prepare("SELECT * FROM company_moa WHERE company_name = ?");
            $checkStmt->execute([$company_name]);
            $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                if ($existingRecord) {
                    $stmt = $conn->prepare("UPDATE company_moa SET moa_file = ? WHERE company_name = ?");
                    $stmt->execute([$relativePath, $company_name]);
                    $message = "MOA file updated successfully!";
                } else {
                    $stmt = $conn->prepare("INSERT INTO company_moa (moa_file, company_name) VALUES (?, ?)");
                    $stmt->execute([$relativePath, $company_name]);
                    $message = "MOA file uploaded successfully!";
                }
                
                $conn->commit();
                $_SESSION['upload_success'] = $message;
                $_SESSION['just_uploaded_file'] = $targetPath;
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                throw new Exception("Failed to move uploaded file.");
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['upload_error'] = 'Failed to process MOA file: ' . $e->getMessage();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $_SESSION['upload_error'] = implode("\n", $errors);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}


if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Appointment</title>
    <style>
    .success-message-container {
        display: block !important;
        margin: 20px auto !important;
        width: 100% !important;
        max-width: 500px !important;
    }
    .success-message-box {
        border: 3px solid #28a745 !important;
        border-radius: 12px !important;
        background-color: #f8f9fa !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        padding: 25px 30px !important;
    }
    .success-message-content {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        gap: 15px !important;
    }
    .success-icon-wrapper {
        background-color: #d4edda !important;
        border-radius: 50% !important;
        width: 80px !important;
        height: 80px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto 15px !important;
    }
    .success-icon {
        color: #28a745 !important;
        width: 48px !important;
        height: 48px !important;
    }
    .success-title {
        color: #28a745 !important;
        font-size: 24px !important;
        font-weight: 700 !important;
        margin: 0 !important;
    }
 
    </style>
    <link rel="shortcut icon" href="images/pupLogo.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        .swal-custom-popup {
            background-color: #700000 !important;
            color: #ffc107 !important;
        }
        .swal-title, .swal-text {
            color: white !important;
        }
        .swal-title-white {
            color:rgb(255, 255, 255) !important;
        }
        .swal-text-white {
            color:rgb(255, 255, 255) !important;
        }
        
        .swal2-icon.swal2-success {
            border-color: #ffc107 !important;
            color: #ffc107 !important;
        }
        .swal2-icon.swal2-success .swal2-success-ring {
            border: 0.25em solid rgba(255, 193, 7, 0.3) !important;
        }
        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #ffc107 !important;
        }
        .swal2-icon.swal2-success .swal2-success-fix {
            background-color: transparent !important;
        }
        .swal2-icon.swal2-success .swal2-success-circular-line-left,
        .swal2-icon.swal2-success .swal2-success-circular-line-right {
            background: transparent !important;
        }

        
        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            overflow-y: auto;
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
        .required-hours, .hours-info {
            margin-bottom: 8px !important;
        }
        .required-hours span:first-child,
        .hours-info span:first-child,
        .MOA-validity span:first-child {
            display: inline-block;
            min-width: 190px;
            font-weight: bold;
        }
        .colon {
            display: inline-block;
            min-width: 18px;
            text-align: right;
        }

        .swal2-title{
            margin-top: 20px;
        }
        .swal2-html-container {
            margin-bottom: 20px !important;
        }
        .MOA-validity span:first-child {
            color: #700000;
        }
        .MOA-validity {
            font-size: 15px;
        }

        .moa-status-container {
            border: 2px solid #700000;
            border-radius: 15px;
            padding: 18px 20px;
            margin-top: 4rem;
            background: none;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            bottom: 0;
            height: calc(100vh - 5 rem);
            margin-bottom: 5rem;
        }
        .moa-newfile-container {
            border: 2px solid #700000;
            border-radius: 15px;
            padding: 18px 20px;
            background: none;
            width: 80%;
            min-height: 80px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            margin-top: 8rem;
        }
        .moa-status-label {
        position: absolute;
        top: -26px;
        left: 35px;
        background: #fff;
        color: #700000;
        font-size: 25px;
        padding: 0 20px;
        letter-spacing: 0.5px;
    }
    .moa-status-flex {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: flex-start;
        gap: 20px;
    }
    .moa-status-flex p {
        margin: 0;
    }
    .moa-btn-col {
        display: flex;
        flex-direction: column;
    }
    .moa-view-btn, .moa-download-btn, .moa-print-btn, .moa-renew-btn {
        color: #fff;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        margin-bottom: 0.5rem;
    }
    .moa-view-btn {
        padding: 15px 115px;
        border: 2px solid #700000;
        background: #700000;
        color: #fff !important;
    }
    .moa-download-btn {
        padding: 15px 100px;
        border: 2px solid #700000;
        background: #700000;
        color: #fff !important;
        width: 295px !important;
        max-width: 100%;
        box-sizing: border-box;
    }
    .moa-button {
        position: relative;
        width: 180px;
        padding: 0 16px 0 0;
        text-align: center;
        display: block;
    }
    .moa-button img {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        height: 22px;
        width: 22px;
        filter: brightness(0) invert(1);
    }
    .moa-button span {
        display: block;
        width: 100%;
        text-align: center;
        font-weight: bold;
    }
        .moa-status-flex {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
        }
        .moa-status-flex p {
            margin: 0;
        }
        .moa-btn-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            margin-top: 1rem;
        }

        .moa-status-flex .moa-view-btn {
            justify-self: center;
            align-self: center;
        }
        .moa-view-btn, .moa-download-btn, .moa-print-btn, .moa-renew-btn {
            color: #fff;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 0.5rem;
        }
        .moa-view-btn {
            padding: 15px 115px;
            border: 2px solid #700000;
            background: #700000;
            color: #fff !important;
        }
        .moa-edit-btn {
            padding: 15px 110px !important;
        }
        .moa-download-btn {
            padding: 15px 95px;
            border: 2px solid #700000;
            background: #700000;
            color: #fff !important;
        }
        .moa-print-btn {
            padding: 15px 100px;
            border: 2px solid #700000;
            background: #700000;
            color: #fff !important;
            width: 295px!important;
        }
        .moa-renew-btn {
            padding: 15px 85px;
            background: #0c0c9b;
            border: 2px solid #0c0c9b;
            color: #fff !important;
            width: 295px!important;
        }
        .moa-view-btn:hover, .moa-download-btn:hover, .moa-print-btn:hover, .moa-renew-btn:hover,
        .moa-view-btn:active, .moa-download-btn:active, .moa-print-btn:active, .moa-renew-btn:active {
            color: #fff !important;
        }
    
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
    }
    .swal-cancel-proceed {
        background-color:rgb(255, 255, 255) !important;
        color: #000000 !important;
    }
    .swal-confirm-proceed:hover, .swal-cancel-proceed:hover {
        background-color: #ffc107 !important;
        color: #700000 !important;
    }
    .swal-cancel-proceed {
        background-color:rgb(255, 255, 255) !important;
        color: #000000 !important;
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
        
        .moa-download-btn:hover {
            background: #700000;
            border-color: #700000;
        }
        
        .moa-print-btn:hover {
            background: #700000;
            border-color: #700000;
        }
        
        .moa-renew-btn:hover {
            background: #0c0c9b;
            border-color: #0c0c9b;
        }
        
        button:disabled, 
        button[disabled] {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
            cursor: not-allowed !important;
            opacity: 0.7;
            transform: none !important;
            box-shadow: none !important;
        }
       
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
        }
        .swal-cancel-proceed {
            background-color:rgb(255, 255, 255) !important;
            color: #000000 !important;
        }
        .swal-confirm-proceed:hover, .swal-cancel-proceed:hover {
            background-color: #ffc107 !important;
            color: #700000 !important;
        }
        .swal-cancel-proceed {
            background-color:rgb(255, 255, 255) !important;
            color: #000000 !important;
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

        .page-information.moa-renew-steps {
            margin-bottom: 16px;
        }
        .moa-renew-step {
            font-size: 13px;
            color: #333;
            margin-bottom: 4px;
            background: #f5f5f5;
            border-left: 3px solid #700000;
            padding: 6px 12px;
            border-radius: 4px;
        }

    </style>
</head>
<body>
    <?php require_once 'templates/supervisor_navbar.php'; ?>

    <div class="content-wrap">
        <div class="profile-container">
            <div class="page-header">
                <div class="page-title">
                    <a href="moa_status.php" class="back-button">
                        <img src="images/less-than.png" alt="Back" style="width: 30px; height: 30px; color: #333;">
                        Back
                    </a>
                    <br><br>
                    <h1 style="font-size: 17px;"><b>Application for renewal of MOA</b></h1>
                    <br>
                    <div class="page-information moa-renew-steps">
    <p class="moa-renew-step"><b>Step 1:</b> Download the attached template MOA file.</p>
    <p class="moa-renew-step"><b>Step 2:</b> The desired HTE shall review the proposed PUP MOA to consider applicable terms and conditions and incorporate other terms deemed appropriate.</p>
    <p class="moa-renew-step"><b>Step 3:</b> After filling out the MOA template, submit it accompanied by the HTE's SEC registration or DTI registration.</p>
</div>
                    <p class="moa-renew-note" style="font-size:13px; color:#700000; background:#fff6f6; border-left:3px solid #700000; padding:7px 13px; border-radius:4px; margin-bottom:18px;">
    <b>Note:</b> Make sure to submit the filled-out MOA template along with the HTE's SEC or DTI registration to avoid errors and repeating the process.
</p>
                    <div class="moa-newfile-container">
    <span class="moa-status-label">New Moa File</span>
    <div style="display: flex; align-items: center; justify-content: center; width: 100%; position: relative; min-height: 50px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin: 0 auto;">
            <img src="images/doc.png" alt="File Icon" style="height:34px; width:auto; display:inline-block;">
            <a href="downloadable-documents/Renew-MOA.docx" download class="doc-download-link" style="color: #0066cc !important; text-decoration: underline !important; text-underline-offset: 3px; text-decoration-thickness: 2px;">
                Renew-MOA.docx
            </a>
        </div>
        <a href="downloadable-documents/Renew-MOA.docx" download style="position: absolute; right: 0; top: 50%; transform: translateY(-50%);">
            <img id="new-moa-img-preview" src="images/download.png" alt="Download Evaluation Instrument" style="height:28px; width:auto; filter: invert(18%) sepia(95%) saturate(7495%) hue-rotate(349deg) brightness(55%) contrast(105%); cursor:pointer;">
        </a>
    </div>
</div>
<script>
    const imgInput = document.getElementById('new-moa-img-src');
    const imgPreview = document.getElementById('new-moa-img-preview');
    imgInput.addEventListener('input', function() {
        imgPreview.src = this.value || 'images/download.png';
    });

    document.getElementById('moa_file').addEventListener('change', function(e) {
        if (this.files.length > 0) {
            const formData = new FormData(document.getElementById('moaUploadForm'));
            
            const uploadBtn = this.closest('form').querySelector('button');
            const originalBtnHTML = uploadBtn.innerHTML;
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<span>Uploading...</span>';
            
            fetch('upload_moa.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'MOA file uploaded successfully!',
                        icon: 'success',
                        confirmButtonColor: '#700000'
                    });

                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Failed to upload file');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'An error occurred while uploading the file',
                    icon: 'error',
                    confirmButtonColor: '#700000'
                });
            })
            .finally(() => {
                if (!document.getElementById('moa_file').disabled) {
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = originalBtnHTML;
                }
            });
        }
    });
</script>
<div class="moa-status-container">
    <span class="moa-status-label">Memorandum of Agreement</span>
    
    <div class="moa-status-flex">
        <div class="moa-document-area" style="height: 20rem; width:50rem; max-width:600px; min-height:48rem; position:relative; padding:20px 0; margin-bottom:-50px; margin-left: 70px; margin-top: 25px;">
            <style>
                .moa-document-area::before {
                    content: '';
                    position: absolute;
                    top: 0px;
                    bottom: 20px;
                    left: 10px;
                    right: 70px;
                    background: #f9f9f9;
                    border-radius: 6px;
                    z-index: -1;
                    height: 42.5rem;
                }
            </style>
            <?php if ($has_moa && isset($pdf_data_url)): ?>
                <div id="pdf-viewer" style="width:90%; height:90%; display:flex; flex-direction:column;">
                    <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
                        <div id="pdf-container" style="flex:1; display:flex; justify-content:center; border-radius:4px; padding:10px; box-sizing:border-box;">
                            <div id="canvas-container" style="margin:0 auto;">
                                <canvas id="pdf-canvas"></canvas>
                            </div>
                        </div>
                    </div>
                    <div style="text-align:center; margin:40px 0 0; padding:10px 0;">
                        <button id="prev-page" class="btn btn-primary" style="height: 2rem; width: 5.8rem; background-color:#700000; border-color:#700000; margin-right:10px; min-width:40px; font-size: 0.85rem;">
                            <i class="fa ti-arrow-left"></i> Previous
                        </button>
                        <span style="margin:0 15px; line-height:38px; vertical-align:middle; font-weight:bold; font-size: rem;">
                            Page <span id="page-num">1</span> of <span id="page-count">1</span>
                        </span>
                        <button id="next-page" class="btn btn-primary" style="background-color:#700000; border-color:#700000; min-width:40px; font-size: 0.85rem;">
                            Next <i class="fa ti-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
                <script>

                pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js";
                
                let pdfDoc = null;
                let pageNum = 1;
                let pageRendering = false;
                let pageNumPending = null;
                let scale = 1.0;
                let currentScale = 1.0;
                
                const canvas = document.getElementById("pdf-canvas");
                const ctx = canvas.getContext("2d");
                
                function calculateScale(viewport, container) {
                    const containerWidth = container.clientWidth - 40;
                    const containerHeight = container.clientHeight - 40;
                    
                    const scaleX = (containerWidth) / viewport.width;
                    const scaleY = (containerHeight) / viewport.height;
                    
                    return Math.min(scaleX, scaleY) * 1.05;
                }
                
                function renderPage(num) {
                    pageRendering = true;
                    document.getElementById("page-num").textContent = num;
                    
                    const container = document.getElementById("pdf-container");
                    
                    pdfDoc.getPage(num).then(function(page) {
                        const viewport = page.getViewport({ scale: 1.0 });
                        
                        scale = calculateScale(viewport, container);
                        currentScale = scale;
                        
                        const scaledViewport = page.getViewport({ scale: scale });
                        
                        const canvas = document.getElementById("pdf-canvas");
                        const context = canvas.getContext("2d");
                        canvas.height = scaledViewport.height;
                        canvas.width = scaledViewport.width;
                        
                        const canvasContainer = document.getElementById("canvas-container");
                        canvasContainer.style.width = scaledViewport.width + 'px';
                        canvasContainer.style.margin = '0 auto';
                        
                        const renderContext = {
                            canvasContext: context,
                            viewport: scaledViewport
                        };
                        
                        const renderTask = page.render(renderContext);
                        
                        renderTask.promise.then(function() {
                            pageRendering = false;
                            if (pageNumPending !== null) {
                                renderPage(pageNumPending);
                                pageNumPending = null;
                            }
                        });
                    });
                }
                
                function queueRenderPage(num) {
                    if (pageRendering) {
                        pageNumPending = num;
                    } else {
                        renderPage(num);
                    }
                }
                
                function onPrevPage() {
                    if (pageNum <= 1) {
                        return;
                    }
                    pageNum--;
                    queueRenderPage(pageNum);
                }
                
                function onNextPage() {
                    if (pageNum >= pdfDoc.numPages) {
                        return;
                    }
                    pageNum++;
                    queueRenderPage(pageNum);
                }
                
                document.getElementById("prev-page").addEventListener("click", onPrevPage);
                document.getElementById("next-page").addEventListener("click", onNextPage);
                
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if (pdfDoc) {
                            renderPage(pageNum);
                        }
                    }, 250);
                });
                
                const loadingTask = pdfjsLib.getDocument({
                    url: '<?php echo $pdf_data_url; ?>',
                    cMapUrl: "https://unpkg.com/pdfjs-dist@3.4.120/cmaps/",
                    cMapPacked: true
                });
                
                loadingTask.promise.then(function(pdf) {
                    pdfDoc = pdf;
                    document.getElementById("page-count").textContent = pdf.numPages;
                    renderPage(1);
                }).catch(function(error) {
                    console.error("Error loading PDF:", error);
                    const container = document.querySelector("#pdf-viewer");
                    container.innerHTML = `
                        <div style="padding: 20px; text-align: center;">
                            <p>Error loading PDF. Please try opening in a new tab or check the file.</p>
                            <a href="<?php echo $moa_file_url; ?>" target="_blank" class="btn btn-primary">Open in New Tab</a>
                        </div>`;
                });
                </script>
            <?php else: ?>
                <div class="no-document-message" style="color:#800000; text-align:center; width:100%;">
                    <i class="fa fa-file-pdf-o" style="font-size:48px; margin-bottom:15px; display:block; color:#800000;"></i>
                    <p>No document uploaded yet</p>
                </div>
            <?php endif; ?>
        </div>
        
            <div class="moa-button-container" style="display: flex; flex-direction: column; gap: 12px; width: 100%; max-width: 295px; margin: 0 auto; margin-right: 140px;">
    <?php if ($has_moa): ?>
        <?php if ($renewal_status !== 'completed'): ?>
            <button type="button" id="uploadBtn" class="moa-upload-btn" onclick="document.getElementById('moa_file').click();"
                style="width: 100%; padding: 15px; background: #700000; border: 2px solid #700000; color: #fff; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; margin: 0;">
                EDIT
            </button>
        <?php else: ?>
            <button type="button" class="moa-upload-btn" disabled style="width: 100%; padding: 15px; background: #6c757d; border: 2px solid #6c757d; color: #fff; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: not-allowed; margin: 0;">
                EDIT
            </button>
        <?php endif; ?>

        <a href="view_moa.php?id=<?php echo $moa_data['id']; ?>" target="_blank" class="moa-view-btn" style="display: block; width: 100%; padding: 15px; background: #700000; border: 2px solid #700000; color: #fff !important; border-radius: 8px; font-size: 18px; font-weight: bold; text-align: center; text-decoration: none; cursor: pointer; margin: 0;">
            VIEW
        </a>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php if ($renewal_status !== 'completed'): ?>
                <button type="button" id="submitEvaluationBtn" class="moa-renew-btn moa-submit-btn" style="background-color: #0c0c9b; border-color: #0c0c9b; width: 100%; padding: 15px; font-size: 18px; font-weight: bold; border-radius: 8px;">SUBMIT</button>
            <?php else: ?>
                <button type="button" class="moa-renew-btn" disabled style="background-color: #6c757d; border-color: #6c757d; color: #fff; cursor: not-allowed; width: 100%; padding: 15px; font-size: 18px; font-weight: bold; border-radius: 8px;">
                    <span style="position: relative; z-index: 1;">SUBMITTED</span>
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <button type="button" id="uploadBtn" class="moa-upload-btn" onclick="document.getElementById('moa_file').click();"
            style="width: 100%; padding: 15px; background: #700000; border: 2px solid #700000; color: #fff; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; margin: 0;">Upload</button>
        <button class="moa-download-btn" disabled style="width: 100%; padding: 15px; background: #6c757d; border: 2px solid #6c757d; color: #fff; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: not-allowed; margin: 0; opacity: 1;">View</button>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <button type="button" class="moa-renew-btn" disabled style="background-color: #6c757d; border-color: #6c757d; color: #fff; cursor: not-allowed; width: 100%; padding: 15px; font-size: 18px; font-weight: bold; border-radius: 8px;">
                <span style="position: relative; z-index: 1;">SUBMIT</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if ($renewal_status === 'completed'): ?>
        <div id="successMessage" style="display: block; margin: 15px auto 0; width: 110%; max-width: 295px;">
            <div style="border: 2px solid #700000; border-radius: 8px; padding: 12px 15px; background-color: #f8f9fa; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #28a745;">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span style="color: #28a745; font-size: 14px; font-weight: 500; text-align:center;">You have successfully submitted the MOA renewal</span>
                    </div>
                    <div style="margin-top: 8px; text-align: center;">
                        <a href="moa_submission_process.php" style="color: #0d6efd; text-decoration: underline; font-size: 13px;">
                            Click here to view your submitted MOA process
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

            
            <form id="moaUploadForm" method="POST" enctype="multipart/form-data" style="display: none;">
                <input type="file" name="moa_file" id="moa_file" accept=".pdf">
            </form>
            
            <script>
            document.getElementById('submitEvaluationBtn')?.addEventListener('click', async function() {
                Swal.fire({
                    title: 'Submit MOA',
                    html: '<span class="swal-text-white">Are you sure you want to submit this MOA for verification?</span>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#700000',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-confirm-proceed',
                        cancelButton: 'swal-cancel-proceed'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('update_moa_status.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `supervisor_id=${encodeURIComponent(<?php echo json_encode($_SESSION['auth_user']['supervisor_id'] ?? ''); ?>)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'MOA submitted successfully!',
                                    icon: 'success',
                                    confirmButtonColor: '#700000',
                                    customClass: {
                                        popup: 'swal-custom-popup',
                                        confirmButton: 'swal-confirm-proceed'
                                    },
                                    didClose: () => {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                throw new Error(data.message || 'Failed to submit MOA');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to submit MOA: ' + error.message);
                        });
                    }
                });
            });
            </script>
            
            <script>
            document.getElementById('moa_file').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                const uploadBtn = document.getElementById('uploadBtn');
                uploadBtn.disabled = true;
                uploadBtn.classList.add('disabled');
                uploadBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Uploading...';
                
                const formData = new FormData();
                formData.append('moa_file', file);
                
                fetch('upload_moa.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const uploadBtn = document.getElementById('uploadBtn');
                        uploadBtn.disabled = true;
                        uploadBtn.classList.add('disabled');
                        uploadBtn.innerHTML = 'Uploaded';
                        const newUploadBtn = uploadBtn.cloneNode(true);
                        uploadBtn.parentNode.replaceChild(newUploadBtn, uploadBtn);
                        newUploadBtn.onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        };
                        
                    
                        Swal.fire({
                            title: 'Success!',
                            html: '<span class="swal-text-white">MOA uploaded successfully!</span>',
                            icon: 'success',
                            confirmButtonColor: '#700000',
                            background: '#700000',
                            customClass: {
                                popup: 'swal-custom-popup',
                                confirmButton: 'swal-confirm-proceed',
                                icon: 'swal2-icon swal2-success'
                            },
                            didClose: () => {
                                window.location.reload();
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Upload failed');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to upload file: ' + error.message,
                        icon: 'error',
                        confirmButtonColor: '#700000',
                        customClass: {
                            popup: 'swal-custom-popup',
                            confirmButton: 'swal-confirm-proceed'
                        }
                    });
                })
                .finally(() => {
                    if (!data || !data.success) {
                        uploadBtn.disabled = false;
                        uploadBtn.style.pointerEvents = 'auto';
                        uploadBtn.style.opacity = '1';
                        uploadBtn.style.cursor = 'pointer';
                        uploadBtn.innerHTML = originalBtnHTML;
                    }
                    document.getElementById('moa_file').value = '';
                    updateButtonStates(true);
                });
            });
            </script>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const viewBtn = document.querySelector('.moa-view-btn');
        if (viewBtn) {
            viewBtn.addEventListener('click', function() {
                <?php if ($has_moa): ?>
                window.open('<?php echo $moa_file_url; ?>', '_blank');
                <?php endif; ?>
            });
        }

        const submitBtn = document.querySelector('.moa-submit-btn');
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                <?php if ($has_moa): ?>
                Swal.fire({
                    title: 'Submit MOA',
                    html: '<span class="swal-text-white">Are you sure you want to submit this MOA for verification?</span>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#700000',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-confirm-proceed',
                        cancelButton: 'swal-cancel-proceed'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'moa_submission_process.php';
                    }
                });
                <?php endif; ?>
            });
        }

        function updateButtonStates(hasMoa) {
            const buttons = [
                document.querySelector('.moa-view-btn'),
                document.querySelector('.moa-submit-btn')
            ];
            
            buttons.forEach(btn => {
                if (btn) {
                    btn.disabled = !hasMoa;
                    btn.style.opacity = hasMoa ? '1' : '0.7';
                    btn.style.cursor = hasMoa ? 'pointer' : 'not-allowed';
                }
            });
        }

        updateButtonStates(<?php echo $has_moa ? 'true' : 'false'; ?>);

        document.getElementById('moa_file')?.addEventListener('change', function() {
            
        });
        const renewBtn = document.querySelector('.moa-renew-btn');
        if (renewBtn) {
            renewBtn.addEventListener('click', function () {
                Swal.fire({
                    title: 'RENEW',
                    html: `<span class="swal-text-white move-text-up">Are you sure you want to renew the MOA validation?</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#700000',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-custom-popup',
                        icon: 'swal-custom-icon',
                        title: 'title-color',
                        confirmButton: 'swal-confirm-proceed',
                        cancelButton: 'swal-cancel-proceed'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'renew_moa.php';
                    }
                });
            });
        }
        const submitBtn = document.querySelector('.moa-download-btn.moa-button');
        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                window.location.href = 'moa_submission_process.php';
            });
        }
    });
    </script>
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

