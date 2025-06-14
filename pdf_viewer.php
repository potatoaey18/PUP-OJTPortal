<?php
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($_GET['file']) . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$file_path = __DIR__ . '/' . $_GET['file'];

if (!file_exists($file_path)) {
    die('File not found');
}

readfile($file_path);
?>
<!DOCTYPE html>
<html>
<head>
    <title>PDF Viewer</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        embed {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <embed type="application/pdf" src="<?php echo htmlspecialchars($_GET['file']); ?>" width="100%" height="100%">
</body>
</html>
