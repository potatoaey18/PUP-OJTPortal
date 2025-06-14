<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$current_student_id = '';
if (isset($_GET['student_ID'])) {
    $current_student_id = $_GET['student_ID'];
} elseif (isset($_GET['intern_id'])) {
    $current_student_id = $_GET['intern_id'];
} elseif (isset($_POST['student_ID'])) {
    $current_student_id = $_POST['student_ID'];
}

if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    echo "<script>window.location.href='index.php'</script>";
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
    <!-- PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js" integrity="sha512-ml/QKfGJH+SYbj7zQScUMGib2A7vEYU79DbbVjwfJRy6qOo+WDWtQfDppAQDp+lM3kS1pI6FoY7Jb5k6LhYQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>

        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            overflow-y: auto;
        }
        .content-wrap {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
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
                    <br>
        

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
    <span class="moa-status-label">Evaluation for Student Intern</span>
    <div class="evaluation-viewer-container" style="width:100%;">
        <?php
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

        if (empty($current_student_id)) {
            echo '<div class="no-document">No student selected.</div>';
        } elseif (empty($eval) || empty($eval['evaluation_file'])) {
            echo '<div class="no-document">No evaluation document found for this student.</div>';
        } elseif (!$is_student_pdf) {
            echo '<div class="no-document">The evaluation document could not be displayed. <a href="' . htmlspecialchars($student_eval_pdf_url) . '" download>Click here to download</a>.</div>';
        } else {
            $pdf_data = file_get_contents($local_file_path);
            $pdf_base64 = base64_encode($pdf_data);
            $pdf_data_url = 'data:application/pdf;base64,' . $pdf_base64;
            
            echo '<div id="pdf-viewer" class="pdf-viewer">';
            echo '  <div class="pdf-container">';
            echo '    <canvas id="pdf-canvas"></canvas>';
            echo '  </div>';
            echo '  <div class="pdf-controls">';
            echo '    <button id="prev-page" class="btn btn-primary">Previous</button>';
            echo '    <span id="page-info" class="page-info">Page <span id="page-num">1</span> of <span id="page-count">1</span></span>';
            echo '    <button id="next-page" class="btn btn-primary">Next</button>';
            echo '  </div>';
            echo '</div>';      
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>';
            echo '<script>';
            echo 'pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js";';
            echo 'let pdfDoc = null;';
            echo 'let pageNum = 1;';
            echo 'let pageRendering = false;';
            echo 'let pageNumPending = null;';
            echo 'const scale = 2.0;';
            echo 'function loadPDF() {';
            echo '  const loadingTask = pdfjsLib.getDocument(\'' . $pdf_data_url . '\');';
            echo '  loadingTask.promise.then(function(pdf) {';
            echo '    pdfDoc = pdf;';
            echo '    document.getElementById("page-count").textContent = pdf.numPages;';
            echo '    renderPage(1);';
            echo '  }).catch(function(error) {';
            echo '    console.error("Error loading PDF: ", error);';
            echo '    alert("Error loading PDF. Please try again.");';
            echo '  });';
            echo '}';
            echo 'function renderPage(num) {';
            echo '  pageRendering = true;';
            echo '  document.getElementById("page-num").textContent = num;';
            echo '  pdfDoc.getPage(num).then(function(page) {';
            echo '    const viewport = page.getViewport({ scale: scale });';
            echo '    const canvas = document.getElementById("pdf-canvas");';
            echo '    const ctx = canvas.getContext("2d");';
            echo '    canvas.height = viewport.height;';
            echo '    canvas.width = viewport.width;';
            echo '    const renderContext = {';
            echo '      canvasContext: ctx,';
            echo '      viewport: viewport';
            echo '    };';
            echo '    const renderTask = page.render(renderContext);';
            echo '    renderTask.promise.then(function() {';
            echo '      pageRendering = false;';
            echo '      if (pageNumPending !== null) {';
            echo '        renderPage(pageNumPending);';
            echo '        pageNumPending = null;';
            echo '      }';
            echo '    });';
            echo '    document.getElementById("page-num").textContent = num;';
            echo '  });';
            echo '  document.getElementById("prev-page").disabled = (num <= 1);';
            echo '  document.getElementById("next-page").disabled = (num >= pdfDoc.numPages);';
            echo '}';
            echo 'function queueRenderPage(num) {';
            echo '  if (pageRendering) {';
            echo '    pageNumPending = num;';
            echo '  } else {';
            echo '    renderPage(num);';
            echo '  }';
            echo '}';
            echo 'document.addEventListener("DOMContentLoaded", function() {';
            echo '  if (document.getElementById("pdf-canvas")) {';
            echo '    loadPDF();';
            echo '  }';
            echo '  document.getElementById("prev-page").addEventListener("click", function() {';
            echo '    if (pageNum <= 1) return;';
            echo '    pageNum--;';
            echo '    queueRenderPage(pageNum);';
            echo '  });';
            echo '  document.getElementById("next-page").addEventListener("click", function() {';
            echo '    if (pageNum >= pdfDoc.numPages) return;';
            echo '    pageNum++;';
            echo '    queueRenderPage(pageNum);';
            echo '  });';
            echo '});';
            echo '</script>';
        }
        ?>
    </div>
</div>

<style>
.evaluation-viewer-container {
    width: 100%;
    height: 100vh;
    margin: 0;
    padding: 20px;
    box-sizing: border-box;
}

.pdf-viewer {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #f5f5f5;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.pdf-container {
    flex: 1;
    overflow: auto;
    background: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px;
    box-sizing: border-box;
}

#pdf-canvas {
    width: auto !important;
    height: auto !important;
    max-width: none !important;
    max-height: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    background: white;
    transform-origin: 0 0;
    transform: scale(1.2);
}

