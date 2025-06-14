<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if($_SESSION['auth_user']['supervisor_id']==0){
    echo"<script>window.location.href='index.php'</script>";
}

// Get the current supervisor's company name
$supervisor_id = $_SESSION['auth_user']['supervisor_id'];
$supervisor_query = "SELECT company_name FROM supervisor WHERE id = ?";
$supervisor_stmt = $conn->prepare($supervisor_query);
$supervisor_stmt->execute([$supervisor_id]);
$supervisor_result = $supervisor_stmt->fetch();

if (!$supervisor_result) {
    echo "<script>alert('Failed to retrieve supervisor information'); window.location.href='index.php';</script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OJT Web Portal: Weekly Accomplishment</title>
    <!-- ================= Favicon ================== -->
    <link rel="shortcut icon" href="images/Picture1.png">
    
    <!-- Common -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <!-- SweetAlert2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            background-color: #F1F1F1;
        }

        .content-wrap {
            margin-left: 19.5rem;
            margin-top: 7rem;
            width: calc(100vw - 20rem);
            height: calc(100vh - 7rem);
            padding: 0;
            box-sizing: border-box;
            overflow-y: auto;
            background-color: #Ffffff;
        }

        .page-container {
            padding: 2rem;
        }

        .back-button {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
            color: #333;
            margin-bottom: 2rem;
            font-size: 1rem;
            margin-left: -10px;
        }

        .back-button:hover {
            color: #700000;
        }

        .page-title {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #333;
        }

        .table-container {
            overflow-x: auto;
            max-height: calc(100vh - 22rem);
            border-radius: 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin-bottom: 3rem;
        }

        .table {
            margin-bottom: 0;
            min-width: 140%;
            background-color: #fff;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-header, .table th {
            background-color: #700000;
            color: white;
            text-align: center;
            font-size: 1rem;
        }

        .table-header {
            padding: 0.4rem;
            position: sticky;
            top: 0;
            z-index: 3;
        }

        .table th {
            white-space: nowrap;
            padding: 1.5rem;
        }

        .table thead tr {
            width: 100%;
        }

        .table thead th {
            padding: 0;
        }

        .table tr.column-headers td {
            font-weight: bold;
            background-color: #fff;
            color: #700000;
            font-size: 1.1rem !important;
        }

        .table tr.data-row:nth-child(even) {
            background-color:rgb(189, 189, 189);
        }

        .table tr.data-row:nth-child(odd) {
            background-color:rgb(255, 255, 255);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table td {
    border-right: 1px solid #700000;
    border-bottom: 1px solid #700000;
    height: 50px;
    padding: 0.8rem 4rem;
    white-space: nowrap;
    text-align: center;
    vertical-align: middle;
    color: #000000;
    font-size: 1rem;
}

.table td:nth-child(1) {
    padding-left: 0;
    padding-right: 0;
}

        .table td:last-child {
            border-right: none;
            min-width: 200px;
        }

        .table td:nth-child(1) { /* Assigned Department */
    min-width: 20px;
}
.table td:nth-child(2) { /* SIS No. */
    min-width: 70px;
    padding-left: 0.9rem;
    padding-right: 0.9rem;
}
.table td:nth-child(3) { /* Full Name */
    min-width: 100px;
    padding-left: 4rem;
    padding-right: 4rem;
}
.table td:nth-child(4) { /* Status */
    min-width: 70px;
    padding-left: 0.7rem;
    padding-right: 0.7rem;
}
.table td:nth-child(6), .table th:nth-child(6) { /* View Accomplishment */
    min-width: 10px;
    max-width: 900px;
    width: 8%;
    padding-left: 3rem;
    padding-right: 3rem;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
.table td:nth-child(7), /* View Profile column */
.table th:nth-child(7) {
    text-align: center !important;
    vertical-align: middle !important;
}
.table td:nth-child(7) {
    min-width: 50px;
    padding-left: 0rem;
    padding-right: 0rem;
}

.table tr.column-headers td:nth-child(7),
.table th:nth-child(7) {
    text-align: center !important;
    justify-content: center;
    align-items: center;
}

        .table td {
            vertical-align: middle !important;
        }

        .action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
            gap: 48px;
            padding: 0;
            margin: 0 auto;
            width: fit-content;
            min-height: 70px;
        }

        .view-profile-btn, .drop-btn, .complete-btn {
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.9rem;
            color: white !important;
            background-color: #700000;
            min-width: 100px;
            padding: 10px 20px;
            line-height: 1.5;
            display: inline-block;
            width: auto;
            vertical-align: middle;
            text-align: center;
        }

        .view-profile-btn {
            padding: 10px 15px !important;
        }

        .drop-btn:hover {
            background-color: #900000;
        }


        .view-dtr-btn {
    background-color: #700000;
    color: white !important;
    border: none;
            padding: 0.30rem 1.5rem;
            border-radius: 12px;
    cursor: pointer;
            font-size: 0.8rem;
            display: inline-block;
            width: 100%;
            max-width: 110px;
    text-decoration: none;
}



        .view-dtr-btn:active {
            transform: scale(0.98);
        }

        .table-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            width: 100%;
        }

        .download-container {
            position: relative;
            display: inline-block;
        }

        .download-btn {
            background-color: #ffc107;
            color: #000;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: bold;
        }

        .btn-icon {
            width: 0.8rem;
            height: 0.8rem;
            object-fit: contain;
        }

        .download-options {
            display: none;
            position: absolute;
            left: 100%;
            top: -5px;
            margin-left: 0.5rem;
            white-space: nowrap;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            padding: 0.25rem;
            z-index: 1000;
        }

        .download-options.show {
            display: flex;
            gap: 0.25rem;
        }

        .option-btn {
            background-color: #e9ecef;
            color: #000;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .option-btn:hover {
            background-color: #dee2e6;
        }

        .search-container {
            display: flex;
            align-items: stretch;
        }

        .search-input-wrapper {
            position: relative;
            flex-grow: 1;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0.9rem;
            height: 0.9rem;
            opacity: 0.5;
        }

        .search-input {
            padding: 0.5rem 0.75rem 0.5rem 2.5rem;
            border: 1px solid #700000;
            border-right: none;
            width: 400px;
            font-size: 0.9rem;
            outline: none;
        }

        .search-input:focus {
            border-color: #700000;
            box-shadow: 0 0 0 2px rgba(112, 0, 0, 0.2);
        }

        .search-btn {
            background-color: #700000;
            color: white !important;
            border: none;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            font-size: 0.9rem;
        }

    .swal2-success {
    color: white !important;
}
.swal2-success-line-tip {
    background-color: white !important;
}
.swal2-success-line-long {
    background-color: white !important;
}

