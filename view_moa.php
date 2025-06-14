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
        
        $stmt = $conn->prepare("SELECT id, moa_file FROM company_moa WHERE company_name = ?");
        $stmt->execute([$company_name]);
        $moa_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
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
        gap: 10px;
        min-width: 200px;
        padding: 10px;
    }
    
    .moa-button, 
    .moa-view-btn,
    .moa-download-btn,
    .moa-print-btn,
    .moa-renew-btn,
    .moa-edit-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        text-align: center;
        min-width: 180px;
        height: 50px;
    }
    
    .moa-button img,
    .moa-view-btn img,
    .moa-download-btn img,
    .moa-print-btn img,
    .moa-renew-btn img {
        width: 20px;
        height: 20px;
        object-fit: contain;
        margin: 0;
    }
    .moa-view-btn, .moa-edit-btn {
        background: #700000;
        color: #fff;
    }
    
    .moa-download-btn {
        background: #0c0c9b;
        color: #fff;
    }
    
    .moa-print-btn {
        background: #006400;
        color: #fff;
    }
    
    .moa-renew-btn {
        background: #4CAF50;
        color: #fff;
    }
    
    .moa-view-btn:hover, 
    .moa-download-btn:hover, 
    .moa-print-btn:hover, 
    .moa-renew-btn:hover,
    .moa-edit-btn:hover {
        opacity: 0.9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .moa-view-btn:active, 
    .moa-download-btn:active, 
    .moa-print-btn:active, 
    .moa-renew-btn:active,
    .moa-edit-btn:active {
        transform: translateY(0);
    }
    .moa-view-btn, .moa-edit-btn {
        background: #700000;
        color: #fff !important;
    }
    .moa-download-btn {
        padding: 15px 95px;
        border: 2px solid #700000;
        background: #700000;
        color: #fff !important;
    }
    .moa-button {
        position: relative;
        width: 100%;
        padding: 10px 15px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
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
            gap: 12px;
            margin-top: 1rem;
            min-width: 200px;
            padding: 10px;
        }

        .moa-status-flex .moa-view-btn {
            justify-self: center;
            align-self: center;
        }
        .moa-button,
    .moa-view-btn, 
    .moa-download-btn, 
    .moa-print-btn, 
    .moa-renew-btn,
    .moa-edit-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        border: 2px solid transparent;
        text-align: center;
        min-width: 240px;
        height: 48px;
        transition: all 0.2s ease;
    }
    .moa-view-btn {
        padding: 12px 20px;
        border: 2px solid #700000;
        background: #700000;
        color: #fff !important;
        justify-content: center;
    }
    .moa-download-btn {
        padding: 12px 20px;
        background: #0c0c9b;
        color: #fff !important;
        justify-content: center;
    }
    .moa-download-btn span,
    .moa-view-btn span {
        margin: 0;
    }
    .moa-print-btn {
        padding: 12px 20px;
        background: #006400;
        color: #fff !important;
        justify-content: center;
    }
    .moa-renew-btn {
        padding: 15px 85px;
        background: #0c0c9b;
        border: 2px solid #0c0c9b;
        color: #fff !important;
    }
    .moa-view-btn:hover, .moa-download-btn:hover, .moa-print-btn:hover, .moa-renew-btn:hover,
    .moa-view-btn:active, .moa-download-btn:active, .moa-print-btn:active, .moa-renew-btn:active {
        color: #fff !important;
    }
    .moa-view-btn:hover, .moa-edit-btn:hover {
        background: #5a0000;
        border-color: #5a0000;
    }
    
    .moa-download-btn:hover {
        background: #0a0a7d;
        border-color: #0a0a7d;
    }
    
    .moa-print-btn:hover {
        background: #004d00;
        border-color: #004d00;
    }
    
    .moa-renew-btn:hover {
        background: #3d8b40;
        border-color: #3d8b40;
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
                    <?php 
                    $back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '#';
                    $onclick = $back_url === '#' ? 'window.history.back(); return false;' : 'return true;';
                    ?>
                    <a href="<?php echo htmlspecialchars($back_url); ?>" class="back-button" onclick="<?php echo $onclick ?>">
                        <img src="images/less-than.png" alt="Back" style="width: 30px; height: 30px; color: #333;">
                        Back
                    </a>
         <br>
           

<script>
    const imgInput = document.getElementById('new-moa-img-src');
    const imgPreview = document.getElementById('new-moa-img-preview');
    imgInput.addEventListener('input', function() {
        imgPreview.src = this.value || 'images/download.png';
    });


</script>
<div class="moa-status-container">
    <span class="moa-status-label">Memorandum of Agreement</span>
    
    <div class="moa-status-flex">
        <div class="moa-document-area" style="width:120%; max-width:4000px; height:800px; border-radius:6px; margin:0 auto; padding:20px; box-sizing:border-box; display:flex; flex-direction:column;">
            <?php if ($has_moa && isset($pdf_data_url)): ?>
                <div class="pdf-container" style="width:100%; flex:1; overflow:auto; background:#f5f5f5; position:relative;">
                    <canvas id="pdf-canvas" style="display:block; margin:0 auto;"></canvas>
                </div>
                
                <div id="pdf-controls" style="margin-top:15px; text-align:center; padding:5px 0 5px 0;">
                    <button id="prev-page" class="btn btn-sm btn-secondary">
                        <i class="ti-arrow-left"></i> Previous
                    </button>
                    <span style="margin:0 15px; color:#333; font-size:14px;">
                        Page <span id="page-num">1</span> of <span id="page-count">0</span>
                    </span>
                    <button id="next-page" class="btn btn-sm btn-secondary">
                        Next <i class="ti-arrow-right"></i>
                    </button>
                </div>
                
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
                <script>
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
                
                let pdfDoc = null;
                let pageNum = 1;
                let pageRendering = false;
                let pageNumPending = null;
                let scale = 1.5;
                
                function renderPage(num) {
                    pageRendering = true;
                    document.getElementById('page-num').textContent = num;
                    
                    pdfDoc.getPage(num).then(function(page) {
                        const viewport = page.getViewport({ scale: scale });
                        const canvas = document.getElementById('pdf-canvas');
                        const context = canvas.getContext('2d');
                        
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        
                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
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
                    
                    document.getElementById('prev-page').disabled = (num <= 1);
                    document.getElementById('next-page').disabled = (num >= pdfDoc.numPages);
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
                
                function queueRenderPage(num) {
                    if (pageRendering) {
                        pageNumPending = num;
                    } else {
                        renderPage(num);
                    }
                }
                
                const loadingTask = pdfjsLib.getDocument({
                    url: '<?php echo $pdf_data_url; ?>',
                    cMapUrl: "https://unpkg.com/pdfjs-dist@3.4.120/cmaps/",
                    cMapPacked: true
                });
                
                loadingTask.promise.then(function(pdf) {
                    pdfDoc = pdf;
                    document.getElementById('page-count').textContent = pdf.numPages;
                    renderPage(1);
                }).catch(function(error) {
                    console.error("Error loading PDF:", error);
                    const container = document.querySelector(".pdf-container");
                    container.innerHTML = `
                        <div style="padding: 40px; text-align: center; color: #700000;">
                            <p style="font-size: 16px; margin-bottom: 15px;">Error loading PDF. The file may be corrupted or not properly uploaded.</p>
                            <a href="<?php echo $moa_file_url; ?>" target="_blank" class="btn btn-primary">
                                <i class="ti-export"></i> Open in New Tab
                            </a>
                        </div>`;
                });
                
                document.getElementById('prev-page').addEventListener('click', onPrevPage);
                document.getElementById('next-page').addEventListener('click', onNextPage);
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowLeft') {
                        onPrevPage();
                    } else if (e.key === 'ArrowRight') {
                        onNextPage();
                    }
                });
                </script>
            <?php else: ?>
                <div style="text-align: center; padding: 50px 20px; color: #700000;">
                    <i class="ti-file" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                    <p style="font-size: 16px; margin-bottom: 20px;">No MOA document has been uploaded yet.</p>
                    <a href="moa_application_renewal.php" class="btn btn-primary">
                        <i class="ti-upload"></i> Upload MOA
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="js/lib/jquery.min.js"></script>
<script src="js/lib/bootstrap.min.js"></script>
<script src="js/lib/sweetalert/sweetalert.min.js"></script>

<?php if (isset($_SESSION['status']) && $_SESSION['status'] != ''): ?>
<script>
    swal({
        title: "<?php echo addslashes($_SESSION['alert']); ?>",
        text: "<?php echo addslashes($_SESSION['status']); ?>",
        icon: "<?php echo $_SESSION['status-code']; ?>"
    });
    <?php unset($_SESSION['status']); ?>
</script>
<?php endif; ?>

</body>
</html>