.pdf-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-top: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.btn-primary {
    background-color: #700000;
    color: white;
    border: none;
    padding: 6px 15px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background-color: #5a0000;
}

.btn-primary:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

.page-info {
    color: #495057;
    font-size: 14px;
    font-weight: 500;
    min-width: 100px;
    text-align: center;
}

.no-document {
    text-align: center;
    padding: 50px 20px;
    color: #700000;
    font-size: 16px;
}

.no-document a {
    color: #007bff;
    text-decoration: none;
}

.no-document a:hover {
    text-decoration: underline;
}
</style>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

let pdfDoc = null;
let pageNum = 1;
let pageRendering = false;
let pageNumPending = null;
const scale = 2.0;

function loadPDF() {
    const loadingTask = pdfjsLib.getDocument('<?php echo $pdf_data_url; ?>');
    loadingTask.promise.then(function(pdf) {
        pdfDoc = pdf;
        document.getElementById('page-count').textContent = pdf.numPages;
        
        renderPage(1);
    }, function(reason) {
        console.error('Error loading PDF: ', reason);
        alert('Error loading PDF. Please try again.');
    });
}

function renderPage(num) {
    pageRendering = true;
    document.getElementById('page-num').textContent = num;
    
    pdfDoc.getPage(num).then(function(page) {
        const viewport = page.getViewport({ scale: scale });
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        
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
        
        document.getElementById('page-num').textContent = num;
    });
    
    document.getElementById('prev-page').disabled = (num <= 1);
    document.getElementById('next-page').disabled = (num >= pdfDoc.numPages);
}

function queueRenderPage(num) {
    if (pageRendering) {
        pageNumPending = num;
    } else {
        renderPage(num);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('pdf-canvas')) {
        loadPDF();
    }
});

document.getElementById('prev-page').addEventListener('click', function() {
    if (pageNum <= 1) return;
    pageNum--;
    queueRenderPage(pageNum);
});
document.getElementById('next-page').addEventListener('click', function() {
    if (pageNum >= pdfDoc.numPages) return;
    pageNum++;
    queueRenderPage(pageNum);
});
window.addEventListener('load', function() {
    <?php if ($is_student_pdf): ?>
    loadPDF();
    <?php endif; ?>
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

