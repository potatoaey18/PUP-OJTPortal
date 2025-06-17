<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

ob_start();

include '../connection/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\php\logs\php_error_log');

session_start();
if (!isset($_SESSION['auth_user']['coordinators_id']) || $_SESSION['auth_user']['coordinators_id'] == 0) {
    header('Location: index.php');
    exit;
}

$coordinatorID = $_SESSION['auth_user']['coordinators_id'];
$stmt = $conn->prepare("SELECT assigned_section FROM coordinators_account WHERE id = ?");
$stmt->execute([$coordinatorID]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data === false || !isset($data['assigned_section'])) {
    die("Error: No assigned section found for coordinator ID $coordinatorID");
}

$assignedSection = $data['assigned_section'];
$stmt = $conn->prepare("SELECT * FROM students_data WHERE stud_section = ?");
$stmt->execute([$assignedSection]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Excel download
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    ob_clean();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'SIS NO.');
    $sheet->setCellValue('B1', 'Section');
    $sheet->setCellValue('C1', 'Full Name');
    $sheet->setCellValue('D1', 'STATUS');

    $row = 2;
    foreach ($students as $trainee) {
        $sheet->setCellValue('A' . $row, $trainee['student_ID'] ?? 'N/A');
        $sheet->setCellValue('B' . $row, $trainee['stud_section'] ?? 'N/A');
        $fullName = trim(($trainee['first_name'] ?? '') . ' ' . ($trainee['middle_name'] ?? '') . ' ' . ($trainee['last_name'] ?? ''));
        $sheet->setCellValue('C' . $row, $fullName ?: 'N/A');
        $sheet->setCellValue('D' . $row, $trainee['ojt_status'] ?? 'N/A');
        $row++;
    }

    $filename = "Trainees_Section_{$assignedSection}.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Handle CSV download
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    ob_clean();
    $filename = "Trainees_Section_{$assignedSection}.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['SIS NO.', 'Section', 'Full Name', 'STATUS']);

    foreach ($students as $trainee) {
        $fullName = trim(($trainee['first_name'] ?? '') . ' ' . ($trainee['middle_name'] ?? '') . ' ' . ($trainee['last_name'] ?? ''));
        fputcsv($output, [
            $trainee['student_ID'] ?? 'N/A',
            $trainee['stud_section'] ?? 'N/A',
            $fullName ?: 'N/A',
            $trainee['ojt_status'] ?? 'N/A'
        ]);
    }

    fclose($output);
    exit;
}

