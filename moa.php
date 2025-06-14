<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id'])) {
    error_log("Unauthorized access attempt. Redirecting to ../pending/login.php");
    header('Location: ../pending/login.php');
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
    error_log("Relative path: " . $relative_path);
    error_log("Temporary file path: " . $_FILES["moaDocument"]["tmp_name"]);
    error_log("Upload error code: " . $_FILES["moaDocument"]["error"]);

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
        $_SESSION['alert'] = "Error";
        header("Location: moa.php");
        exit;
    }

    if ($_FILES["moaDocument"]["size"] > 10000000) {
        error_log("File size exceeds limit: " . $_FILES["moaDocument"]["size"]);
        $_SESSION['status'] = "File size exceeds 10MB limit.";
        $_SESSION['status-code'] = "error";
        $_SESSION['alert'] = "Error";
        header("Location: moa.php");
        exit;
    }

    if (file_exists($target_file)) {
        error_log("Existing file deleted: " . $target_file);
        unlink($target_file);
    }

    if (move_uploaded_file($_FILES["moaDocument"]["tmp_name"], $target_file)) {
        error_log("File successfully moved to: " . $target_file);
        try {
            $checkStmt = $conn->prepare("SELECT id, status FROM endorsement_documents WHERE student_id = ? AND document_type = 'moa'");
            $checkStmt->execute([$student_id]);
            $existing_document = $checkStmt->fetch(PDO::FETCH_ASSOC);

            $status = ($existing_document['status'] ?? 'denied') === 'accepted' ? 'pending' : 'pending';
            $sql = $existing_document ? 
                "UPDATE endorsement_documents 
                 SET document_name = ?, uploaded_path = ?, upload_date = NOW(), status = ?
                 WHERE id = ?" :
                "INSERT INTO endorsement_documents 
                 (student_id, document_name, document_type, uploaded_path, upload_date, status) 
                 VALUES (?, ?, 'moa', ?, NOW(), ?)";
            
            $stmt = $conn->prepare($sql);
            $params = $existing_document ? 
                [$file_name, $relative_path, $status, $existing_document['id']] : 
                [$student_id, $file_name, $relative_path, $status];
            
            if ($stmt->execute($params)) {
                error_log("Database updated successfully. Path: " . $relative_path);
                $_SESSION['status'] = "File uploaded successfully. Awaiting review.";
                $_SESSION['status-code'] = "success";
                $_SESSION['alert'] = "Success";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['status'] = "System error. Contact support.";
            $_SESSION['status-code'] = "error";
            $_SESSION['alert'] = "Error";
        }
    } else {
        error_log("File move failed. Error: " . $_FILES["moaDocument"]["error"]);
        $_SESSION['status'] = "Upload failed. Error: " . $_FILES["moaDocument"]["error"];
        $_SESSION['status-code'] = "error";
        $_SESSION['alert'] = "Error";
    }
    
    header("Location: moa.php");
    exit;
}