.swal-custom-popup {
    padding-top: 40px !important;
    background-color: #700000 !important;
}
.swal-custom-icon {
    position: relative !important;
    top: -25% !important;
    transform: translateY(-50%) !important;
    border: 3px solid #ffc107 !important;
    margin-top: -20px !important;
    animation: animate 0.5s ease-in-out !important;
}
.swal2-icon.animate,
.swal-custom-icon.animate, .swal2-icon.swal2-question.animate {
    animation: animate 0.5s ease-in-out !important;
}
.swal2-icon {
    position: relative;
    top: -25%;
    transition: none !important;
    background-color: #700000 !important;
}
.swal2-icon.swal2-warning {
    margin-top: -20px;
}

.swal2-icon.swal2-question::before {
color: #ffc107 !important;
animation: animate 0.5s ease-in-out;
display: inline-block;
}

.swal2-icon.swal2-info, .swal2-icon.swal2-question {
border-color: #ffc107 !important; 
color: #ffc107 !important; 
}

.swal2-icon.swal2-info .swal2-icon-content {
color: #ffc107 !important; 
}
.swal2-popup {
    padding: 40px 30px !important;
    border-radius: 40px !important;
}
.swal-button--wide {
    width: 150px !important;
    padding: 12px 20px !important;
    margin: 0 20px !important;
    color: white !important;
    font-weight: bold !important;
}
.swal-button--wide.swal-button--confirm {
    background-color: #0c0c9b !important;
}
.swal-button--wide.swal-button--cancel {
    background-color: #700000 !important;
}
.swal2-input::placeholder {
    color: #666 !important;
}
@keyframes animate {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
    100% {
        transform: translateY(0);
    }
}
.animate {
    animation: animate 0.5s ease-in-out;
}
.swal2-icon-text {
    color: #ffc107 !important;
}
.swal-white-bg {
    background-color: #fff !important;
}

.action-buttons button {
    color: white !important;
}
.action-buttons button:hover {
    color: white !important;
}

