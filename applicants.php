<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if($_SESSION['auth_user']['supervisor_id']==0){
    echo"<script>window.location.href='index.php'</script>";
}


$supervisor_id = $_SESSION['auth_user']['supervisor_id'];
$supervisor_query = "SELECT company_name FROM supervisor WHERE id = ?";
$supervisor_stmt = $conn->prepare($supervisor_query);
$supervisor_stmt->execute([$supervisor_id]);
$supervisor_result = $supervisor_stmt->fetch();

if ($supervisor_result) {
    $company_name = $supervisor_result['company_name'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OJT Web Portal: Daily Time Record</title>
    
    <link rel="shortcut icon" href="images/Picture1.png">
    
    
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">

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
            height: calc(100vh - 6rem);
            padding: 0;
            box-sizing: border-box;
            overflow: hidden;
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
        }

        .table {
            margin-bottom: 0;
            min-width: 100%;
            background-color: #fff;
            border-collapse: separate;
            border-spacing: 0;
            
           
        }

        .table th {
            background-color: #700000;
            color: white;
            white-space: nowrap;
            padding: 1.5rem;
            text-align: center;
            font-size: 1rem;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .table-header {
            background-color: #700000;
            color: white;
            padding: 0.4rem;
            text-align: center;
            font-weight: bold;
            font-size: 1rem;
            width: 100%;
            margin: 0 auto;
            position: sticky;
            top: 0;
            z-index: 3;
        }

        .table thead tr {
            width: 100%;
        }

        .table thead th {
            padding: 0;
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

        .table td:last-child {
            border-right: none;
        }

        .table tr.column-headers td {
            font-weight: bold;
            background-color: #fff;
            color: #700000;
            font-size: 1.1rem !important;
        }

        .table tr.data-row.even-row {
            background-color:rgb(189, 189, 189);
        }

        .table tr.data-row.odd-row {
            background-color:rgb(255, 255, 255);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table td:nth-child(1) { 
            min-width: 600px;
        }

        .table td:nth-child(3) { 
            min-width: 400px;
        }

        .table td:nth-child(4) { 
            min-width: 150px;
        }

        .table td:last-child {
            text-align: center;
            min-width: 600px;
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            gap: 40px;
            justify-content: center;
            
        }

        .view-profile-btn, .deploy-btn, .dropped-btn {
            
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.9rem;
            color: white;
            background-color: #700000;
            min-width: 80px;
        }

        .view-profile-btn {
            padding: 5px 15px;
        }

        .deploy-btn {
            padding: 5px 33px;
        }

        .dropped-btn {
            padding: 5px 35px;
        }

        .download-container {
            position: relative;
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
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .option-btn:hover {
            background-color: #dee2e6;
        }

        .table-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            width: 100%;
        }

        .search-container {
            display: flex;
            align-items: stretch;
            width: 400px;
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
            width: 100%;
            padding: 0.5rem 0.75rem 0.5rem 2.5rem;
            border: 1px solid #700000;
            border-right: none;
            border-radius: 6px 0 0 6px;
            outline: none;
        }

        .search-btn {
            background-color: #700000;
            color: white;
            border: 1px solid #700000;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            border-radius: 0 6px 6px 0;
        }

       
    </style>
    <style>
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
        
        .swal-custom-icon-top {
    position: absolute;
    top: -20%;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    background: #700000 !important;
    border: 4px solid #ffc107 !important;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.swal2-icon.success {
    background: none !important;
    box-shadow: none !important;
}
.swal2-icon.swal2-success [class^='swal2-success-circular-line'] {
    background: none !important;
}
.swal2-icon.swal2-success .swal2-success-ring {
    stroke: #ffc107 !important;
    stroke-width: 4;
    fill: none !important;
}
.swal2-icon.swal2-success .swal2-success-fix {
    background: none !important;
}
.swal2-icon.swal2-success .swal2-success-line-tip,
.swal2-icon.swal2-success .swal2-success-line-long {
    background: #ffc107 !important;
}
.swal2-icon.swal2-success {
    width: 80px !important;
    height: 80px !important;
    min-width: 80px !important;
    min-height: 80px !important;
    border-radius: 50%;
    padding: 0;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
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

        
        .swal-confirm-proceed, .swal-cancel-proceed, .swal-confirm-proceed1, .swal-cancel-proceed1 {
            background-color: white !important;
            color: black !important;
        }
        
        .swal-confirm-proceed:hover, .swal-cancel-proceed:hover, .swal-confirm-proceed1:hover, .swal-cancel-proceed1:hover {
            background-color: #ffc107 !important;
        }

        .swal-confirm-proceed1, .swal-cancel-proceed1{
            padding: 10px 30px !important;
        }

        
        .student-name-highlight {
            color: #ffc107 !important;
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

    </style>
</head>

<body>
    <?php include('templates/supervisor_navbar.php'); ?>
    
    <div class="content-wrap">
        <div class="page-container">
            <a href="javascript:history.back()" class="back-button">
                <img src="images/less-than.png" alt="Back" style="width: 30px; height: 30px; color: #333;">
                Back
            </a>
            <div class="page-title">
            <h1 style="font-size: 16px;"><b>Applicants</b></h1>
            </div>

            <br>
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
                        <input type="text" class="search-input" placeholder="Search here...">
                    </div>
                    <button class="search-btn">Search</button>
                </div>
            </div>

            <div class="table-container">
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th colspan="6">
                                <div class="table-header">Applicants</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="column-headers">
                            <td style="width: 60%;">Course</td>
                            <td style="width: 20%;">Date of Application</td>
                            <td style="width: 30%;">Full Name</td>
                            <td style="width: 20%;">Target Hours</td>
                            <td style="width: 60%; text-align: center;">Action</td>
                        </tr>
                        <?php
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
                                    s.stud_course
                                "; 
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$company_name]);
                            $result = $stmt->fetchAll();

                            if (count($result) > 0) {
                                $counter = 0;
                                foreach ($result as $row) {
                                    $rowClass = ($counter % 2 == 0) ? 'even-row' : 'odd-row';
                                    echo "<tr class='data-row {$rowClass}'>";
                                    $counter++;
                                    echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                                    echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['department']))) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars('120') . "</td>";
                                    echo "<td>  
                                        <div class='action-buttons'>
                                            <button class='view-profile-btn' data-intern-id='" . htmlspecialchars($row['intern_id']) . "'>View Profile</button>
                                            <button class='deploy-btn' data-intern-id='" . htmlspecialchars($row['intern_id']) . "'>Deploy</button>   
                                            <button class='dropped-btn' data-intern-id='" . htmlspecialchars($row['intern_id']) . "'>Reject</button>
                                        </div>
                                    </td>";
                                    echo "</tr>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const searchBtn = document.querySelector('.search-btn');
            const downloadOptions = document.querySelectorAll('.option-btn');
            const downloadContainer = document.querySelector('.download-container');
            const downloadBtn = document.querySelector('.download-btn');
            const downloadOpts = document.querySelector('.download-options');
            
            function showDeploymentForm(internId, department = '', supervisor = '', hours = '', mode = 'new') {
                return new Promise((resolve) => {
                    Swal.fire({
                        html: `
                            <div style="padding: 20px; border-radius: 12px; background-color: #f8f9fa;">
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <p style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold; font-size: 14px;">In which department will the intern be designated?</p>
                                    <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold; font-size: 14px;">Please Specify: <span style="color: #700000;">*</span></label>
                                    <input type="text" id="department" class="swal2-input" placeholder="Ex. IT Department" style="width: 100%; margin: 0; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background-color:rgb(228, 228, 228); color: #000000;" value="${department}" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold; font-size: 14px;">Assigned Supervisor: <span style="color: #700000;">*</span></label>
                                    <input type="text" id="supervisor" class="swal2-input" placeholder="Ex. Juan Dela Cruz" style="width: 100%; margin: 0; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background-color:rgb(228, 228, 228); color: #000000;" value="${supervisor}" required>
                                </div>
                                <div class="form-group">
                                    <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold; font-size: 14px;">Required Training Hours: <span style="color: #700000;">*</span></label>
                                    <input type="text" id="hours" name="hours" class="swal2-input" placeholder="Ex. 300" style="width: 100%; margin: 0; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background-color:rgb(228, 228, 228); color: #000000;" value="${hours}" required>
                                </div>
                                <div id="formWarning" style="display: none; color: #700000; font-size: 13px; margin-top: 15px; text-align: left;">
                                    <svg style="display: none;" width="0" height="0">
                                        <filter id="warningColor">
                                            <feColorMatrix type="matrix" values="0.2126 0.7152 0.0722 0 0
                                                0.2126 0.7152 0.0722 0 0
                                                0.2126 0.7152 0.0722 0 0
                                                0 0 0 1 0"/>
                                            <feColorMatrix type="matrix" values="1 0 0 0 0
                                                0 1 0 0 0
                                                0 0 1 0 0
                                                0 0 0 1 0"/>
                                            <feColorMatrix type="matrix" values="0.439215686 0 0 0 0.439215686
                                                0 0 0 0 0
                                                0 0 0 0 0
                                                0 0 0 1 0"/>
                                        </filter>
                                    </svg>
                                    <img src="images/warning-sign.png" alt="Warning" style="width: 20px; height: 20px; margin-top: -2px; margin-right: 2px; vertical-align: middle; filter: url(#warningColor);">
                                    Please fill in all required fields before deploying.
                                </div>
                            </div>
                        `,
                        confirmButtonColor: '#0c0c9b',
                        confirmButtonText: mode === 'edit' ? 'Update' : 'Deploy',
                        showLoaderOnConfirm: true,
                        width: '800px',
                        customClass: {
                            container: 'swal-wide',
                            confirmButton: 'swal-button--wide',
                            popup: 'swal-white-bg'
                        },
                        buttonsStyling: true,
                        preConfirm: () => {
                            const department = document.getElementById('department').value.trim();
                            const supervisor = document.getElementById('supervisor').value.trim();
                            const hours = document.getElementById('hours').value.trim();

                            if (!department || !supervisor || !hours) {
                                document.getElementById('formWarning').style.display = 'block';
                                return false;
                            }

                            document.getElementById('formWarning').style.display = 'none';

                            return new Promise((resolve) => {
                                Swal.fire({
                                    title: '<span style="font-size: 20px; color: #000000;">Verify Before Proceeding</span>',
                                    html: `
                                        <div style="padding: 20px; border-radius: 12px; background-color: #f8f9fa;">
                                            <div class="form-group" style="margin-bottom: 25px;">
                                                <p style="text-align: left; font-weight: bold; font-size: 14px;">Department</p>
                                                <p style="text-align: left; font-size: 14px; color: #700000; background-color: #e4e4e4; border-radius: 6px; padding: 12px 15px;">${department}</p>
                                            </div>
                                            <div class="form-group" style="margin-bottom: 25px;">
                                                <p style="text-align: left; font-weight: bold; font-size: 14px;">Supervisor</p>
                                                <p style="text-align: left; font-size: 14px; color: #700000; background-color: #e4e4e4; border-radius: 6px; padding: 12px 15px;">${supervisor}</p>
                                            </div>
                                            <div class="form-group">
                                                <p style="text-align: left; font-weight: bold; font-size: 14px;">Required Training Hours</p>
                                                <p style="text-align: left; font-size: 14px; color: #700000; background-color: #e4e4e4; border-radius: 6px; padding: 12px 15px;">${hours}</p>
                                            </div>
                                        </div>
                                    `,
                                    showCancelButton: false,
                                    confirmButtonText: 'Confirm',
                                    cancelButtonText: 'Edit',
                                    confirmButtonColor: '#0c0c9b',
                                    cancelButtonColor: '#700000',
                                    width: '800px',
                                    customClass: {
                                        confirmButton: 'swal-button--wide',
                                        cancelButton: 'swal-button--wide',
                                        popup: 'swal-white-bg'
                                    },
                                    preConfirm: () => {
                                        Swal.showLoading();
                                        return fetch('deploy_intern.php', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                            body: new URLSearchParams({
                                                intern_id: internId,
                                                department: department,
                                                supervisor: supervisor,
                                                hours: hours,
                                                mode: mode
                                            })
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.status === 'success') {
                                                return { isConfirmed: true, message: data.message };
                                            } else {
                                                throw new Error(data.message || 'Error deploying intern');
                                            }
                                        })
                                        .catch(error => {
                                            Swal.hideLoading();
                                            const msg = error.message;

                                            if (msg.includes("EDITABLE")) {
                                                return Swal.fire({
                                                    html: `<span class="swal-text-white move-text-up"><br><br>This intern has already been deployed. Do you want to edit the deployment information instead?</span>`,
                                                    icon: 'question',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Edit',
                                                    cancelButtonText: 'Cancel',
                                                    confirmButtonColor: '#0c0c9b',
                                                    cancelButtonColor: '#700000',
                                                    background: '#700000',
                                                    color: 'white',
                                                    customClass: {
                                                        popup: 'swal-custom-popup',
                                                        icon: 'swal-custom-icon',
                                                        title: 'move-title-up',
                                                        confirmButton: 'swal-confirm-proceed1',
                                                        cancelButton: 'swal-cancel-proceed'
                                                    },
                                                    showClass: {
                                                        popup: '',
                                                        icon: 'animate'
                                                    },
                                                    hideClass: {
                                                        popup: '',
                                                        icon: ''
                                                    },
                                                    didOpen: () => {
                                                        const icon = document.querySelector('.swal2-icon.swal2-question');
                                                        if (icon) {
                                                            icon.classList.add('animate');
                                                        }

    const content = icon?.querySelector('.swal2-icon-content');
    if (content) {
        content.style.animation = 'animate 0.5s ease-in-out';
    }
}

                                                }).then(res => {
                                                    if (res.isConfirmed) {
                                                        return fetch(`get_deployment_info.php?intern_id=${internId}`)
                                                            .then(r => r.json())
                                                            .then(data => {
                                                                if (data.status === 'success') {
                                                                    return showDeploymentForm(internId, data.department, data.supervisor, data.hours, 'edit');
                                                                } else {
                                                                    return Swal.fire('Error', 'Could not load existing info.', 'error');
                                                                }
                                                            });
                                                    } else {
                                                        return false;
                                                    }
                                                });
                                            } else {
                                                Swal.showValidationMessage(msg);
                                                return false;
                                            }
                                        });
                                    }
                                }).then(result => {
     if (result.isConfirmed && result.value && result.value.message) {
          const successMsg = result.value.message.includes("updated") ? "Updated" : "Deployed";
        Swal.fire({
                                            icon: 'success',
                                            title: '<span style="margin-top: 25px; display: block;">' + successMsg + '</span>',
                                            html: '<p style="font-size: 12px; color: white; margin-top: -15px; opacity: 0.5; font-style: italic;">(Click anywhere to proceed)</p>',
                                            showConfirmButton: false,
                                            background: '#700000',
                                            color: 'white',
                                            allowOutsideClick: true,
                                            allowEnterKey: true,
                                            allowEscapeKey: true,
                                            customClass: {
                                                icon: 'swal-custom-icon-top'
                                            },
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
                                    } else if (result.isConfirmed && result.message) {
                                        const successMsg = result.message.includes("updated") ? "Updated" : "Deployed";
                                        Swal.fire({
                                            icon: 'success',
                                            title: '<span style="margin-top: 25px; display: block;">' + successMsg + '</span>',
                                            html: '<p style="font-size: 12px; color: white; margin-top: -15px; opacity: 0.5; font-style: italic;">(Click anywhere to proceed)</p>',
                                            showConfirmButton: false,
                                            background: '#700000',
                                            color: 'white',
                                            allowOutsideClick: true,
                                            allowEnterKey: true,
                                            allowEscapeKey: true,
                                            customClass: {
                                                icon: 'swal-custom-icon-top'
                                            },
                                            
                                            
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        resolve({
                                            reopen: true,
                                            department,
                                            supervisor,
                                            hours
                                        });
                                    } else {
                                        resolve(false);
                                    }
                                });
                            });
                        }
                    }).then(result => {
    if (result && result.value && result.value.success) {
        resolve({ message: result.value.message });
    } else if (result && result.value && result.value.reopen) {
       
        resolve(result.value);
    } else {
        resolve(false);
    }
});


                });
            }

            
            document.querySelectorAll('.view-profile-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const internId = this.getAttribute('data-intern-id');
                    window.location.href = 'view_student_profile.php?intern_id=' + encodeURIComponent(internId);
                });
            });

            document.querySelectorAll('.deploy-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const studentName = row.querySelector('td:nth-child(3)').textContent;
                    const internId = this.getAttribute('data-intern-id');
                    
                    Swal.fire({
                        html: `<span class="swal-text-white move-text-up">Do you want to deploy <span class="student-name-highlight">${studentName}</span>?</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#700000',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed!',
                        cancelButtonText: 'No, cancel!',
                        customClass: {
                            popup: 'swal-custom-popup',
                            icon: 'swal-custom-icon',
                            title: 'move-title-up',
                            confirmButton: 'swal-confirm-proceed',
                            cancelButton: 'swal-cancel-proceed'
                        },
                        allowOutsideClick: true,
                        allowEnterKey: true,
                        allowEscapeKey: true,
                        didOpen: () => {
       
        const icon = document.querySelector('.swal2-icon.swal2-warning');
        if (icon) {
            icon.classList.add('animate');
        }
    }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showDeploymentForm(internId, '', '', '', 'new').then((result) => {
                                if (result && result.message) {
                                    const successMsg = result.message.includes("updated") ? "Updated" : "Deployed";
                                    Swal.fire({
                                        icon: 'success',
                                        title: '<span style="margin-top: 25px; display: block;">' + successMsg + '</span>',
                                        html: '<p style="font-size: 12px; color: white; margin-top: -15px; opacity: 0.5; font-style: italic;">(Click anywhere to proceed)</p>',
                                        showConfirmButton: false,
                                        background: '#700000',
                                        color: 'white',
                                        allowOutsideClick: true,
                                        allowEnterKey: true,
                                        allowEscapeKey: true,
                                        customClass: {
                                            icon: 'swal-custom-icon-top'
                                        },
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                       
                                    if (result.value && result.value.department && result.value.supervisor) {
                                        showDeploymentForm(internId, result.value.department, result.value.supervisor, result.value.hours || '', 'edit');
                                    }
                                }
                            }).catch((error) => {
                                if (error && error.message && error.message.includes("EDITABLE")) {
                                    Swal.fire({
                                        title: 'Intern Already Deployed',
                                        text: 'This intern has already been deployed. Do you want to edit the deployment information instead?',
                                        icon: 'question',
                                        showCancelButton: false,
                                        confirmButtonText: 'Edit',
                                        cancelButtonText: 'Cancel',
                                        confirmButtonColor: '#0c0c9b',
                                        cancelButtonColor: '#700000',
                                        customClass: {
                                            confirmButton: 'edit-btn-wide',
                                            cancelButton: 'edit-btn-wide'
                                        },
                                        showClass: {
                                            popup: '',
                                            icon: 'animate'
                                        },
                                        hideClass: {
                                            popup: '',
                                            icon: ''
                                        },
                                        didOpen: () => {
                                            const icon = document.querySelector('.swal2-icon.swal2-question');
                                            if (icon) icon.classList.add('animate');
                                        }
                                        
                                    }).then(res => {
                                        if (res.isConfirmed) {
                                           
                                            fetch(`get_deployment_info.php?intern_id=${internId}`)
                                                .then(r => r.json())
                                                .then(data => {
                                                    if (data.status === 'success') {
                                                        showDeploymentForm(internId, data.department, data.supervisor, data.hours, 'edit');
                                                    } else {
                                                        Swal.fire('Error', 'Could not load existing deployment info.', 'error');
                                                    }
                                                });
                                        }
                                    });
                                } else {
                                    Swal.fire('Error', error.message || 'Something went wrong.', 'error');
                                }
                            });
                        }
                    });
                });
            });
            


            function updateRowStripes() {
                const rows = document.querySelectorAll('.table tr.data-row');
                let visibleIndex = 0;
                
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        row.classList.remove('even-row', 'odd-row');
                        row.classList.add(visibleIndex % 2 === 0 ? 'even-row' : 'odd-row');
                        visibleIndex++;
                    }
                });
            }

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('.table tr.data-row');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });

                updateRowStripes();
            }

         
            searchInput.addEventListener('input', filterTable);


            searchBtn.addEventListener('click', filterTable);


            downloadOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const format = this.textContent;
                    const table = document.querySelector('.table');
                    downloadOpts.classList.remove('show');
                    
                    if (format === 'Print') {
                        window.print();
                    } else {
                       
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'download_table.php';
                        
                        const formatInput = document.createElement('input');
                        formatInput.type = 'hidden';
                        formatInput.name = 'format';
                        formatInput.value = format;
                        
                        form.appendChild(formatInput);
                        document.body.appendChild(form);
                        form.submit();
                        document.body.removeChild(form);
                    }
                });
            });

        
            downloadBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                downloadOpts.classList.toggle('show');
            });

  
            document.addEventListener('click', function(event) {
                if (!downloadContainer.contains(event.target)) {
                    downloadOpts.classList.remove('show');
                }
            });

            document.querySelectorAll('.dropped-btn').forEach(button => {
    button.addEventListener('click', function () {
        const row = this.closest('tr');
        const studentName = row.querySelector('td:nth-child(3)').textContent;
        const internId = row.querySelector('.deploy-btn').getAttribute('data-intern-id');

        Swal.fire({
            title: 'Checking status...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            background: '#700000',
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
            if (data.status === 'Dropped') {
                Swal.fire({
                    title: 'Already Rejected',
                    html: `<span class="swal-text-white move-text-up">This student is already rejected.<br><br><span style="font-size: 12px; font-style: italic;">(Click anywhere to continue)</span></span>`,
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
                    html: `<span class="swal-text-white move-text-up">Are you sure you want to reject <span class="student-name-highlight">${studentName}</span>?</span>`,
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
                            title: '<span class="move-title-up" style="font-size: 20px; color: #000000;">Reject Intern</span>',
                            html: `
                                <div style="padding: 12px 20px 20px 20px; border-radius: 12px; background-color: #f8f9fa; margin-top: -18px;">
                                    <div class="form-group" style="margin-bottom: 25px;">
                                        <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold; font-size: 14px;">
                                            Please provide a reason for rejecting <span class="student-name-highlight1">${studentName}</span>:
                                            <span style="color: #700000;">*</span>
                                        </label>
                                        <div style="display: flex; justify-content: center;">
                                            <textarea
                                                id="drop-reason"
                                                class="swal2-textarea"
                                                placeholder="Ex. Incomplete Required Document."
                                                style="width: 100%; min-height: 45px; margin: 0; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background-color: rgb(228, 228, 228); color: #000000; resize: vertical;"
                                                required
                                                onfocus="this.style.color='#000000'"
                                                oninput="this.style.color='#000000'"
                                            ></textarea>
                                        </div>
                                        <div class="form-group" style="margin-bottom: 20px; margin-top: 10px;">
                                            <label for="evidence-file" style="display: block; text-align: left; margin-top: 30px; margin-bottom: -5px; font-weight: bold; font-size: 14px;">
                                                Upload Evidence Document: <span style="color: #700000;">*</span>
                                            </label>
                                            <input
                                                type="file"
                                                id="evidence-file"
                                                class="swal2-file"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx,.zip"
                                                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; background-color: #f4f4f4; font-size: 14px; cursor: pointer;"
                                                required
                                            />
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
                            confirmButtonText: 'Reject',
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
                                            title: '<span style="margin-top: 25px; display: block;">Student Rejected</span>',
                                            html: '<p style="font-size: 12px; color: white; margin-top: -15px; opacity: 0.5; font-style: italic;">(Click anywhere to continue)</p>',
                                            showConfirmButton: false,
                                            background: '#700000',
                                            color: 'white',
                                            allowOutsideClick: true,
                                            allowEnterKey: true,
                                            allowEscapeKey: true,
                                            customClass: {
                                                icon: 'swal-custom-icon-top'
                                            },
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



      });
  </script>
</body>
</html>