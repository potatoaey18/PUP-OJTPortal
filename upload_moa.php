<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_FILES['moa_file']) || $_FILES['moa_file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error occurred']);
    exit();
}

$file = $_FILES['moa_file'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];
$fileType = $file['type'];

$allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
if (!in_array($fileType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF and Word documents are allowed.']);
    exit();
}

$maxFileSize = 10 * 1024 * 1024;
if ($fileSize > $maxFileSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 10MB.']);
    exit();
}

$uploadDir = __DIR__ . '/moa_files/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit();
    }
}

if (!is_writable($uploadDir)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Upload directory is not writable']);
    exit();
}

$safeFileName = preg_replace("/[^a-zA-Z0-9_.-]/", "_", $fileName);
$filePath = $uploadDir . $safeFileName;

$counter = 1;
$pathInfo = pathinfo($safeFileName);
$baseName = $pathInfo['filename'];
$extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

while (file_exists($filePath)) {
    $filePath = $uploadDir . $baseName . '_' . $counter . $extension;
    $counter++;
}

$newFileName = basename($filePath);

if (move_uploaded_file($fileTmpName, $filePath)) {
    try {
        include '../connection/config.php';
        
        $supervisorId = $_SESSION['auth_user']['supervisor_id'];
        $stmt = $conn->prepare("SELECT company_name, supervisor_email FROM supervisor WHERE id = ?");
        $stmt->execute([$supervisorId]);
        $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$supervisor || empty($supervisor['company_name'])) {
            throw new Exception('Company information not found');
        }
        
        if (empty($supervisor['supervisor_email'])) {
            throw new Exception('Supervisor email not found');
        }
        
        $relativePath = 'supervisor/moa_files/' . $newFileName;
        
        error_log("Saving file with path: " . $relativePath);
        error_log("Full server path: " . $filePath);
        
        error_log("File uploaded to: " . $filePath);
        error_log("Web accessible path: " . $relativePath);
        
        $checkStmt = $conn->prepare("SELECT * FROM company_moa WHERE company_name = ?");
        $checkStmt->execute([$supervisor['company_name']]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        $conn->beginTransaction();
        
        try {
            if ($existingRecord) {
                $stmt = $conn->prepare("UPDATE company_moa SET moa_file = ?, supervisor_email = ?, date_uploaded = NOW() WHERE company_name = ?");
                $stmt->execute([$relativePath, $supervisor['supervisor_email'], $supervisor['company_name']]);
            } else {
                $stmt = $conn->prepare("INSERT INTO company_moa (moa_file, company_name, supervisor_email, date_uploaded) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$relativePath, $supervisor['company_name'], $supervisor['supervisor_email']]);
            }
            
            if (!file_exists($filePath)) {
                throw new Exception('Uploaded file not found on server');
            }
            
            if (!is_readable($filePath)) {
                throw new Exception('Uploaded file is not readable');
            }
            
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'File uploaded successfully',
                'filePath' => $relativePath
            ]);
            
        } catch (Exception $e) {
            $conn->rollBack();
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>
