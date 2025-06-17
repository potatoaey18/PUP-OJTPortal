<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$current_student_id = '';
$evaluation_status = '';
$show_success = false;

if (isset($_GET['student_ID'])) {
    $current_student_id = $_GET['student_ID'];
} elseif (isset($_GET['intern_id'])) {
    $current_student_id = $_GET['intern_id'];
} elseif (isset($_POST['student_ID'])) {
    $current_student_id = $_POST['student_ID'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_evaluation'])) {
    if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
        header('Location: index.php');
        exit();
    }
    
    $student_id = $_POST['student_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (!empty($student_id) && !empty($status)) {
        try {
            
            $stmt = $conn->prepare("SELECT * FROM student_evaluations WHERE student_id = ?");
            $stmt->execute([$student_id]);
            
            if ($stmt->rowCount() > 0) {
                $stmt = $conn->prepare("UPDATE student_evaluations SET evaluation_status = ? WHERE student_id = ?");
                $result = $stmt->execute([$status, $student_id]);
            } else {
                
                $stmt = $conn->prepare("INSERT INTO student_evaluations (student_id, evaluation_status) VALUES (?, ?)");
                $result = $stmt->execute([$student_id, $status]);
            }
            
            if ($result) {
                $evaluation_status = $status;
                $show_success = true;
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => !empty($result)]);
        exit();
    }
}

