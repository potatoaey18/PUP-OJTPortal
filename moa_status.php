<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
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
            margin-top: 6rem;
            background: none;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            bottom: 0;
            height: calc(100vh - 13.5rem);
            margin-bottom: 5rem;
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
        .moa-download-btn {
            padding: 15px 95px;
            border: 2px solid #700000;
            background: #700000;
            color: #fff !important;
        }
        .moa-print-btn {
            padding: 15px 117px;
            border: 2px solid #700000;
            background: #700000;
            color: #fff !important;
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
        }
        .swal-cancel-proceed {
            background-color:rgb(255, 255, 255) !important;
            color: #000000 !important;
        }
        .swal-confirm-proceed:hover, .swal-cancel-proceed:hover {
            background-color: #ffc107 !important;
            color: #700000 !important;
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
        .page-information-container {
            margin-left: 9.5rem;
        }

    </style>
</head>
<body>
<?php // include 'chatbot.php'; ?>
    <?php require_once 'templates/supervisor_navbar.php'; ?>

    <?php $supervisor_id = isset($_SESSION['auth_user']['supervisor_id']) ? $_SESSION['auth_user']['supervisor_id'] : 0;
$moa_file_url = '';
$pdf_data_url = '';
$is_pdf = false;
if ($supervisor_id) {
    require_once '../connection/config.php';
 
    $stmt = $conn->prepare("SELECT company_name FROM supervisor WHERE id = ? LIMIT 1");
    $stmt->execute([$supervisor_id]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
    $company_name = $supervisor['company_name'] ?? '';

    $moa_details = [];
    $moa_stmt = $conn->prepare("SELECT start_date_validity, end_date_validity FROM moa_form WHERE supervisor_id = ? ORDER BY created_at DESC LIMIT 1");
    $moa_stmt->execute([$supervisor_id]);
    $moa_details = $moa_stmt->fetch(PDO::FETCH_ASSOC);
    
    $start_date = '';
    $end_date = '';
    $validity_years = '';
    $remaining_time = '';
    
    if ($moa_details && !empty($moa_details['start_date_validity']) && !empty($moa_details['end_date_validity'])) {
        $start_date_obj = new DateTime($moa_details['start_date_validity']);
        $end_date_obj = new DateTime($moa_details['end_date_validity']);
        $today = new DateTime();

        $start_date = $start_date_obj->format('F j, Y');
        $end_date = $end_date_obj->format('F j, Y');
        
        $interval = $start_date_obj->diff($end_date_obj);
        $validity_years = $interval->y . ' Year' . ($interval->y != 1 ? 's' : '');
        
        if ($today < $end_date_obj) {
            $remaining = $today->diff($end_date_obj);
            if ($remaining->y > 0) {
                $remaining_time = $remaining->y . ' Year' . ($remaining->y > 1 ? 's' : '');
                if ($remaining->m > 0) {
                    $remaining_time .= ' and ' . $remaining->m . ' Month' . ($remaining->m > 1 ? 's' : '');
                }
            } elseif ($remaining->m > 0) {
                $remaining_time = $remaining->m . ' Month' . ($remaining->m > 1 ? 's' : '');
                if ($remaining->d > 0) {
                    $remaining_time .= ' and ' . $remaining->d . ' Day' . ($remaining->d > 1 ? 's' : '');
                }
            } else {
                $remaining_time = $remaining->d . ' Day' . ($remaining->d > 1 ? 's' : '');
            }
        } else {
            $remaining_time = 'Expired';
        }
    }
}
    ?>

    <div class="content-wrap">
        <div class="profile-container">
            <div class="page-header">
                <div class="page-title">
                    <h1 style="font-size: 18px;"><b>Check MOA Status</b></h1>
                    <br><br>
                    <div class="page-information-container">
                    <div class="page-information">
                        <p class="required-hours"><span>Started Date of Validity</span><span class="colon">:</span> <?php echo !empty($start_date) ? $start_date : 'N/A'; ?></p>
                        <p class="hours-info"><span>End Date of Validity</span><span class="colon">:</span> <?php echo !empty($end_date) ? $end_date : 'N/A'; ?></p>
                        <p class="hours-info"><span>Years of Validity</span><span class="colon">:</span> <?php echo !empty($validity_years) ? $validity_years : 'N/A'; ?></p>
                    </div>
                    <br>
                    <p class="MOA-validity"><span>Remaining time for MOA's Validity</span><span class="colon">:</span> <?php echo !empty($remaining_time) ? $remaining_time : 'N/A'; ?></p>
                    </div>
                    <div class="moa-status-container">
                        <span class="moa-status-label">MOA file</span>
<div class="moa-status-flex">
    <div>
        <div class="moa-document-area" style="width:100%; max-width:500px; height:650px; background:#f5f5f5; border-radius:6px; display:flex; flex-direction:column; align-items:center; justify-content:center; margin-bottom:10px; margin-top:20px; margin-left: 80px;">
            <?php    
 
    if ($company_name) {
        $stmt2 = $conn->prepare("SELECT moa_file FROM company_moa WHERE company_name = ? ORDER BY date_uploaded DESC LIMIT 1");
        $stmt2->execute([$company_name]);
        $moa_row = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($moa_row && !empty($moa_row['moa_file'])) {
            $moa_file_url = '../' . ltrim($moa_row['moa_file'], '/');
            $is_pdf = strtolower(pathinfo($moa_file_url, PATHINFO_EXTENSION)) === 'pdf';
            if ($is_pdf) {
                $local_file_path = realpath(__DIR__ . '/../' . ltrim($moa_row['moa_file'], '/'));
                if ($local_file_path && file_exists($local_file_path)) {
                    $pdf_data = file_get_contents($local_file_path);
                    $pdf_base64 = base64_encode($pdf_data);
                    $pdf_data_url = 'data:application/pdf;base64,' . $pdf_base64;
                }
            }
        }
    }
?>

<?php if (isset($moa_file_url) && isset($is_pdf) && $moa_file_url && $is_pdf): ?>
    <div class="pdf-container" style="width:100%; max-width:400px; height:100vh; margin:0 auto; overflow:auto; background:#f5f5f5; position:relative; border-radius:6px; display:flex; align-items:center; justify-content:center;">
        <canvas id="pdf-canvas" style="display:block; margin:0 auto; max-width:100%; max-height:100%; object-fit:contain;"></canvas>
    </div>
    <div id="pdf-controls" style="margin:15px auto 0 auto; text-align:center; padding:10px 0; max-width:600px;">
        <button id="prev-page" class="btn btn-sm btn-secondary">
            <i class="ti-arrow-left"></i> Previous
        </button>
        <span style="margin:0 15px; font-size:16px; font-weight:bold;">
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
            <?php elseif ($moa_file_url): ?>
                <a href="<?php echo htmlspecialchars($moa_file_url); ?>" target="_blank" style="color: #700000; font-weight: bold;">Download MOA Document</a>
            <?php else: ?>
                <div class="no-document-message" style="color:#800000; text-align:center; width:100%;">No MOA PDF uploaded.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="moa-btn-col">
        <?php if ($moa_file_url && $is_pdf): ?>
            <button class="moa-view-btn" onclick="window.location.href='view_moa.php'">View</button>
            <button class="moa-download-btn" onclick="downloadPDF('<?php echo htmlspecialchars($moa_file_url); ?>')">Download</button>
            <button class="moa-print-btn" onclick="window.open('<?php echo htmlspecialchars($moa_file_url); ?>', '_blank')">Print</button>
        <?php else: ?>
            <button class="moa-view-btn" disabled>View</button>
            <button class="moa-download-btn" disabled>Download</button>
            <button class="moa-print-btn" disabled>Print</button>
        <?php endif; ?>
        <button class="moa-renew-btn">Renew MOA</button>
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

    function downloadPDF(pdfUrl) {

        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = pdfUrl.split('/').pop();
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    function printPDF(pdfUrl) {
        const printWindow = window.open(pdfUrl, '_blank');
        printWindow.onload = function() {
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
            }, 500);
        };
    }
    document.addEventListener('DOMContentLoaded', function () {
        const renewBtn = document.querySelector('.moa-renew-btn');
        if (renewBtn) {
            renewBtn.addEventListener('click', function () {
 
                fetch('check_moa_status.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'expired' || data.status === 'expiring_soon') {
                            
                            Swal.fire({
                                title: 'RENEW',
                                html: `<span class="swal-text-white move-text-up">${data.message}</span>`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#700000',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Proceed',
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
                        } else {
                            Swal.fire({
                                title: 'MOA Still Valid',
                                html: `<span class="swal-text-white">${data.message}</span>`,
                                icon: 'info',
                                confirmButtonColor: '#0c0c9b',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'swal-custom-popup',
                                    icon: 'swal-custom-icon',
                                    title: 'title-color',
                                    confirmButton: 'swal-confirm-proceed'
                                }
                            }).then(() => {
                                window.location.href = 'moa_application_renewal.php';
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while checking MOA status. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#700000'
                        });
                    });
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