.swal2-title {
    color: white !important;
}
.swal2-content {
    color: white !important;
}

.swal-edit-title {
    color: black !important;
}
.swal-edit-content {
    color: black !important;
}

.swal-form p, .swal-form label {
    color: inherit !important;
}

.swal-text-white {
    color: white;
}

.move-text-up {
    position: relative;
    top: -25px;
    z-index: 2;
    display: inline-block;
    padding: 5px 0;
}

.move-title-up {
    position: relative;
    top: -10px;
    z-index: 1;
}

.swal-confirm-proceed, .swal-cancel-proceed {
    background-color: white !important;
    color: black !important;
}

.swal-confirm-proceed:hover, .swal-cancel-proceed:hover {
    background-color: #ffc107 !important;
}

.student-name-highlight {
    color: #ffc107 !important;
}

.student-name-highlight1 {
    color: #0c0c9b !important;
}

.edit-btn-wide {
    width: 160px !important;
    padding: 10px 20px !important;
    font-size: 14px !important;
    font-weight: bold !important;
    border-radius: 8px !important;
    color: white !important;
}

.edit-btn-wide:hover {
    filter: none !important;
    color: white !important;
}

.fixed-header {
    position: sticky;
    top: 0;
    z-index: 20;
    background: #fff;
    padding: 1.5rem 2rem 0.5rem 2rem;

}
.header-separator {
    height: 16px;
    margin-bottom: -20px;
}
.table-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    margin-top: 10px;
}
.download-container, .search-container {
    display: flex;
    align-items: center;
}

</style>
</head>

<body>

    <?php include('templates/supervisor_navbar.php'); ?>


    <div class="content-wrap">
        <div class="fixed-header">
        <a href="javascript:history.back()" class="back-button">
            <img src="images/less-than.png" alt="Back" style="width: 30px; height: 30px; color: #333;">
            Back
        </a>
        <div class="page-title">
            <h1 style="font-size: 16px;"><b>Weekly Accomplishment</b></h1>
        </div>
        <div class="table-actions">
            <div class="download-container">
                <button class="download-btn">
                    <img src="images/download.png" alt="Download" class="btn-icon">
                    Download
                </button>
                <div class="download-options">
                    <button class="option-btn">CSV</button>
                    <button class="option-btn">Excel</button>
                    <button class="option-btn">PDF</button>
                    <button class="option-btn">Print</button>
                </div>
            </div>
            <div class="search-container">
                <div class="search-input-wrapper">
                    <img src="images/search1.png" alt="Search" class="search-icon">
                    <input type="text" class="search-input" placeholder="Search here..." style="border-radius: 6px 0 0 6px; padding-left: 2.2rem;">
                </div>
                <button class="search-btn" style="border-radius: 0 6px 6px 0;">Search</button>
            </div>
        </div>
        <div class="header-separator"></div>
    </div>
    <div class="page-container" style="padding-top: 0;">

    <div id="no-results-message" style="
    display: none;
    position: absolute;
    top: 45%;
    left: 55%;
    transform: translate(-50%, -50%);
    color: #888;
    font-size: 1.5rem;
    text-align: center;
    pointer-events: none;
">
  No Results Found
</div>



            <?php
$dept_stmt = $conn->prepare("SELECT DISTINCT department FROM intern_deployments WHERE department IS NOT NULL AND department != ''");
$dept_stmt->execute();
$raw_departments = $dept_stmt->fetchAll(PDO::FETCH_COLUMN);


$normalized_departments = [];
$dept_display_names = [];
foreach ($raw_departments as $dept) {
    $norm = strtoupper(trim($dept));
    if (!isset($normalized_departments[$norm])) {
        $normalized_departments[$norm] = true;
        $trimmed = trim($dept);


        $override = [
            'IT DEPARTMENT' => 'IT Department',
            'HR' => 'HR',
            'ICT' => 'ICT',
        ];

        if (isset($override[$norm])) {
            $dept_display_names[$norm] = $override[$norm];
        } elseif ($trimmed === strtolower($trimmed)) {
            $dept_display_names[$norm] = ucwords($trimmed);
        } else {
            $dept_display_names[$norm] = $trimmed;
        }
    }
}

$departments = array_keys($normalized_departments);


