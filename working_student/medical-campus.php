<?php
include '../../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id'])) {
    header('Location: index.php');
    exit;
}
$student_id = $_SESSION['auth_user']['student_id'];

$upload_dir = "uploads/medical/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (isset($_POST['submit_medical_files'])) {
    $files_to_upload = [
        'xray_file' => 'X-RAY PA VIEW',
        'cor_file' => 'COR',
        'declaration_file' => 'Declaration of Medical Information and Data Subject Consent Form',
        'health_info_file' => 'Health Information Form for Student'
    ];
    $file_paths = [];

    foreach ($files_to_upload as $file_input => $doc_name) {
        if (!empty($_FILES[$file_input]['name'])) {
            if ($_FILES[$file_input]['error'] == UPLOAD_ERR_OK) {
                $file_name = $student_id . "_" . $file_input . "_" . time() . "_" . basename($_FILES[$file_input]['name']);
                $target_file = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES[$file_input]['tmp_name'], $target_file)) {
                    $file_paths[$file_input] = ['path' => $target_file, 'name' => $doc_name];
                } else {
                    $_SESSION['status'] = "Error moving uploaded file for " . $file_input;
                    $_SESSION['alert'] = "Error";
                    $_SESSION['status-code'] = "error";
                }
            } else {
                $error_messages = [
                    UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
                    UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
                    UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
                    UPLOAD_ERR_NO_FILE => "No file was uploaded",
                    UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
                    UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                    UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload",
                ];
                $error_code = $_FILES[$file_input]['error'];
                $error_message = $error_messages[$error_code] ?? "Unknown upload error";
                $_SESSION['status'] = "Error uploading " . $file_input . ": " . $error_message;
                $_SESSION['alert'] = "Error";
                $_SESSION['status-code'] = "error";
            }
        }
    }

    if (!empty($file_paths)) {
        try {
            foreach ($file_paths as $file_input => $file_data) {
                $query = "INSERT INTO endorsement_documents (student_id, document_name, document_type, uploaded_path, upload_date, status, medical_at_campus) 
                          VALUES (:student_id, :document_name, :document_type, :uploaded_path, NOW(), 'pending', 'YES')";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->bindValue(':document_name', $file_data['name'], PDO::PARAM_STR);
                $stmt->bindValue(':document_type', $file_input, PDO::PARAM_STR);
                $stmt->bindValue(':uploaded_path', $file_data['path'], PDO::PARAM_STR);
                $stmt->execute();
            }

            $_SESSION['status'] = "Files uploaded successfully!";
            $_SESSION['alert'] = "Success";
            $_SESSION['status-code'] = "success";
        } catch (PDOException $e) {
            $_SESSION['status'] = "Database error: " . $e->getMessage();
            $_SESSION['alert'] = "Error";
            $_SESSION['status-code'] = "error";
        }
    } else {
        $_SESSION['status'] = "No files were selected for upload.";
        $_SESSION['alert'] = "Notice";
        $_SESSION['status-code'] = "info";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch existing uploads
$existing_uploads = [];
$document_paths = [];
try {
    $query = "SELECT document_type, uploaded_path FROM endorsement_documents WHERE student_id = :student_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $row) {
        $existing_uploads[] = $row['document_type'];
        $document_paths[$row['document_type']] = $row['uploaded_path'];
    }
} catch (PDOException $e) {
    $_SESSION['status'] = "Error fetching uploads: " . $e->getMessage();
    $_SESSION['alert'] = "Error";
    $_SESSION['status-code'] = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Medical - Campus Clinic</title>
    <link rel="stylesheet" href="css/lib/font-awesome.min.css">
    <link rel="stylesheet" href="css/lib/themify-icons.css">
    <link rel="stylesheet" href="css/lib/menubar/sidebar.css">
    <link rel="stylesheet" href="css/lib/bootstrap.min.css">
    <link rel="stylesheet" href="css/lib/helper.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/lib/sweetalert/sweetalert.css">
    <link href="endorsement-css/endorsement-moa.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .content-wrap {
            padding: 20px;
        }
        .page-title {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        .page-description {
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .box {
            background-color: white;
            border: 1px solid #8B0000;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            min-height:400px; /* Default height when preview is not active */
        }
        .box.preview-active {
            min-height: 600px; /* Extended height when preview is active */
        }
        @media (max-width: 768px) {
            .box {
                padding: 15px;
                min-height: 400px;
            }
            .box.preview-active {
                min-height: 600px;
            }
        }
        .document-title {
            color: #8B0000;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .document-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.2s;
            cursor: pointer;
            flex-wrap: wrap;
        }
        .document-row:hover {
            background-color: #f9f9f9;
        }
        .document-row.selected {
            background-color: #f0f0f0;
        }
        .document-checkbox {
            margin-right: 10px;
        }
        .document-name {
            flex-grow: 1;
            margin-left: 10px;
            min-width: 0;
        }
        .btn-upload {
            background-color: #8B0000;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-upload:hover {
            background-color: #a30000;
        }
        .btn-view,
        .btn-print,
        .btn-submit {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            margin: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 120px;
        }
        .btn-view {
            background-color: #8B0000;
            color: white;
        }
        .btn-print {
            background-color: #8B0000;
            color: white;
        }
        .btn-submit {
            background-color: #e0e0e0;
            color: #333;
        }
        .btn-view:hover,
        .btn-print:hover {
            opacity: 0.9;
        }
        .action-buttons {
            display: flex;
            flex-direction: row;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-icon {
            margin-right: 5px;
        }
        .download-section {
            margin-bottom: 20px;
        }
        .download-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        .download-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #0066cc;
            margin-bottom: 10px;
        }
        .download-icon {
            margin-right: 5px;
            width: 20px;
            height: 20px;
        }
        .edit-upload-btn {
            background-color: #8B0000;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .edit-upload-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #cccccc;
            color: #666666;
        }
        .hidden {
            display: none;
        }
        .document-preview {
            width: 100%;
            height: 0;
            overflow: hidden;
            transition: height 0.3s ease;
            background: white;
        }
        .document-preview.active {
            height: 300px;
        }
        .document-preview iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .document-preview.enlarged {
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
            <a href="medical.php" class="back-button">
                <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                Back
            </a>

            <p>On-Campus Medical Assistance</p>
            <p>
                After submitting, just wait for further announcements about your medical schedule to be posted on the homepage-announcements.
            </p>
            <br>

            <div>
                <h1 class="title">On-Campus Medical</h1>
            </div> 

            <div class="box">
                <form action="" method="POST" enctype="multipart/form-data" id="medicalForm">
                    <div class="document-row" onclick="selectDocument('xray')">
                        <input type="checkbox" name="xray_checkbox" id="xray_checkbox" class="document-checkbox" disabled <?php echo in_array('xray_file', $existing_uploads) ? 'checked' : ''; ?>>
                        <div class="document-name">X-Ray PA view</div>
                        <input type="file" name="xray_file" id="xray_file" accept=".pdf" class="hidden">
                        <button type="button" class="edit-upload-btn" onclick="document.getElementById('xray_file').click();">Upload</button>
                    </div>

                    <div class="document-row" onclick="selectDocument('cor')">
                        <input type="checkbox" name="cor_checkbox" id="cor_checkbox" class="document-checkbox" disabled <?php echo in_array('cor_file', $existing_uploads) ? 'checked' : ''; ?>>
                        <div class="document-name">COR</div>
                        <input type="file" name="cor_file" id="cor_file" accept=".pdf" class="hidden">
                        <button type="button" class="edit-upload-btn" onclick="document.getElementById('cor_file').click();">Upload</button>
                    </div>

                    <div class="document-row" onclick="selectDocument('declaration')">
                        <input type="checkbox" name="declaration_checkbox" id="declaration_checkbox" class="document-checkbox" disabled <?php echo in_array('declaration_file', $existing_uploads) ? 'checked' : ''; ?>>
                        <div class="document-name"><a href="templates/endorsement/New_MOA_Template.docx" download="New_MOA_Template.docx" ><u>Declaration of Medical Information and Data Subject Consent Form</u></a></div>
                        <input type="file" name="declaration_file" id="declaration_file" accept=".pdf" class="hidden">
                        <button type="button" class="edit-upload-btn" onclick="document.getElementById('declaration_file').click();">Upload</button>
                    </div>

                    <div class="document-row" onclick="selectDocument('health')">
                        <input type="checkbox" name="health_info_checkbox" id="health_info_checkbox" class="document-checkbox" disabled <?php echo in_array('health_info_file', $existing_uploads) ? 'checked' : ''; ?>>
                        <div class="document-name"><a href="templates/endorsement/New_MOA_Template.docx" download="New_MOA_Template.docx" ><u>Health Information Form for Students</u></a></div>
                        <input type="file" name="health_info_file" id="health_info_file" accept=".pdf" class="hidden">
                        <button type="button" class="edit-upload-btn" onclick="document.getElementById('health_info_file').click();">Upload</button>
                    </div>

                    <div class="document-preview" id="documentPreview">
                        <iframe id="previewIframe"></iframe>
                    </div>

                    <div class="action-buttons">
                        <button type="button" id="viewBtn" class="btn-view" disabled>
                            <i class="fa fa-eye btn-icon"></i> View
                        </button>
                        <button type="button" id="printBtn" class="btn-print" disabled>
                            <i class="fa fa-print btn-icon"></i> Print
                        </button>
                        <button type="submit" name="submit_medical_files" class="btn-submit">
                            <i class="fa fa-upload btn-icon"></i> Submit
                        </button>
                    </div>
                </form>
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
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>

    <script>
        let selectedDocument = null;
        let documentPaths = <?php echo json_encode($document_paths); ?>;
        const existingUploads = <?php echo json_encode($existing_uploads); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Disable upload buttons for existing uploads
            const fileInputs = {
                'xray_file': 'xray_checkbox',
                'cor_file': 'cor_checkbox',
                'declaration_file': 'declaration_checkbox',
                'health_info_file': 'health_info_checkbox'
            };

            Object.keys(fileInputs).forEach(inputId => {
                const uploadBtn = document.querySelector(`input[name="${inputId}"]`).parentElement.querySelector('.edit-upload-btn');
                if (existingUploads.includes(inputId)) {
                    uploadBtn.disabled = true;
                    uploadBtn.style.opacity = '0.5';
                    uploadBtn.style.cursor = 'not-allowed';
                    uploadBtn.textContent = 'Uploaded';
                }

                // Handle file input changes
                document.getElementById(inputId).addEventListener('change', function() {
                    if (this.files.length > 0) {
                        document.getElementById(fileInputs[inputId]).checked = true;
                        // Show preview of selected file
                        if (selectedDocument === inputId) {
                            const previewIframe = document.getElementById('previewIframe');
                            previewIframe.src = URL.createObjectURL(this.files[0]);
                            document.getElementById('documentPreview').classList.add('active');
                        }
                    }
                });
            });
        });

        function selectDocument(docType) {
            document.querySelectorAll('.document-row').forEach(row => {
                row.classList.remove('selected');
            });

            const selectedRow = document.querySelector(`.document-row[onclick="selectDocument('${docType}')"]`);
            if (selectedRow) {
                selectedRow.classList.add('selected');
            }

            selectedDocument = docType + '_file';
            const isUploaded = existingUploads.includes(selectedDocument);
            const fileInput = document.getElementById(selectedDocument);
            const viewBtn = document.getElementById('viewBtn');
            const printBtn = document.getElementById('printBtn');
            const previewIframe = document.getElementById('previewIframe');
            const documentPreview = document.getElementById('documentPreview');
            const box = document.querySelector('.box');

            viewBtn.disabled = !isUploaded && !fileInput.files.length;
            printBtn.disabled = !isUploaded && !fileInput.files.length;

            if (fileInput.files.length > 0) {
                // Show preview of newly selected file
                previewIframe.src = URL.createObjectURL(fileInput.files[0]);
                documentPreview.classList.add('active');
                box.classList.add('preview-active');
            } else if (isUploaded && documentPaths[selectedDocument]) {
                // Show preview of uploaded file
                previewIframe.src = documentPaths[selectedDocument];
                documentPreview.classList.add('active');
                box.classList.add('preview-active');
            } else {
                previewIframe.src = '';
                documentPreview.classList.remove('active');
                box.classList.remove('preview-active');
            }
        }

        // View button functionality
        document.getElementById('viewBtn').addEventListener('click', function(e) {
            e.preventDefault();
            if (selectedDocument) {
                const fileInput = document.getElementById(selectedDocument);
                const documentPreview = document.getElementById('documentPreview');
                const previewIframe = document.getElementById('previewIframe');
                const box = document.querySelector('.box');

                if (fileInput.files.length > 0) {
                    previewIframe.src = URL.createObjectURL(fileInput.files[0]);
                    documentPreview.classList.toggle('enlarged');
                    document.getElementById('overlay').classList.toggle('active');
                    box.classList.remove('preview-active'); // Remove preview-active when enlarged
                } else if (documentPaths[selectedDocument]) {
                    previewIframe.src = documentPaths[selectedDocument];
                    documentPreview.classList.toggle('enlarged');
                    document.getElementById('overlay').classList.toggle('active');
                    box.classList.remove('preview-active'); // Remove preview-active when enlarged
                } else {
                    sweetAlert("Notice", "No document available to view or no document selected", "info");
                }
            }
        });

        // Close enlarged preview when clicking overlay
        document.getElementById('overlay').addEventListener('click', function() {
            const documentPreview = document.getElementById('documentPreview');
            const box = document.querySelector('.box');
            documentPreview.classList.remove('enlarged');
            this.classList.remove('active');
            // Restore preview-active if a document is still selected
            if (selectedDocument && (document.getElementById(selectedDocument).files.length > 0 || documentPaths[selectedDocument])) {
                box.classList.add('preview-active');
                documentPreview.classList.add('active');
            }
        });

        // Print button functionality
        document.getElementById('printBtn').addEventListener('click', function(e) {
            e.preventDefault();
            if (selectedDocument) {
                const fileInput = document.getElementById(selectedDocument);
                let filePath;
                
                if (fileInput.files.length > 0) {
                    filePath = URL.createObjectURL(fileInput.files[0]);
                } else if (documentPaths[selectedDocument]) {
                    filePath = documentPaths[selectedDocument];
                }

                if (filePath) {
                    const printWindow = window.open(filePath, '_blank');
                    if (printWindow) {
                        printWindow.onload = function() {
                            printWindow.print();
                        };
                    }
                } else {
                    sweetAlert("Notice", "No document available to print or no document selected", "info");
                }
            }
        });
    </script>

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