if (!empty($current_student_id)) {
    try {
        $stmt = $conn->prepare("SELECT evaluation_status FROM student_evaluations WHERE student_id = ?");
        $stmt->execute([$current_student_id]);
        $result = $stmt->fetch();
        if ($result && !empty($result['evaluation_status'])) {
            $evaluation_status = $result['evaluation_status'];
        }
    } catch (PDOException $e) {
        error_log("Error checking evaluation status: " . $e->getMessage());
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
    <link rel="shortcut icon" href="images/pupLogo.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js" integrity="sha512-ml/QKfGJH+SYbj7zQScUMGib2A7vEYU79DbbVjwfJRy6qOo+WDWtQfDppAQDp+lM3kS1pI6FoY7Jb5k6LhYQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        .pdf-container {
            width: 100%;
            height: 500px; 
            overflow: auto;
            border-radius: 4px;
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 15px;
            box-sizing: border-box;
        }
        .pdf-viewer {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .pdf-viewer canvas {
            max-width: 100%;
            max-height: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            background: white;
        }
        .pdf-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 4px;
        }
        .pdf-controls button {
            background: #700000;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .pdf-controls button:hover {
            background: #500000;
        }
        .pdf-controls button:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }
        .pdf-page-info {
            color: #495057;
            font-size: 14px;
            font-weight: 500;
            min-width: 100px;
            text-align: center;
        }
        .no-document-message {
            color: #700000;
            text-align: center;
            padding: 20px;
            font-size: 16px;
            font-weight: 500;
            background: #fff8f8;
            border: 1px dashed #ffcccc;
            border-radius: 4px;
            margin: 20px 0;
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
        grid-template-columns: 1fr 1fr;
        align-items: center;
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
            border: 0 !important;
            display: none !important;
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
            display: none !important;
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
        .doc-download-link {
    color: #0074d9;
    text-decoration: underline !important;
    font-size: 15px;
    cursor: pointer;
    display: inline-block;
    text-align: center;
}
.doc-download-link:hover {
    color: #0056a3;
    text-decoration: underline !important;
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
                    <br><br><br>
                    <h1 style="font-size: 17px;"><b>Evaluation to Interns</b></h1>
                    <br>
                    <p class="moa-renew-note" style="font-size:13px; color:#700000; background:#fff6f6; border-left:3px solid #700000; padding:7px 13px; border-radius:4px; margin-bottom:18px;">
    <b>Note:</b> The H.T.E Supervisor requires to evaluate the Student interns performance during their training. Download the template first and input your work here.
</p>
                    <div class="moa-newfile-container">
    <span class="moa-status-label">New Moa File</span>
    <div style="display: flex; align-items: center; justify-content: center; width: 100%; position: relative; min-height: 50px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin: 0 auto;">
            <img src="images/doc.png" alt="File Icon" style="height:34px; width:auto; display:inline-block;">
            <a href="downloadable-documents/Evaluation-Instrument-for-Student-Intern_Final-FOR-HTE - Copy.docx" download class="doc-download-link">
                Evaluation-Instrument-for-Student-Intern_Final-FOR-HTE.docx
            </a>
        </div>
        <a href="downloadable-documents/Evaluation-Instrument-for-Student-Intern_Final-FOR-HTE - Copy.docx" download style="position: absolute; right: 0; top: 50%; transform: translateY(-50%);">
            <img id="new-moa-img-preview" src="images/download.png" alt="Download Evaluation Instrument" style="height:28px; width:auto; filter: invert(18%) sepia(95%) saturate(7495%) hue-rotate(349deg) brightness(55%) contrast(105%); cursor:pointer;">
        </a>
    </div>
</div>
</div>
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
    <div>
        <div class="moa-document-area" style="width:100%; max-width:530px; height:702px; background:#f9f9f9; border-radius:6px; display:flex; align-items:center; justify-content:center; margin-bottom:40px; margin-left:80px; margin-top: 30px;">
            <?php
            
            require_once '../connection/config.php';
            $student_eval_pdf_url = '';
            $is_student_pdf = false;
            if (!empty($current_student_id)) {
                $stmt = $conn->prepare("SELECT evaluation_file FROM student_evaluations WHERE student_ID = ? LIMIT 1");
                $stmt->execute([$current_student_id]);
                $eval = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($eval && !empty($eval['evaluation_file'])) {
                    $student_eval_pdf_url = '/ojt/' . ltrim($eval['evaluation_file'], '/');
                   
                    $local_file_path = realpath(__DIR__ . '/../' . ltrim($eval['evaluation_file'], '/'));
                    $is_student_pdf = strtolower(pathinfo($student_eval_pdf_url, PATHINFO_EXTENSION)) === 'pdf' && $local_file_path && file_exists($local_file_path);
                }
            }
            if ($is_student_pdf) {
              
                $pdf_data = file_get_contents($local_file_path);
                $pdf_base64 = base64_encode($pdf_data);
                $pdf_data_url = 'data:application/pdf;base64,' . $pdf_base64;
                
                echo '<div id="pdf-viewer" class="pdf-viewer">';
                echo '  <div class="pdf-container">';
                echo '    <canvas id="pdf-canvas"></canvas>';
                echo '  </div>';
                echo '  <div class="pdf-controls">';
                echo '    <button id="prev-page" class="btn btn-primary" style="font-size: 0.85rem; width: 6rem;"><i class="ti-arrow-left"></i> Previous</button> ';
                echo '    <span id="page-info" class="page-info" style="font-size: 1rem; font-weight: bold;">Page <span id="page-num">1</span> of <span id="page-count">1</span></span>';
                echo '    <button id="next-page" class="btn btn-primary" style="font-size: 0.85rem;">Next <i class="ti-arrow-right"></i></button>';
                echo '  </div>';
                echo '</div>';
                echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>';
                echo '<style>';
                echo '  .pdf-container { 
      height: 595px; 
      overflow-y: auto; 
      overflow-x: hidden;
      margin: 0 auto 15px; 
      border-radius: 4px; 
      display: flex; 
      flex-direction: column;
      justify-content: flex-start; 
      padding: 20px;
      box-sizing: border-box;
      margin-top: 10px;
    }';
                echo '  .pdf-viewer { 
      width: 100%;
      max-width: 1000px;
      margin: 0 auto;
      position: relative;
    }';
                echo '  #pdf-canvas {
      max-width: 100%;
      width: auto;
      height: auto;
      margin: 0 auto;
      display: block;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }';
                echo '  .pdf-controls { 
      text-align: center; 
      margin: 10px 0; 
      padding: 10px;
      background: #f8f9fa;
      border-radius: 4px;
    }';
                echo '  .page-info { 
      margin: 0 15px; 
      display: inline-block;
      line-height: 38px;
      vertical-align: middle;
    }';
                echo '  #pdf-canvas { 
      max-width: 100%; 
      max-height: 100%;
    }';
                echo '  .btn-primary {
      background-color: #700000;
      border-color: #700000;
    }';
                echo '  .btn-primary:hover {
      background-color: #5a0000;
      border-color: #5a0000;
    }';
                echo '</style>';
                
                echo '<script>';
                echo '  // Set the worker source to CDN
  pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js";
  
  let pdfDoc = null;
  let pageNum = 1;
  let pageRendering = false;
  let pageNumPending = null;
  const scale = 1.5;
  
  const canvas = document.getElementById("pdf-canvas");
  const ctx = canvas.getContext("2d");
  
  function renderPage(num) {
    pageRendering = true;
    document.getElementById("page-num").textContent = num;
    
    // Using a promise to fetch the page
    pdfDoc.getPage(num).then(function(page) {
      const viewport = page.getViewport({ scale: scale });
      
      // Prepare canvas
      canvas.height = viewport.height;
      canvas.width = viewport.width;
      
      // Render PDF page
      const renderContext = {
        canvasContext: ctx,
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
  
  // Add event listeners
  document.getElementById("prev-page").addEventListener("click", onPrevPage);
  document.getElementById("next-page").addEventListener("click", onNextPage);
  
  // Load the PDF using base64 data URL
  const loadingTask = pdfjsLib.getDocument({
    url: "' . $pdf_data_url . '",
    cMapUrl: "https://unpkg.com/pdfjs-dist@3.4.120/cmaps/",
    cMapPacked: true
  });
  
  loadingTask.promise.then(function(pdf) {
    pdfDoc = pdf;
    document.getElementById("page-count").textContent = pdf.numPages;
    renderPage(1);
  }).catch(function(error) {
    console.error("Error loading PDF:", error);
    const container = document.querySelector(".pdf-container");
    container.innerHTML = `
      <div style="padding: 20px; text-align: center;">
        <p>Error loading PDF. Please try opening in a new tab or check the file.</p>
        <a href="' . $student_eval_pdf_url . '" target="_blank" class="btn btn-primary">Open in New Tab</a>
      </div>`;
  });
</script>';
            
            } else {
                echo '<div class="no-document-message" style="color:#800000; text-align:center; width:100%;">No evaluation PDF uploaded yet for this student.</div>';
            }
            ?>
        </div>
    </div>
    <div class="moa-btn-col" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <?php if ($is_student_pdf): ?>
            
            <?php if ($evaluation_status !== 'Complete'): ?>
                <button type="button" class="moa-view-btn moa-edit-btn" onclick="document.getElementById('evaluation_pdf').click();">Edit File</button>
            <?php else: ?>
                <button type="button" class="moa-view-btn moa-edit-btn" disabled style="background-color: #6c757d; border-color: #6c757d; color: #fff; cursor: not-allowed; box-shadow: none; position: relative; overflow: hidden;">
                    <span style="position: relative; z-index: 1;">Edit File</span>
                </button>
            <?php endif; ?>
            
          
            <button class="moa-download-btn" onclick="window.location.href='view_evaluation.php?student_ID=<?php echo htmlspecialchars($current_student_id); ?>'">View</button>
            
            
            <button class="moa-print-btn" onclick="printPDF('<?php echo urlencode($student_eval_pdf_url); ?>')">Print</button>
            
    
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php if ($evaluation_status !== 'Complete'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="update_evaluation" value="1">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($current_student_id); ?>">
                        <input type="hidden" name="status" value="Complete">
                        <button type="submit" class="moa-renew-btn" style="background-color: #0c0c9b; border-color: #0c0c9b;">
                            Submit
                        </button>
                    </form>
                <?php else: ?>
                    <button type="button" class="moa-renew-btn" disabled style="background-color: #6c757d; border-color: #6c757d; color: #fff; cursor: not-allowed; box-shadow: none; position: relative; overflow: hidden;">
                        <span style="position: relative; z-index: 1;">Submitted</span>
                    </button>
                <?php endif; ?>
        <?php else: ?>
         
            <button type="button" class="moa-view-btn" onclick="document.getElementById('evaluation_pdf').click();">Upload</button>
            
       
            <button class="moa-download-btn" disabled style="cursor: not-allowed; background-color: #6c757d; border-color: #6c757d; color: #fff; box-shadow: none; position: relative; overflow: hidden;">
                <span style="position: relative; z-index: 1;">View</span>
            </button>
            
          
            <button class="moa-print-btn" disabled style="cursor: not-allowed; background-color: #6c757d; border-color: #6c757d; color: #fff; box-shadow: none; position: relative; overflow: hidden;">
                <span style="position: relative; z-index: 1;">Print</span>
            </button>
            
         
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button type="button" class="moa-renew-btn" disabled style="background-color: #6c757d; border-color: #6c757d; color: #fff; cursor: not-allowed; box-shadow: none; position: relative; overflow: hidden;">
                    <span style="position: relative; z-index: 1;">Submit</span>
                </button>
            </div>
        <?php endif; ?>
            
            <?php if (($show_success || $evaluation_status === 'Complete')): ?>
                <div id="successMessage" style="display: block; margin-top: 5px;">
                    <div style="border: 2px solid #700000; border-radius: 8px; padding: 12px 15px; background-color: #f8f9fa; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #28a745;">
                                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span style="color: #28a745; font-size: 14px; font-weight: 500;">You successfully evaluated this intern</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <form id="uploadEvalForm" method="POST" enctype="multipart/form-data" style="display:none;">
            <input type="file" name="evaluation_pdf" id="evaluation_pdf" accept="application/pdf" onchange="document.getElementById('uploadEvalForm').submit();">
            <input type="hidden" name="student_ID" value="<?php echo htmlspecialchars($current_student_id); ?>">
        </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['evaluation_pdf']) && isset($_POST['student_ID'])) {
    $student_ID = $current_student_id;
    $file = $_FILES['evaluation_pdf'];
    $errors = [];

    if (!$student_ID) {
        $errors[] = 'Invalid student ID.';
    }

    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($fileType !== 'pdf') {
        $errors[] = 'Only PDF files are allowed.';
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error.';
    }

    $uploadDir = __DIR__ . '/evaluation/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $filename = 'evaluation_' . $student_ID . '_' . time() . '.pdf';
    $targetPath = $uploadDir . $filename;

    if (empty($errors)) {
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $relativePath = 'supervisor/evaluation/' . $filename;
            require_once '../connection/config.php';
            $stmt = $conn->prepare("INSERT INTO student_evaluations (student_ID, evaluation_file) VALUES (?, ?) ON DUPLICATE KEY UPDATE evaluation_file = VALUES(evaluation_file)");
            if ($stmt->execute([$student_ID, $relativePath])) {
                echo '<script>Swal.fire({
                    title: "<span class=\"swal-title-white\">Success</span>",
                    html: "<span class=\"swal-text-white\">Evaluation uploaded successfully!</span>",
                    icon: "success",
                    background: "#700000",
                    confirmButtonColor: "#ffffff",
                    confirmButtonText: "<span style=\"color: #700000;\">OK</span>",
                    customClass: {
                        popup: "swal-custom-popup"
                    }
                }).then(()=>{window.location.href=window.location.pathname+"?student_ID='.$student_ID.'";});</script>';
            } else {
                unlink($targetPath);
                echo '<script>Swal.fire({title: "Database Error", text: "Could not save file path to database.", icon: "error", confirmButtonColor: "#700000"});</script>';
            }
        } else {
            echo '<script>Swal.fire({title: "Upload Error", text: "Failed to move uploaded file.", icon: "error", confirmButtonColor: "#700000"});</script>';
        }
    } else {
        $errMsg = implode(' ', $errors);
        echo '<script>Swal.fire({title: "Error", text: "' . $errMsg . '", icon: "error", confirmButtonColor: "#700000"});</script>';
    }
}
?>
                             
                            </div>
                        </div>
                    </div>
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
    
    <script>
    function printPDF(pdfUrl) {
        if (!pdfUrl) {
            Swal.fire({
                title: 'No Document',
                text: 'No evaluation document is available to print.',
                icon: 'warning',
                confirmButtonColor: '#700000'
            });
            return;
        }
       
        window.open('view_pdf.php?file=' + pdfUrl, '_blank');
    }
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

