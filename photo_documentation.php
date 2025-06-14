<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id']) || empty($_SESSION['auth_user']['student_id'])) {
    error_log('[Photo Documentation] Unauthorized access attempt');
    header('Location: ../pending/login.php');
    exit;
}

class PhotoDocumentationHandler {
    private $conn;
    private $student_id;
    private $target_dir;
    private $relative_dir;
    private $allowed_mimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/jpg'
    ];
    private $max_file_size = 10000000; // 10MB

    public function __construct($conn, $student_id) {
        $this->conn = $conn;
        $this->student_id = $student_id;
        $this->target_dir = $_SERVER['DOCUMENT_ROOT'] . "/PUP/student/uploads/photo_documentation/";
        $this->relative_dir = "/PUP/student/uploads/photo_documentation/";
    }

    public function handleUpload() {
        if (!isset($_POST['submit'])) {
            return;
        }

        header('Content-Type: application/json');

        if (!file_exists($this->target_dir)) {
            if (!mkdir($this->target_dir, 0755, true)) {
                error_log('[Photo Documentation] Failed to create upload directory: ' . $this->target_dir);
                echo json_encode([
                    'status' => 'Unable to create upload directory.',
                    'status_code' => 'error',
                    'alert' => 'Error'
                ]);
                exit;
            }
            error_log('[Photo Documentation] Created directory: ' . $this->target_dir);
        }

        if (!isset($_FILES['photo_documentationDocument']) || $_FILES['photo_documentationDocument']['error'] !== UPLOAD_ERR_OK) {
            error_log('[Photo Documentation] File upload error: ' . ($_FILES['photo_documentationDocument']['error'] ?? 'No file uploaded'));
            echo json_encode([
                'status' => 'File upload failed. Please try again.',
                'status_code' => 'error',
                'alert' => 'Error'
            ]);
            exit;
        }

        $file = $_FILES['photo_documentationDocument'];
        $original_name = basename($file['name']);
        $file_name = $original_name;
        $target_file = $this->target_dir . $this->student_id . "_" . $file_name;
        $relative_path = $this->relative_dir . $this->student_id . "_" . $file_name;

        $this->logUploadDetails($original_name, $target_file, $relative_path, $file);

        if (!$this->validateFile($file, $target_file)) {
            return;
        }

        if ($this->moveAndProcessFile($file, $target_file, $file_name, $relative_path)) {
            echo json_encode([
                'status' => 'Photo Documentation uploaded successfully. Awaiting review.',
                'status_code' => 'success',
                'alert' => 'Success'
            ]);
        } else {
            echo json_encode([
                'status' => 'Upload failed. Error: ' . $file['error'],
                'status_code' => 'error',
                'alert' => 'Error'
            ]);
        }
        exit;
    }

    private function logUploadDetails($original_name, $target_file, $relative_path, $file) {
        error_log('[Photo Documentation] Original file name: ' . $original_name);
        error_log('[Photo Documentation] Target file path: ' . $target_file);
        error_log('[Photo Documentation] Relative path: ' . $relative_path);
        error_log('[Photo Documentation] Temporary file path: ' . $file['tmp_name']);
        error_log('[Photo Documentation] Upload error code: ' . $file['error']);
    }

    private function validateFile($file, $target_file) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowed_mimes)) {
            error_log('[Photo Documentation] Invalid MIME type: ' . $mime);
            echo json_encode([
                'status' => 'Invalid file type. Only JPG, JPEG, PNG, PDF, DOC, and DOCX are allowed.',
                'status_code' => 'error',
                'alert' => 'Error'
            ]);
            exit;
        }

        if ($file['size'] > $this->max_file_size) {
            error_log('[Photo Documentation] File size exceeds limit: ' . $file['size']);
            echo json_encode([
                'status' => 'File size exceeds 10MB limit.',
                'status_code' => 'error',
                'alert' => 'Error'
            ]);
            exit;
        }

        if (file_exists($target_file)) {
            error_log('[Photo Documentation] Existing file deleted: ' . $target_file);
            unlink($target_file);
        }

        return true;
    }

    private function moveAndProcessFile($file, $target_file, $file_name, $relative_path) {
        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            error_log('[Photo Documentation] File move failed. Error: ' . $file['error']);
            return false;
        }

        error_log('[Photo Documentation] File successfully moved to: ' . $target_file);
        return $this->updateDatabase($file_name, $relative_path);
    }

    private function updateDatabase($file_name, $relative_path) {
        try {
            $existing_document = $this->getExistingDocument();
            $status = ($existing_document && $existing_document['status'] === 'accepted') ? 'pending' : 'pending';

            $sql = $existing_document ?
                "UPDATE photo_documentation 
                 SET document_name = ?, uploaded_path = ?, upload_date = NOW(), status = ?, updated_at = NOW()
                 WHERE id = ?" :
                "INSERT INTO photo_documentation 
                 (student_id, document_name, document_type, uploaded_path, upload_date, status, created_at, updated_at) 
                 VALUES (?, ?, 'photo_documentation', ?, NOW(), ?, NOW(), NOW())";

            $stmt = $this->conn->prepare($sql);
            $params = $existing_document ?
                [$file_name, $relative_path, $status, $existing_document['id']] :
                [$this->student_id, $file_name, $relative_path, $status];

            if ($stmt->execute($params)) {
                error_log('[Photo Documentation] Database updated successfully. Path: ' . $relative_path);
                return true;
            } else {
                error_log('[Photo Documentation] Database update failed.');
                return false;
            }
        } catch (PDOException $e) {
            error_log('[Photo Documentation] Database error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'System error. Contact support.',
                'status_code' => 'error',
                'alert' => 'Error'
            ]);
            exit;
        }
    }

    public function getExistingDocument() {
        try {
            $stmt = $this->conn->prepare("SELECT *, DATE_FORMAT(upload_date, '%M %d, %Y %H:%i') AS formatted_date 
                                         FROM photo_documentation 
                                         WHERE student_id = ? AND document_type = 'photo_documentation'");
            $stmt->execute([$this->student_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('[Photo Documentation] Database error: ' . $e->getMessage());
            return null;
        }
    }
}

// Initialize handler and process upload
$handler = new PhotoDocumentationHandler($conn, $_SESSION['auth_user']['student_id']);
$handler->handleUpload();
$existing_document = $handler->getExistingDocument();

$status_msg = [
    'accepted' => "Your Photo Documentation has been approved.",
    'denied' => "Your Photo Documentation was denied. Please review the feedback and submit a revised version.",
    'pending' => "Your Photo Documentation has been submitted and is under review."
];
$document_status = $existing_document ? $status_msg[$existing_document['status'] ?? 'pending'] : '';
$status_class = [
    'accepted' => 'success',
    'denied' => 'error',
    'pending' => 'info'
][$existing_document['status'] ?? 'pending'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Photo Documentation</title>
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
        .document-area img, .document-area iframe {
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
        .moa-title {
            font-size: 16px;
            font-weight: bold;
        }
        .template-box {
            margin-bottom: 2rem;
        }
        .status-notification.success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            padding: 10px;
            margin-top: 10px;
        }
        .status-notification.error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
            padding: 10px;
            margin-top: 10px;
        }
        .status-notification.info {
            background-color: #d9edf7;
            color: #31708f;
            border: 1px solid #bce8f1;
            padding: 10px;
            margin-top: 10px;
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
                    <a href="documentation.php" class="back-button">
                        <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                        Back
                    </a>
                </div>

                <div>
                    <h1 class="moa-title">Photo Documentation Submission</h1>
                </div>

                <div class="template-box">
                    <a href="templates/photo_documentation/photo_documentation.docx" download="photo_documentation.docx" class="btn1-download-template">
                        <img src="images/doc.png" alt="" class="doc-icon"><u>Photo_Documentation_Template.docx</u>
                    </a>
                    <a href="templates/photo_documentation/photo_documentation.docx" download="photo_documentation.docx" class="btn2-download-template btn-download">
                        <i class="fa fa-download"></i>
                    </a>
                </div>

                <div>
                    <h1 class="title">Photo Documentation</h1>
                </div>

                <div class="box">
                    <div class="content">
                        <div class="document-area" id="documentArea">
                            <?php if ($existing_document && !empty($existing_document['uploaded_path'])): ?>
                                <?php
                                $file_ext = strtolower(pathinfo($existing_document['uploaded_path'], PATHINFO_EXTENSION));
                                $cache_buster = '?t=' . time();
                                if (in_array($file_ext, ['jpg', 'jpeg', 'png'])):
                                ?>
                                    <img id="documentImage" src="<?php echo htmlspecialchars($existing_document['uploaded_path']) . $cache_buster; ?>" alt="Photo Documentation Image">
                                <?php elseif ($file_ext == 'pdf'): ?>
                                    <iframe id="documentIframe" src="<?php echo htmlspecialchars($existing_document['uploaded_path']) . $cache_buster; ?>" frameborder="0"></iframe>
                                <?php else: ?>
                                    <div id="placeholderText">Preview not available for this file type.</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div id="placeholderText">No uploads to show</div>
                                <img id="documentImage" class="hidden">
                            <?php endif; ?>
                        </div>

                        <div class="action-buttons">
                            <form id="uploadForm" enctype="multipart-form-data">
                                <input type="file" id="fileInput" name="photo_documentationDocument" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden">

                                <?php if ($existing_document && !empty($existing_document['uploaded_path'])): ?>
                                    <a href="<?php echo htmlspecialchars($existing_document['uploaded_path']); ?>" download class="btn btn-download action" id="downloadBtn">
                                        <i class="fa fa-download btn-icon"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-actions action" id="viewBtn">
                                        <i class="fa fa-eye btn-icon"></i> View
                                    </button>
                                    <button type="button" class="btn btn-actions action" id="printBtn">
                                        <i class="fa fa-print btn-icon"></i> Print
                                    </button>
                                    <button type="button" class="btn btn-submit action disabled" id="submitBtn" disabled>
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
                                            <div class="status-notification <?php echo htmlspecialchars($status_class); ?>" id="statusNotification" style="display: block;">
                                                <span class="close-btn" id="closeStatus">×</span>
                                                <?php echo htmlspecialchars($document_status); ?>
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

            const fileAlreadySubmitted = <?php echo isset($existing_document) && !empty($existing_document['uploaded_path']) ? 'true' : 'false'; ?>;
            const documentStatus = '<?php echo isset($existing_document['status']) ? htmlspecialchars($existing_document['status']) : ''; ?>';

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
                        submitBtn.type = 'button';
                        submitBtn.innerHTML = '<i class="fa fa-paper-plane btn-icon"></i> Submit';
                        uploadForm.appendChild(submitBtn);
                    }
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('disabled');

                    if (file.type.startsWith('image/')) {
                        clearPreviewArea();
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            documentImage.src = e.target.result;
                            documentImage.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
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

                    submitBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Submit button clicked');
                        const formData = new FormData(uploadForm);
                        formData.append('submit', 'true');

                        fetch('', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Fetch response:', data);
                            if (data.status && data.status_code) {
                                if (data.status_code === 'success') {
                                    if (typeof showSuccessAlert === 'function') {
                                        console.log("Calling showSuccessAlert with title: " + data.alert + ", message: " + data.status);
                                        showSuccessAlert(data.alert, data.status);
                                    } else {
                                        console.warn("showSuccessAlert is not defined. Falling back to native alert.");
                                        alert("Success: " + data.status);
                                    }
                                } else {
                                    if (typeof showErrorAlert === 'function') {
                                        console.log("Calling showErrorAlert with title: " + data.alert + ", message: " + data.status);
                                        showErrorAlert(data.alert, data.status);
                                    } else {
                                        console.warn("showErrorAlert is not defined. Falling back to native alert.");
                                        alert("Error: " + data.status);
                                    }
                                }
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                if (typeof showErrorAlert === 'function') {
                                    console.log("Calling showErrorAlert for unexpected response");
                                    showErrorAlert('Error', 'Unexpected response from server.');
                                } else {
                                    console.warn("showErrorAlert is not defined. Falling back to native alert.");
                                    alert("Error: Unexpected response from server.");
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            if (typeof showErrorAlert === 'function') {
                                console.log("Calling showErrorAlert for fetch error");
                                showErrorAlert('Error', 'Failed to upload file. Please try again.');
                            } else {
                                console.warn("showErrorAlert is not defined. Falling back to native alert.");
                                alert("Error: Failed to upload file. Please try again.");
                            }
                        });
                    }, { once: true });
                }
            });

            function clearPreviewArea() {
                while (documentArea.firstChild) {
                    documentArea.removeChild(documentArea.firstChild);
                }
            }

            viewBtn.addEventListener('click', function() {
                console.log('View button clicked');
                <?php if ($existing_document && !empty($existing_document['uploaded_path'])): ?>
                    const fileExt = '<?php echo htmlspecialchars($file_ext); ?>';
                    if (fileExt === 'pdf' || fileExt === 'jpg' || fileExt === 'jpeg' || fileExt === 'png') {
                        console.log('Toggling preview');
                        documentArea.classList.toggle('enlarged');
                        overlay.classList.toggle('active');
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert for non-previewable file");
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
                        if (file.type === 'application/pdf' || file.type.startsWith('image/')) {
                            console.log('Toggling preview for new file');
                            documentArea.classList.toggle('enlarged');
                            overlay.classList.toggle('active');
                        } else {
                            if (typeof showErrorAlert === 'function') {
                                console.log("Calling showErrorAlert for non-previewable file");
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
                                text: "This Photo Documentation has been accepted. Editing requires resubmission. Continue?",
                                confirmButtonText: "Yes, edit file",
                                showCancelButton: true
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    fileInput.click();
                                }
                            });
                        } else {
                            console.warn("showConfirmAlert is not defined. Falling back to native confirm.");
                            if (confirm("This Photo Documentation has been accepted. Editing requires resubmission. Continue?")) {
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
                <?php if ($existing_document && !empty($existing_document['uploaded_path'])): ?>
                    const filePath = "<?php echo htmlspecialchars($existing_document['uploaded_path']); ?>";
                    const fileExt = filePath.split('.').pop().toLowerCase();

                    if (fileExt === 'pdf') {
                        console.log('Opening PDF for printing');
                        const printWindow = window.open(filePath + '?t=<?php echo time(); ?>', '_blank');
                        printWindow.addEventListener('load', function() {
                            setTimeout(function() {
                                printWindow.print();
                            }, 500);
                        });
                    } else if (fileExt === 'jpg' || fileExt === 'jpeg' || fileExt === 'png') {
                        console.log('Opening image for printing');
                        const printWindow = window.open('', '_blank');
                        printWindow.document.write(`
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>Print Photo Documentation</title>
                                <style>
                                    body, html { margin: 0; padding: 0; height: 100%; }
                                    img { width: 100%; height: 100%; }
                                </style>
                            </head>
                            <body>
                                <img src="${filePath}?t=<?php echo time(); ?>" frameborder="0">
                            </body>
                            </html>
                        `);
                        printWindow.document.close();
                        printWindow.focus();
                        setTimeout(function() {
                            printWindow.print();
                        }, 500);
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert for non-printable file");
                            showErrorAlert("Error", "Cannot print DOC/DOCX files directly.");
                        } else {
                            console.warn("showErrorAlert is not defined. Falling back to native alert.");
                            alert("Error: Cannot print DOC/DOCX files directly.");
                        }
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
                                        <title>Print Photo Documentation</title>
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
                        } else if (file.type.startsWith('image/')) {
                            console.log('Printing new image file');
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const printWindow = window.open('', '_blank');
                                printWindow.document.write(`
                                    <!DOCTYPE html>
                                    <html>
                                    <head>
                                        <title>Print Photo Documentation</title>
                                        <style>
                                            body, html { margin: 0; padding: 0; height: 100%; }
                                            img { width: 100%; height: 100%; }
                                        </style>
                                    </head>
                                    <body>
                                        <img src="${e.target.result}" frameborder="0">
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
                                console.log("Calling showErrorAlert for non-printable file");
                                showErrorAlert("Error", "Cannot print DOC/DOCX files directly.");
                            } else {
                                console.warn("showErrorAlert is not defined. Falling back to native alert.");
                                alert("Error: Cannot print DOC/DOCX files directly.");
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
                console.log("Alert script running. Status code: <?php echo htmlspecialchars($_SESSION['status-code']); ?>");
                try {
                    const title = "<?php echo isset($_SESSION['alert']) ? addslashes($_SESSION['alert']) : ($_SESSION['status-code'] === 'success' ? 'Success' : 'Error'); ?>";
                    const message = "<?php echo addslashes($_SESSION['status']); ?>";
                    console.log("Alert details: title=" + title + ", message=" + message);
                    if ("<?php echo htmlspecialchars($_SESSION['status-code']); ?>" === "success") {
                        if (typeof showSuccessAlert === 'function') {
                            console.log("Calling showSuccessAlert with title: " + title + ", message: " + message);
                            showSuccessAlert(title, message);
                        } else {
                            console.warn("showSuccessAlert is not defined. Falling back to native alert.");
                            alert("Success: " + message);
                        }
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert with title: " + title + ", message: " + message);
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