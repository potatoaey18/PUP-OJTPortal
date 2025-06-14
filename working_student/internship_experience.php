<?php
include '../../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id'])) {
    header('Location: index.php');
    exit;
}

$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/PUP/student/uploads/internship_experience/";
$relative_dir = "/PUP/student/uploads/internship_experience/";

if (isset($_POST['submit'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
        error_log("Created directory: " . $target_dir);
    }

    $original_name = basename($_FILES["internship_experienceDocument"]["name"]);
    // Remove sanitization: Use the original file name as-is
    $file_name = $original_name;
    $target_file = $target_dir . $student_id . "_" . $file_name;
    $relative_path = $relative_dir . $student_id . "_" . $file_name;

    // Debugging: Log the file paths and upload details
    error_log("Original file name: " . $original_name);
    error_log("Target file path: " . $target_file);
    error_log("Relative path: " . $relative_path);
    error_log("Temporary file path: " . $_FILES["internship_experienceDocument"]["tmp_name"]);
    error_log("Upload error code: " . $_FILES["internship_experienceDocument"]["error"]);

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES["internship_experienceDocument"]["tmp_name"]);
    $allowed_mimes = [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    if (!in_array($mime, $allowed_mimes)) {
        error_log("Invalid MIME type: " . $mime);
        $_SESSION['status'] = "Invalid file type. Only PDF, DOC, and DOCX are allowed.";
        $_SESSION['status-code'] = "error";
        header("Location: internship_experience.php");
        exit;
    }

    if ($_FILES["internship_experienceDocument"]["size"] > 10000000) {
        error_log("File size exceeds limit: " . $_FILES["internship_experienceDocument"]["size"]);
        $_SESSION['status'] = "File size exceeds 10MB limit.";
        $_SESSION['status-code'] = "error";
        header("Location: internship_experience.php");
        exit;
    }

    if (file_exists($target_file)) {
        error_log("Existing file deleted: " . $target_file);
        unlink($target_file);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["internship_experienceDocument"]["tmp_name"], $target_file)) {
        error_log("File successfully moved to: " . $target_file);
        try {
            $status = ($existing_document['status'] ?? 'denied') === 'accepted' ? 'pending' : 'pending';
            $sql = $existing_document ? 
                "UPDATE endorsement_documents 
                 SET document_name = ?, uploaded_path = ?, upload_date = NOW(), status = ?
                 WHERE id = ?" :
                "INSERT INTO endorsement_documents 
                 (student_id, document_name, document_type, uploaded_path, upload_date, status) 
                 VALUES (?, ?, 'internship_experience', ?, NOW(), ?)";
            
            $stmt = $conn->prepare($sql);
            $params = $existing_document ? 
                [$file_name, $relative_path, $status, $existing_document['id']] : 
                [$student_id, $file_name, $relative_path, $status];
            
            if ($stmt->execute($params)) {
                error_log("Database updated successfully. Path: " . $relative_path);
                $_SESSION['status'] = "File uploaded successfully. Awaiting review.";
                $_SESSION['status-code'] = "success";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['status'] = "System error. Contact support.";
            $_SESSION['status-code'] = "error";
        }
    } else {
        error_log("File move failed. Error: " . $_FILES["internship_experienceDocument"]["error"]);
        $_SESSION['status'] = "Upload failed. Error: " . $_FILES["internship_experienceDocument"]["error"];
        $_SESSION['status-code'] = "error";
    }
    
    header("Location: internship_experience.php");
    exit;
}

try {
    $stmt = $conn->prepare("SELECT *, DATE_FORMAT(upload_date, '%M %d, %Y %H:%i') AS formatted_date 
                           FROM endorsement_documents 
                           WHERE student_id = ? AND document_type = 'internship_experience'");
    $stmt->execute([$_SESSION['auth_user']['student_id']]);
    $existing_document = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $existing_document = null;
}

if ($existing_document) {
    $status_msg = [
        'accepted' => "Your Internship Experience has been approved.",
        'denied' => "Your MOA was denied. Please review the feedback and submit a revised version.",
        'pending' => "Your Internship Experience has been submitted and Under review."
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
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
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
            overflow-y: auto; /* Vertical scrollbar only */
            overflow-x: hidden; /* No horizontal scrollbar */
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
    </style>
</head>
<body>
    <?php require_once 'templates/stud_navbar.php'; ?>

    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto; position: relative;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div>
                <div>
                    <a href="documentation.php" class="back-button">
                        <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                        Back
                    </a>
                </div>

                <div>
                    <h1 class="title">Internship Experience</h1>
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
                                <input type="file" id="fileInput" name="internship_experienceDocument" accept=".pdf,.doc,.docx" class="hidden">

                                <?php if ($existing_document): ?>
                                    <!-- After Submission -->
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

                                    <?php if (!empty($document_status)): ?>
                                        <div class="status-notification <?php echo $status_class ?? ''; ?>" id="statusNotification" style="display: block;">
                                            <span class="close-btn" id="closeStatus">×</span>
                                            <?php echo $document_status; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!-- Before Upload -->
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
            const closeStatusBtn = document.getElementById('closeStatus');
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

            if (uploadBtn) {
                uploadBtn.addEventListener('click', function() {
                    fileInput.click();
                });
            }

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
                <?php if ($existing_document): ?>
                    const fileExt = '<?php echo $file_ext; ?>';
                    if (fileExt === 'pdf') {
                        // Toggle the enlarged state of the document area
                        documentArea.classList.toggle('enlarged');
                        overlay.classList.toggle('active');
                    } else {
                        sweetAlert("Notice", "Preview not available for DOC/DOCX files.", "info");
                    }
                <?php else: ?>
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        const fileExt = file.name.split('.').pop().toLowerCase();
                        console.log("Unsubmitted file extension: " + fileExt);
                        if (file.type === 'application/pdf') {
                            // Toggle the enlarged state of the document area
                            documentArea.classList.toggle('enlarged');
                            overlay.classList.toggle('active');
                        } else {
                            sweetAlert("Notice", "Preview not available for DOC/DOCX files.", "info");
                        }
                    } else {
                        sweetAlert("Notice", "Please upload a file first to view it.", "info");
                    }
                <?php endif; ?>
            });

            // Close the enlarged view when clicking the overlay
            overlay.addEventListener('click', function() {
                documentArea.classList.remove('enlarged');
                overlay.classList.remove('active');
            });

            if (editBtn) {
                editBtn.addEventListener('click', function() {
                    if (documentStatus === 'accepted') {
                        sweetAlert({
                            title: "Warning",
                            text: "This document has been accepted. Editing requires resubmission. Continue?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, edit file",
                            closeOnConfirm: true
                        }, function(isConfirm) {
                            if (isConfirm) {
                                fileInput.click();
                            }
                        });
                    } else {
                        fileInput.click();
                    }
                });
            }

            printBtn.addEventListener('click', function() {
                <?php if ($existing_document): ?>
                    const filePath = "<?php echo $existing_document['uploaded_path']; ?>";
                    const fileExt = filePath.split('.').pop().toLowerCase();

                    if (fileExt === 'pdf') {
                        const printWindow = window.open(filePath, '_blank');
                        printWindow.addEventListener('load', function() {
                            setTimeout(function() {
                                printWindow.print();
                            }, 500);
                        });
                    } else {
                        window.open(filePath, '_blank');
                    }
                <?php else: ?>
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        if (file.type === 'application/pdf') {
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
                            sweetAlert("Notice", "Cannot print this file type directly.", "info");
                        }
                    } else {
                        sweetAlert("Notice", "Please upload a file first to print it.", "info");
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
            sweetAlert("<?php echo $_SESSION['alert'] ?? 'Notice'; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
        </script>
    <?php
        unset($_SESSION['status']);
    }
    ?>
</body>
</html>