$company_name = isset($supervisor_result['company_name']) ? $supervisor_result['company_name'] : '';
$stud_stmt = $conn->prepare("SELECT * FROM students_data WHERE ojt_status = 'Deployed' AND company = ?");
$stud_stmt->execute([$company_name]);
$students = $stud_stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($departments)) {
    echo '<div style="display: flex; align-items: center; justify-content: center; height: 45vh; width: 100%;"><span style="font-size: 1.3rem; color: #888; text-align: center;">No tables to display.<br>Deploy a student for a table to appear.</span></div>';
}
foreach ($departments as $normalized_department) {
    echo '<div class="department-table-wrapper table-container">';
    echo '<table class="department-table table">';
    echo '<thead>';
    echo '<tr><th colspan="7"><div class="table-header">' . htmlspecialchars($dept_display_names[$normalized_department]) . '</div></th></tr>';
    echo '</thead>';
    echo '<tbody>';
    echo '<tr class="column-headers">';
    echo '<td>Assigned Department</td>';
    echo '<td>SIS No.</td>';
    echo '<td>Full Name</td>';
    echo '<td>Status</td>';
    echo '<td>OJT Adviser</td>';
    echo '<td>View Accomplishment</td>';
    echo '<td>View Profile</td>';
    echo '</tr>';
                $found = false;
foreach ($students as $student) {
    if ($student['ojt_status'] !== 'Deployed') continue;
    $deploy_stmt = $conn->prepare("SELECT department FROM intern_deployments WHERE intern_id = ? ORDER BY deployment_date DESC LIMIT 1");
    $deploy_stmt->execute([$student['student_ID']]);
    $deployment = $deploy_stmt->fetch(PDO::FETCH_ASSOC);
    if ($deployment && strtoupper(trim($deployment['department'])) === $normalized_department) {
        $found = true;
        $sis_no = htmlspecialchars($student['student_ID']);
        $full_name = htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . $student['middle_name']);
        $status = htmlspecialchars($student['ojt_status']);
        $ojt_adviser = isset($student['ojt_adviser']) && $student['ojt_adviser'] !== '' ? htmlspecialchars($student['ojt_adviser']) : 'N/A';
$raw_dept = $deployment ? trim($deployment['department']) : '-';
$norm = strtoupper($raw_dept);
$override = [
    'IT DEPARTMENT' => 'IT Department',
    'HR' => 'HR',
    'ICT' => 'ICT',
];
if (isset($override[$norm])) {
    $displayDept = $override[$norm];
} elseif ($raw_dept === strtolower($raw_dept)) {
    $displayDept = ucwords($raw_dept);
} else {
    $displayDept = $raw_dept;
}
$assigned_dept = htmlspecialchars($displayDept);
echo '<tr class="data-row">';
echo '<td>' . $assigned_dept . '</td>';
echo '<td>' . $sis_no . '</td>';
echo '<td>' . $full_name . '</td>';
echo '<td>' . $status . '</td>';
echo '<td>' . $ojt_adviser . '</td>';
echo '<td><a href="view_accomplishment.php?student_id=' . urlencode($student['student_ID']) . '" class="view-dtr-btn" data-student-id="' . htmlspecialchars($student['student_ID']) . '">View</a></td>';
echo '<td><a href="view_student_profile.php?intern_id=' . urlencode($student['student_ID']) . '" class="view-profile-btn">View Profile</a></td>';
echo '</tr>';
    }
}
                if (!$found) {
                    echo '<tr><td colspan="6" style="text-align:center;color:#aaa;">No students in this department.</td></tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            }
            ?>

    <!-- Scripts -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/filter_tables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.drop-btn').forEach(button => {
                button.addEventListener('click', function () {
        const row = this.closest('tr');
        const fullName = row.querySelector('td:nth-child(3)').textContent;
        const internId = this.getAttribute('data-intern-id');

        Swal.fire({
            title: 'Checking status...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            background: '#700000',
            customClass: {
                popup: 'swal-checking-status'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('check_student_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `intern_id=${encodeURIComponent(internId)}`
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();

            if (data.status === 'Dropped') {
                Swal.fire({
                    title: 'Already Dropped',
                    html: `<span class="swal-text-white move-text-up">This student is already dropped.<br><br><span style="font-size: 12px; font-style: italic;">(Click anywhere to continue)</span></span>`,
                    icon: 'info',
                    showConfirmButton: false,
                    background: '#0c0c9b',
                    color: 'white',
                    customClass: {
                        popup: 'swal-custom-popup',
                        icon: 'swal-custom-icon',
                        title: 'move-title-up',
                    },
                    allowOutsideClick: true,
                    allowEnterKey: true,
                    allowEscapeKey: true,
                    didOpen: () => {
                        const popup = Swal.getPopup();
                        popup.addEventListener('click', () => Swal.close());
                        document.addEventListener('keydown', function enterHandler(e) {
                            if (e.key === 'Enter') {
                                Swal.close();
                                document.removeEventListener('keydown', enterHandler);
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    html: `<span class="swal-text-white move-text-up">Are you sure you want to drop <span class="student-name-highlight">${fullName}</span>?</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#700000',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-custom-popup',
                        icon: 'swal-custom-icon',
                        title: 'move-title-up',
                        confirmButton: 'swal-confirm-proceed',
                        cancelButton: 'swal-cancel-proceed'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                   
                        Swal.fire({
                            title: '<span class="move-title-up" style="font-size: 20px; color: #000000;">Drop Intern</span>',
                            html: `
                                <div style="padding: 12px 20px 20px 20px; border-radius: 12px; background-color: #f8f9fa; margin-top: -18px;">
                                    <div class="form-group" style="margin-bottom: 25px;">
                                        <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold; font-size: 14px;">
                                            Please provide a reason for dropping <span class="student-name-highlight1">${fullName}</span>: <span style="color: #700000;">*</span>
                                        </label>
                                        <div style="display: flex; justify-content: center;">
    <textarea id="drop-reason" class="swal2-textarea" placeholder="Ex. Irresponsible with his/her tasks."
        style="width: 100%; min-height: 45px; margin: 0; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background-color: rgb(228, 228, 228); color: #000000; resize: vertical;"
        required
        onfocus="this.style.color='#000000'" oninput="this.style.color='#000000'"
    ></textarea>
</div>
<div class="form-group" style="margin-bottom: 20px; margin-top: 10px;">
    <label for="evidence-file" style="display: block; text-align: left; margin-top: 30px; margin-bottom: -5px; font-weight: bold; font-size: 14px;">Upload Evidence Document: <span style="color: #700000;">*</span></label>
    <input type="file" id="evidence-file" class="swal2-file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx,.zip" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; background-color: #f4f4f4; font-size: 14px; cursor: pointer;" required />
    <small style="color: #666;">Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
</div>
<style>
.swal2-textarea::placeholder {
    color: #222222 !important;
    opacity: 0.6;
}
.move-title-up {
    margin-top: -15px !important;
    display: block;
    margin-bottom: 30px;
}
</style>
                                    </div>
                                    <div id="formWarning" style="display: none; color: #700000; font-size: 13px; margin-top: 15px; text-align: left;">
                                        <img src="images/warning-sign.png" alt="Warning" style="width: 20px; height: 20px; margin-top: -2px; margin-right: 2px; vertical-align: middle;">
                                        Please enter a reason before dropping.
                                    </div>
                                </div>
                            `,

                            confirmButtonColor: '#700000',
                            confirmButtonText: 'Drop',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel',
                            width: '600px',
                            customClass: {
                                confirmButton: 'swal-button--wide',
                                cancelButton: 'swal-button--wide',
                                popup: 'swal-white-bg'
                            },
                            preConfirm: () => {
    const reason = document.getElementById('drop-reason').value.trim();
    const fileInput = document.getElementById('evidence-file');
    const file = fileInput && fileInput.files[0];
    let warning = '';
    if (!reason) {
        warning = 'Please enter a reason.';
    } else if (!file) {
        warning = 'Please upload an evidence document.';
    }
    if (warning) {
        document.getElementById('formWarning').textContent = warning;
        document.getElementById('formWarning').style.display = 'block';
        return false;
    }
    document.getElementById('formWarning').style.display = 'none';
    return { reason, file };
}
                        }).then((formResult) => {
                            if (formResult.isConfirmed && formResult.value) {
    const formData = new FormData();
    formData.append('intern_id', internId);
    formData.append('reason', formResult.value.reason);
    formData.append('evidence_file', formResult.value.file);
    fetch('drop_intern.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                                            icon: 'success',
                                            title: 'Student Dropped',
                                            html: '<p style="font-size: 12px; color: white; margin-top: -15px; opacity: 0.5; font-style: italic;">(Click anywhere to continue)</p>',
                                            showConfirmButton: false,
                                            background: '#0c0c9b',
                                            color: 'white',
                                            allowOutsideClick: true,
                                            allowEnterKey: true,
                                            allowEscapeKey: true,
                                            didOpen: () => {
                                                const popup = Swal.getPopup();
                                                popup.addEventListener('click', () => {
                                                    Swal.close();
                                                });
                                                document.addEventListener('keydown', function enterHandler(e) {
                                                    if (e.key === 'Enter') {
                                                        Swal.close();
                                                        document.removeEventListener('keydown', enterHandler);
                                                    }
                                                });
                                            }
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Failed to drop intern.', 'error');
                                });
                            }
                        });
                    }
                });
            }
        });
    });
});

    
            $('.complete-btn').click(function() {
                const internId = $(this).data('intern-id');
                const fullName = $(this).closest('tr').find('td:nth-child(3)').text();

                Swal.fire({
    html: `<span class="swal-text-white move-text-up">Are you sure that <span class="student-name-highlight">${fullName}</span> has completed his/her working hours?</span>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Confirm',
    cancelButtonText: 'Cancel',
    background: '#0c0c9b',
    color: 'white',
    customClass: {
        popup: 'swal-custom-popup',
        icon: 'swal-custom-icon',
        title: 'move-title-up',
        confirmButton: 'swal-confirm-proceed',
        cancelButton: 'swal-cancel-proceed'
    }
}).then((result) => {
    if (result.isConfirmed) {
        $.ajax({
            url: 'complete_intern.php',
            type: 'POST',
            data: {
                intern_id: internId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
    icon: 'success',
    title: 'Marked as Completed',
    html: '<p style="font-size: 12px; color: white; margin-top: -15px; opacity: 0.5; font-style: italic;">(Click anywhere to continue)</p>',
    showConfirmButton: false,
    background: '#0c0c9b',
    color: 'white',
    allowOutsideClick: true,
    allowEnterKey: true,
    allowEscapeKey: true,
    didOpen: () => {
        const popup = Swal.getPopup();
        popup.addEventListener('click', () => {
            Swal.close();
        });
        document.addEventListener('keydown', function enterHandler(e) {
            if (e.key === 'Enter') {
                Swal.close();
                document.removeEventListener('keydown', enterHandler);
            }
        });
    }
}).then(() => {
    location.reload();
});
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to complete intern.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'An error occurred while completing the intern.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    $(document).on('click', '.view-profile-btn', function() {
        const internId = $(this).data('intern-id');
        window.location.href = 'view_student_profile.php?intern_id=' + encodeURIComponent(internId);
    });

    $(document).on('click', '.view-dtr-btn', function(e) {
    e.preventDefault();
    const studentId = $(this).data('student-id');
    if (studentId) {
        window.location.href = 'view_accomplishment.php?student_id=' + encodeURIComponent(studentId);
    } else {
        alert('Student ID not found.');
    }
});

});

    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.querySelector('.search-input');
    const searchBtn         = document.querySelector('.search-btn');
    const noResultsMessage  = document.getElementById('no-results-message');
    const downloadOptions   = document.querySelectorAll('.option-btn');
    const downloadContainer = document.querySelector('.download-container');
    const downloadBtn       = document.querySelector('.download-btn');
    const downloadOpts      = document.querySelector('.download-options');

    function filterTables() {
        const term = searchInput.value.trim().toLowerCase();
        const tables = document.querySelectorAll('.department-table-wrapper');
        let visibleCount = 0;

        tables.forEach(wrapper => {
            let rowMatches = false;
            wrapper.querySelectorAll('tr.data-row').forEach(row => {
                const match = row.textContent.toLowerCase().includes(term);
                row.style.display = match ? '' : 'none';
                if (match) rowMatches = true;
            });

            wrapper.style.display = rowMatches ? '' : 'none';
            if (rowMatches) visibleCount++;
        });

        noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterTables);
    searchBtn.addEventListener('click', filterTables);

    downloadOptions.forEach(option => {
        option.addEventListener('click', function () {
            const format = this.textContent;
            downloadOpts.classList.remove('show');

            if (format === 'Print') {
                window.print();
            } else {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'download_table.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'format';
                input.value = format;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        });
    });

    downloadBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        downloadOpts.classList.toggle('show');
    });

    document.addEventListener('click', function (event) {
        if (!downloadContainer.contains(event.target)) {
            downloadOpts.classList.remove('show');
        }
    });
});
</script>

</body>
</html>