try {
    $stmt = $conn->prepare("SELECT *, DATE_FORMAT(upload_date, '%M %d, %Y %H:%i') AS formatted_date 
                           FROM endorsement_documents 
                           WHERE student_id = ? AND document_type = 'moa'");
    $stmt->execute([$_SESSION['auth_user']['student_id']]);
    $existing_document = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $existing_document = null;
}

if ($existing_document) {
    $status_msg = [
        'accepted' => "Your Memorandum of Agreement (MOA) has been approved.",
        'denied' => "Your MOA was denied. Please review the feedback and submit a revised version.",
        'pending' => "Your Memorandum of Agreement has been submitted and Under review."
    ];
    $document_status = $status_msg[$existing_document['status'] ?? 'pending'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Student Profile</title>
    <link rel="shortcut icon" href="images/Picture1.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="endorsement-css/endorsement-moa.css" rel="stylesheet">
    <style>
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
        .remarks-notification {
            position: relative;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #000;
            background: none;
            border-radius: 4px;
            font-size: 14px;
            color: #dc3545;
            display: none;
            max-width: 300px;
        }
        .remarks-notification .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-size: 18px;
            color: #333;
        }
        .remarks-notification .close-btn:hover {
            color: #000;
        }
        .status-notification {
            margin-top: 0px;
            padding: 10px;
            max-width: 300px;
        }
        .notifs-container {
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <?php require_once 'templates/stud_navbar.php'; ?>

    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto; position: relative;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div>
                <div>
                    <a href="endorsement.php" class="back-button">
                        <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                        Back
                    </a>
                </div>

                <div>
                    <h1 class="moa-title">Memorandum of Agreement Document Template</h1>
                </div>

                <div class="template-box">
                    <a href="templates/endorsement/New_MOA_Template.docx" download="New_MOA_Template.docx" class="btn1-download-template"><img src="images/doc.png" alt="" class="doc-icon"><u>New_MOA_Template.docx</u></a>
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
                            <?php if ($existing_document): ?>
                                <?php
                                $file_ext = strtolower(pathinfo($existing_document['uploaded_path'], PATHINFO_EXTENSION));
                                $cache_buster = '?t=' . time();
                                if ($file_ext == 'pdf'):
                                ?>
                                    <iframe id="documentIframe" src="<?php echo $existing_document['uploaded_path'] . $cache_buster; ?>" frameborder="0"></iframe>
                                <?php else: ?>
                                    <div id="placeholderText">Preview not available for this file type.</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div id="placeholderText">No uploads to show</div>
                                <img id="documentImage" class="hidden">
                            <?php endif; ?>
                        </div>

                        <div class="action-buttons">
                            <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
                                <input type="file" id="fileInput" name="moaDocument" accept=".pdf,.doc,.docx" class="hidden">

                                <?php if ($existing_document): ?>
                                    <a href="<?php echo $existing_document['uploaded_path']; ?>" download class="btn btn-download action" id="downloadBtn">
                                        <i class="fa fa-download btn-icon"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-actions action" id="viewBtn">
                                        <i class="fa fa-eye btn-icon"></i> View
                                    </button>
                                    <button type="button" class="btn btn-actions action" id="printBtn">
                                        <i class="fa fa-print btn-icon"></i> Print
                                    </button>
                                    <button type="submit" name="submit" class="btn btn-submit action disabled" id="submitBtn" disabled>
                                        <i class="fa fa-paper-plane btn-icon"></i> Submit
                                    </button>

                                    <div class="notifs-container">
                                        <?php if ($existing_document['status'] === 'denied' && !empty($existing_document['remarks'])): ?>
                                            <div class="remarks-notification" id="remarksNotification" style="display: block;">
                                                <span class="close-btn" id="closeRemarks">×</span>
                                                <strong>Remarks:</strong> <?php echo htmlspecialchars($existing_document['remarks']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($document_status)): ?>
                                            <div class="status-notification <?php echo $existing_document['status']; ?>" id="statusNotification" style="display: block;">
                                                <span class="close-btn" id="closeStatus">×</span>
                                                <?php echo $document_status; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <button type="button" class="btn btn-upload action" id="uploadBtn">
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
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/customAlert.js?t=<?php echo time(); ?>"></script>
    <script src="js/errorAlert.js?t=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Checking alert functions: ", {
                showSuccessAlert: typeof showSuccessAlert === 'function',
                showErrorAlert: typeof showErrorAlert === 'function',
                showConfirmAlert: typeof showConfirmAlert === 'function'
            });

            const fileInput = document.getElementById('fileInput');
            const viewBtn = document.getElementById('viewBtn');
            const editBtn = document.getElementById('editBtn');
            const printBtn = document.getElementById('printBtn');
            const documentImage = document.getElementById('documentImage');
            const placeholderText = document.getElementById('placeholderText');
            const uploadForm = document.getElementById('uploadForm');
            const uploadBtn = document.getElementById('uploadBtn');
            const documentArea = document.getElementById('documentArea');
            const closeStatusBtn = document.getElementById('closeStatus');
            const closeRemarksBtn = document.getElementById('closeRemarks');
            const overlay = document.getElementById('overlay');

            const fileAlreadySubmitted = <?php echo isset($existing_document) && $existing_document ? 'true' : 'false'; ?>;
            const documentStatus = '<?php echo isset($existing_document["status"]) ? $existing_document["status"] : ""; ?>';

            if (closeStatusBtn) {
                closeStatusBtn.addEventListener('click', function() {
                    const statusNotification = document.getElementById('statusNotification');
                    if (statusNotification) {
                        statusNotification.style.display = 'none';
                    }
                });
            }

            if (closeRemarksBtn) {
                closeRemarksBtn.addEventListener('click', function() {
                    const remarksNotification = document.getElementById('remarksNotification');
                    if (remarksNotification) {
                        remarksNotification.style.display = 'none';
                    }
                });
            }

            if (uploadBtn) {
                uploadBtn.addEventListener('click', function() {
                    console.log('Upload button clicked');
                    fileInput.click();
                });
            }

            fileInput.addEventListener('change', function(event) {
                console.log('File input changed');
                if (event.target.files.length > 0) {
                    const file = event.target.files[0];
                    console.log('Selected file: ' + file.name);

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
                        submitBtn.className = 'btn btn-submit action';
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
                            console.log('PDF preview loaded');
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

            function clearPreviewArea() {
                while (documentArea.firstChild) {
                    documentArea.removeChild(documentArea.firstChild);
                }
            }

            viewBtn.addEventListener('click', function() {
                console.log('View button clicked');
                <?php if ($existing_document): ?>
                    const fileExt = '<?php echo $file_ext; ?>';
                    if (fileExt === 'pdf') {
                        console.log('Toggling PDF preview');
                        documentArea.classList.toggle('enlarged');
                        overlay.classList.toggle('active');
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert for non-PDF view");
                            showErrorAlert("Error", "Preview not available for DOC/DOCX files.");
                        } else {
                            console.warn("showErrorAlert is not defined. Falling back to native alert.");
                            alert("Error: Preview not available for DOC/DOCX files.");
                        }
                    }
                <?php else: ?>
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        const fileExt = file.name.split('.').pop().toLowerCase();
                        console.log("Unsubmitted file extension: " + fileExt);
                        if (file.type === 'application/pdf') {
                            console.log('Toggling PDF preview for new file');
                            documentArea.classList.toggle('enlarged');
                            overlay.classList.toggle('active');
                        } else {
                            if (typeof showErrorAlert === 'function') {
                                console.log("Calling showErrorAlert for non-PDF view");
                                showErrorAlert("Error", "Preview not available for DOC/DOCX files.");
                            } else {
                                console.warn("showErrorAlert is not defined. Falling back to native alert.");
                                alert("Error: Preview not available for DOC/DOCX files.");
                            }
                        }
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert for no file view");
                            showErrorAlert("Error", "Please upload a file first to view it.");
                        } else {
                            console.warn("showErrorAlert is not defined. Falling back to native alert.");
                            alert("Error: Please upload a file first to view it.");
                        }
                    }
                <?php endif; ?>
            });

            overlay.addEventListener('click', function() {
                console.log('Overlay clicked');
                documentArea.classList.remove('enlarged');
                overlay.classList.remove('active');
            });

            if (editBtn) {
                editBtn.addEventListener('click', function() {
                    console.log('Edit button clicked');
                    if (documentStatus === 'accepted') {
                        if (typeof showConfirmAlert === 'function') {
                            console.log("Calling showConfirmAlert for edit");
                            showConfirmAlert({
                                title: "Warning",
                                text: "This document has been accepted. Editing requires resubmission. Continue?",
                                confirmButtonText: "Yes, edit file",
                                showCancelButton: true
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    fileInput.click();
                                }
                            });
                        } else {
                            console.warn("showConfirmAlert is not defined. Falling back to native confirm.");
                            if (confirm("This document has been accepted. Editing requires resubmission. Continue?")) {
                                fileInput.click();
                            }
                        }
                    } else {
                        fileInput.click();
                    }
                });
            }

            printBtn.addEventListener('click', function() {
                console.log('Print button clicked');
                <?php if ($existing_document): ?>
                    const filePath = "<?php echo $existing_document['uploaded_path']; ?>";
                    const fileExt = filePath.split('.').pop().toLowerCase();

                    if (fileExt === 'pdf') {
                        console.log('Opening PDF for printing');
                        const printWindow = window.open(filePath, '_blank');
                        printWindow.addEventListener('load', function() {
                            setTimeout(function() {
                                printWindow.print();
                            }, 500);
                        });
                    } else {
                        console.log('Opening non-PDF file');
                        window.open(filePath, '_blank');
                    }
                <?php else: ?>
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        if (file.type === 'application/pdf') {
                            console.log('Printing new PDF file');
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const printWindow = window.open('', '_blank');
                                printWindow.document.write(`
                                    <!DOCTYPE html>
                                    <html>
                                    <head>
                                        <title>Print Document</title>
                                        <style>
                                            body, html { margin: 0; padding: 0; height: 100%; }
                                            iframe { width: 100%; height: 100%; }
                                        </style>
                                    </head>
                                    <body>
                                        <iframe src="${e.target.result}" frameborder="0"></iframe>
                                    </body>
                                    </html>
                                `);
                                printWindow.document.close();
                                printWindow.focus();
                                setTimeout(function() {
                                    printWindow.print();
                                }, 500);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            if (typeof showErrorAlert === 'function') {
                                console.log("Calling showErrorAlert for non-PDF print");
                                showErrorAlert("Error", "Cannot print this file type directly.");
                            } else {
                                console.warn("showErrorAlert is not defined. Falling back to native alert.");
                                alert("Error: Cannot print this file type directly.");
                            }
                        }
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert for no file print");
                            showErrorAlert("Error", "Please upload a file first to print it.");
                        } else {
                            console.warn("showErrorAlert is not defined. Falling back to native alert.");
                            alert("Error: Please upload a file first to print it.");
                        }
                    }
                <?php endif; ?>
            });

            if (fileAlreadySubmitted) {
                viewBtn.removeAttribute('disabled');
                viewBtn.classList.remove('btn-disabled');
                viewBtn.classList.add('btn-actions');

                printBtn.removeAttribute('disabled');
                printBtn.classList.remove('btn-disabled');
                printBtn.classList.add('btn-actions');

                const downloadBtn = document.getElementById('downloadBtn');
                if (downloadBtn) {
                    downloadBtn.classList.add('btn-actions');
                }

                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.classList.add('disabled');
                }
            }
        });
    </script>

    <?php
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log("Alert script running. Status code: <?php echo $_SESSION['status-code']; ?>");
                try {
                    const title = "<?php echo isset($_SESSION['alert']) ? addslashes($_SESSION['alert']) : ($_SESSION['status-code'] === 'success' ? 'Success' : 'Error'); ?>";
                    const message = "<?php echo addslashes($_SESSION['status']); ?>";
                    console.log("Alert details: title=" + title + ", message=" + message);
                    if ("<?php echo $_SESSION['status-code']; ?>" === "success") {
                        if (typeof showSuccessAlert === 'function') {
                            console.log("Calling showSuccessAlert with title: " + title + ", message=" + message);
                            showSuccessAlert(title, message);
                        } else {
                            console.warn("showSuccessAlert is not defined. Falling back to native alert.");
                            alert("Success: " + message);
                        }
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert with title: " + title + ", message=" + message);
                            showErrorAlert(title, message);
                        } else {
                            console.warn("showErrorAlert is not defined. Falling back to native alert.");
                            alert("Error: " + message);
                        }
                    }
                } catch (e) {
                    console.error("Error displaying alert: ", e);
                    alert("An error occurred: <?php echo addslashes($_SESSION['status']); ?>");
                }
            });
        </script>
    <?php
        unset($_SESSION['status']);
        unset($_SESSION['status-code']);
        unset($_SESSION['alert']);
    }
    ?>
</body>
</html>