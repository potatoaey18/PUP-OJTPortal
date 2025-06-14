<?php
session_start();

if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    die('Unauthorized access');
}

$file_path = isset($_GET['file']) ? $_GET['file'] : '';
if (empty($file_path)) die('No file specified');

$relative_file = preg_replace('#^/ojt/supervisor/#', '', $file_path);
$full_path = realpath(__DIR__ . '/' . $relative_file);

$allowed_dir = realpath(__DIR__ . '/evaluation');
if (!$full_path || !file_exists($full_path) || strpos($full_path, $allowed_dir) !== 0) {
    die('Invalid file path or file does not exist');
}

if (mime_content_type($full_path) !== 'application/pdf') {
    die('Invalid file type. Only PDF files can be viewed.');
}

$pdf_base64 = base64_encode(file_get_contents($full_path));
$pdf_data_uri = 'data:application/pdf;base64,' . $pdf_base64;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Print PDF</title>
    <style>
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0; 
            overflow: hidden;
        }
        body { 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            height: 100vh; 
        }

        #pdfViewer {
            width: 100%;
            flex-grow: 1;
            border: none;
        }
    </style>
</head>
<body>

    <iframe id="pdfViewer" src="<?php echo $pdf_data_uri; ?>" type="application/pdf"></iframe>
    <script>
        function printPDF() {
            const iframe = document.getElementById('pdfViewer');
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }

        setTimeout(printPDF, 1000);

        document.getElementById('pdfViewer').onload = function() {
            setTimeout(printPDF, 500);
        };
    </script>
    
</body>
</html>