// Handle PDF download
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    ob_clean();

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    $pdf->SetCreator('OJT Web Portal');
    $pdf->SetAuthor('Coordinator');
    $pdf->SetTitle("Trainees Section {$assignedSection}");
    $pdf->SetSubject('Trainee Data');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 15);

    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 12);

    $pdf->Cell(0, 10, "SECTION {$assignedSection}", 0, 1, 'C');
    $pdf->Ln(5);

    $html = '<table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr style="background-color:#700000; color:white;">
                        <th width="20%">SIS NO.</th>
                        <th width="20%">Section</th>
                        <th width="40%">Full Name</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($students as $trainee) {
        $fullName = trim(($trainee['first_name'] ?? '') . ' ' . ($trainee['middle_name'] ?? '') . ' ' . ($trainee['last_name'] ?? ''));
        $html .= '<tr>
                    <td>' . htmlspecialchars($trainee['student_ID'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($trainee['stud_section'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($fullName ?: 'N/A') . '</td>
                    <td>' . htmlspecialchars($trainee['ojt_status'] ?? 'N/A') . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = "Trainees_Section_{$assignedSection}.pdf";
    $pdf->Output($filename, 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Find your H.T.E</title>
    <link rel="shortcut icon" href="images/Picture1.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .top-function { 
            display: flex;
            justify-content: space-between;
         }
        .dl-btn { 
            background-color: #ffc107;
            padding: 8px; border-radius: .5rem;
            color: #333; font-weight: bold;
            border: none; 
            cursor: pointer;
        }
        .dl-btn:hover { 
            background-color: #ffc107;
            padding: 8px; border-radius: .5rem;
            color: #8B0000; 
            font-weight: bold;
            border: none; 
            cursor: pointer;
        }
        .search-cont { 
            display: flex;
            flex-direction: row;
            justify-content: space-around; 
            gap: 10px;
        }
        .search-box {
            width: 100%;
            padding: 8px; 
            border-radius: 4px; 
            border: 2px solid #8B0000; 
        }
        .search-btn {
            padding: 8px 20px; 
            background-color: #8B0000; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .hidden-btn {
            padding: 8px 20px; 
            background-color:rgb(73, 70, 70); 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .table-container { 
            width: 100%; 
            overflow-x: auto; 
            margin-bottom: 20px; 
        }
        .trainee-table { 
            width: 100%; 
            border-collapse: collapse;
         }
        .trainee-table th { 
            background-color: #fff; 
            color: #700000; 
            text-align: center; 
            padding: 20px 50px; 
            min-width: 300px; 
            border: 2px solid #700000; 
            font-weight: 600; 
        }
        .trainee-table th[colspan="5"] { 
            background-color: #700000;
            color: #fff; 
            text-align: center; 
            padding: 20px 50px; 
            border: 2px solid #fff; 
            font-weight: 600; 
        }
        tbody tr td {
            color: #000;
            text-align: center;
            padding: 20px 50px; 
            border: 2px solid #fff; 
            font-weight: 600; 
            border: 2px solid #700000;
        }
        tbody tr:nth-child(odd){
            color: #000;
            text-align: center;
            padding: 20px 50px; 
            border: 2px solid #fff; 
            font-weight: 600; 
            background-color:rgb(221, 218, 218);
            border: 2px solid #700000;
        }
        .view-profile {
            background-color: #8B0000;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .dl-btn-cont{
            display: flex; 
            justify-content: space-between;
            gap: 10px;
        }

        @media print {
        .dl-btn-cont, .search-cont, .view-profile, .sidebar, .navbar {
        display: none !important;
        }
        
    </style>
</head>
<body>
    <?php require_once 'templates/coordinators_navbar.php'; ?>
    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div class="page-header">
                <div class="page-title"><br><h1 style="font-size: 16px;"><b>TRAINEES</b></h1><br><br></div>
            </div>
            <div class="top-function">
                <div class="dl-btn-cont">
                    <button class="dl-btn" id="dl-btn" onclick="toggleDownloadValue()">
                        <i class="fa-solid fa-download"></i>
                        Download
                    </button>
                    <div>
                        <button class="hidden-btn" id="cv-btn" style="display: none;" onclick="window.location.href='?export=csv'"><i class="fa-solid fa-file-csv"></i></button>
                        <button class="hidden-btn" id="ex-btn" style="display: none;" onclick="window.location.href='?export=excel'"><i class="fa-solid fa-file-excel"></i></button>
                        <button class="hidden-btn" id="pdf-btn" style="display: none;" onclick="window.location.href='?export=pdf'"><i class="fa-solid fa-file-pdf"></i></button>
                    </div> 
                </div>
                <div class="search-cont">
                    <input type="text" class="search-box" placeholder="Search trainee..." id="traineeSearch">
                    <button class="search-btn">Search</button>
                </div>
            </div>
            <br><br>
            <div class="table-container">
                <table class="trainee-table">
                    <thead>
                        <tr>
                            <th colspan="5">SECTION <?php echo htmlspecialchars($data['assigned_section'] ?? 'N/A'); ?></th>
                        </tr>
                        <tr>
                            <th>SIS NO.</th>
                            <th>Section</th>
                            <th>Full Name</th>
                            <th>Status</th>
                            <th>View Profile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $trainee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($trainee['student_ID'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($trainee['stud_section'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($trainee['first_name'] ?? ''); ?>
                                        <?php echo htmlspecialchars($trainee['middle_name'] ?? ''); ?>
                                        <?php echo htmlspecialchars($trainee['last_name'] ?? ''); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($trainee['ojt_status'] ?? 'N/A'); ?></td>
                                    <td><button class="view-profile">View Profile</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No students found in section <?php echo htmlspecialchars($data['assigned_section'] ?? 'N/A'); ?>.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <br><br>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>

    <script>
        $(document).ready(function() {
            $('.search-btn').on('click', function() {
                var searchTerm = $('#traineeSearch').val().toLowerCase(); 
                $('.trainee-table tbody tr').each(function() {
                    var fullName = $(this).find('td:eq(2)').text().toLowerCase(); 
                    if (fullName.includes(searchTerm)) {
                        $(this).show(); 
                    } else {
                        $(this).hide(); 
                    }
                });
            });
 
            $('#traineeSearch').on('input', function() {
                if ($(this).val() === '') {
                    $('.trainee-table tbody tr').show(); 
                }
            });
        });
    </script>

    <script>
        let dlValue = false;

        function toggleDownloadValue() {
            dlValue = !dlValue;
            console.log(dlValue);

            const cvDl = document.getElementById("cv-btn");
            const exDl = document.getElementById("ex-btn");
            const pdfDl = document.getElementById("pdf-btn");

            cvDl.style.display = dlValue ? "inline-block" : "none";
            exDl.style.display = dlValue ? "inline-block" : "none";
            pdfDl.style.display = dlValue ? "inline-block" : "none";
        }

        function printTable() {
            window.print()
        }

            $(".view-profile").on("click", function() {
            var studID = $(this).closest("tr").find("td:nth-child(1)").text().trim();
            window.location.href = "view_student_profile.php?student_ID=" + encodeURIComponent(studID);
        });
    </script>
</body> 
</html>