<?php
session_start();
require_once('../connection/config.php');
require_once('fpdf/fpdf.php');

if (!isset($_SESSION['auth_user']) || empty($_SESSION['auth_user']['supervisor_id'])) {
    header('Content-Type: text/plain');
    echo "Error: User not logged in";
    exit;
}

$format = $_POST['format'];
$supervisor_id = $_SESSION['auth_user']['supervisor_id'];

$supervisor_query = "SELECT company_name FROM supervisor WHERE id = ?";
$supervisor_stmt = $conn->prepare($supervisor_query);
$supervisor_stmt->execute([$supervisor_id]);
$supervisor_result = $supervisor_stmt->fetch();

if ($supervisor_result) {
    $company_name = $supervisor_result['company_name'];
    
    $query = "SELECT 
                s.student_ID as intern_id,
                s.stud_course as course,
                s.stud_dept as department,
                CONCAT(s.first_name, ' ', s.middle_name, ' ', s.last_name) as full_name,
                s.ojt_status AS status,
                s.student_ID as sis_no
            FROM students_data s
            LEFT JOIN intern_deployments d ON s.student_ID = d.intern_id
            WHERE s.company = ?
            ORDER BY 
            s.first_name,
            s.middle_name,
            s.last_name,
            s.stud_course";
    
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([$company_name]);
        $records = $stmt->fetchAll();
    } catch (Exception $e) {
        header('Content-Type: text/plain');
        echo "Database error: " . $e->getMessage();
        exit;
    }
} else {
    header('Content-Type: text/plain');
    echo "Error: Supervisor not found";
    exit;
}

if (!isset($records) || empty($records)) {
    header('Content-Type: text/plain');
    echo "No records found";
    exit;
}

switch ($format) {
    case 'CSV':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Applicants List.csv"');
        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['Course', 'Date of Application', 'Full Name', 'Target Hours', 'Status']);
        
        foreach ($records as $record) {
            fputcsv($output, [
                $record['course'],
                date('d/m/Y', strtotime($record['department'])),
                $record['full_name'],
                '120',
                $record['status']
            ]);
        }
        fclose($output);
        exit;
        break;
        
    case 'Excel':
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Applicants List.xls"');
        
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $fp = fopen($tempFile, 'w');
        
        $content = "<table border='1'>";
        $content .= "<tr><th>Course</th><th>Date of Application</th><th>Full Name</th><th>Target Hours</th><th>Status</th></tr>";
        
        foreach ($records as $record) {
            $content .= "<tr>";
            $content .= "<td>" . htmlspecialchars($record['course']) . "</td>";
            $content .= "<td>" . date('d/m/Y', strtotime($record['department'])) . "</td>";
            $content .= "<td>" . htmlspecialchars($record['full_name']) . "</td>";
            $content .= "<td>120</td>";
            $content .= "<td>" . htmlspecialchars($record['status']) . "</td>";
            $content .= "</tr>";
        }
        $content .= "</table>";
        
        fwrite($fp, $content);
        fclose($fp);
        
        readfile($tempFile);
        unlink($tempFile);
        exit;
        break;

    case 'PDF':
        ob_start();
        
        class PDF extends FPDF {
            function Header() {
                $this->SetFont('Arial', 'B', 14);
                $this->SetTextColor(112, 0, 0);
                $this->Cell(0, 10, 'List of Applicants', 0, 1, 'C');
                $this->Ln(10);
                
                $this->SetFillColor(112, 0, 0); 
                $this->SetTextColor(255, 255, 255); 
                $this->SetFont('Arial', 'B', 11);
                $this->SetDrawColor(255, 255, 255); 
                
                $this->Cell(100, 10, 'Course', 1, 0, 'C', true);
                $this->Cell(50, 10, 'Date of Application', 1, 0, 'C', true);
                $this->Cell(60, 10, 'Full Name', 1, 0, 'C', true);
                $this->Cell(50, 10, 'Target Hours', 1, 1, 'C', true);
            }
            
            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(112, 0, 0);
                $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
            }
        }

        $pdf = new PDF('L', 'mm', 'A4');
        $pdf->SetMargins(17.5, 25, 25);
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', '', 10);
        
        $pdf->SetDrawColor(112, 0, 0); 
        $pdf->SetTextColor(0, 0, 0); 
        
        foreach ($records as $i => $record) {
    
            if ($i % 2 == 1) {
                $pdf->SetFillColor(189, 189, 189); 
            } else {
                $pdf->SetFillColor(255, 255, 255); 
            }
            
            $pdf->Cell(100, 10, $record['course'], 1, 0, 'C', true);
            $pdf->Cell(50, 10, date('d/m/Y', strtotime($record['department'])), 1, 0, 'C', true);
            $pdf->Cell(60, 10, $record['full_name'], 1, 0, 'C', true);
            $pdf->Cell(50, 10, '120', 1, 1, 'C', true);
        }
        
        $pdf->Output('D', 'Applicants List.pdf');
        exit;
        break;
}
?>
