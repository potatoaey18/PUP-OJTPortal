<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if($_SESSION['auth_user']['supervisor_id']==0){
    echo"<script>window.location.href='index.php'</script>";
}

if (!isset($_GET['student_id'])) {
    $student_id = null;
} else {
    $student_id = $_GET['student_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OJT Web Portal: DTR</title>
    <link rel="shortcut icon" href="images/Picture1.png">
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
            height: calc(100vh - 7rem);
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
            max-height: calc(100vh - 15rem);
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
            text-align: center;
        }

        .table td {
            text-align: center !important;
            border-right: 1px solid #700000;
            border-bottom: 1px solid #700000;
            height: 50px;
            padding: 0.8rem 1rem;
            white-space: normal;
            word-wrap: break-word;
            vertical-align: middle;
            color: #000000;
            font-size: 1rem;
        }

    
        .table td:nth-child(1) { /* Company Name */
            width: 15%;
        }
        .table td:nth-child(2) { /* SIS No. */
            width: 15%;
        }
        .table td:nth-child(3) { /* Full Name */
            width: 25%;
        }
        .table td:nth-child(4) { /* Status */
            width: 15%;
        }
        .table td:nth-child(5), /* View Weekly Accomplishment */
        .table td:nth-child(6) { /* View Profile */
            width: 15%;
        }

        .table td:last-child {
            border-right: none;
        }

        .table tr.data-row:nth-child(even) {
            background-color: rgb(189, 189, 189);
        }

        .table tr.data-row:nth-child(odd) {
            background-color: rgb(255, 255, 255);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .view-profile-btn {
            background-color: #700000;
            color: white;
            border: none;
            border-radius: 15px;
            padding: 8px 20px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }

        .view-profile-btn1 {
            background-color: #700000;
            color: white;
            border: none;
            border-radius: 15px;
            padding: 8px 45px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }

        .view-profile-btn:hover {
            background-color: #8b0000;
        }

        .search-container {
            display: flex;
            align-items: stretch;
            margin-bottom: 0rem;
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
            width: 1.2rem;
            height: 1.2rem;
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

        .search-btn:hover {
            background-color: #8b0000;
        }

        .table-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            width: 100%;
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

        .intern-info {
            margin: 20px 0;
            margin-top:-10px;
        }

        .intern-info p {
            margin: 8px 0;
            font-size: 1rem;
            color:rgb(109, 109, 109);
            display: flex;
        }

        .required-hours span {
    color: #800000 !important;
}


        .intern-info .required-hours span:first-child,
        .intern-info .hours-info span:first-child {
            color:rgb(73, 73, 73);
            width: 245px;
            display: inline-block;
        }

        .intern-info p strong{
            color:rgb(73, 73, 73);
            width: 170px;
            display: inline-block;
        }

        .intern-info p .colon {
            width: 20px;
            font-weight: bold;
        }

        .intern-info .required-hours {
            color: #800000;
            font-weight: bold;
            margin-top: 20px;
            display: flex;
        }

        .required-hours, .hours-info {
            font-size: 1.2rem!important;
        }

        .intern-info .hours-info {
            margin-top: 10px;
            display: flex;
            font-weight: bold;
        }

        .label-cell {
            width: 190px;
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

            <div class="page-title">Daily Time Record</div>
            
            <div class="intern-info">
                <?php
            
                echo "<!-- student_id from URL: ".$student_id." -->\n";
               
                $intern_name = '-';
                $intern_status = '-';
                $assigned_dept = '-';
                if (isset($student_id)) {
                    $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, ojt_status, stud_dept FROM students_data WHERE student_ID = ? LIMIT 1");
             
                    echo "<!-- SQL: SELECT first_name, middle_name, last_name, ojt_status, stud_dept FROM students_data WHERE student_ID = '$student_id' LIMIT 1 -->\n";
                    $stmt->execute([$student_id]);
                    if ($row = $stmt->fetch()) {
                        $intern_name = htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']);
                        $intern_status = htmlspecialchars($row['ojt_status'] ?? '-');
                 
                        $dept_stmt = $conn->prepare("SELECT department FROM intern_deployments WHERE intern_id = ? ORDER BY deployment_date DESC, id DESC LIMIT 1");
                        $dept_stmt->execute([$student_id]);
                        if ($dept_row = $dept_stmt->fetch()) {
                         
                            $raw_dept = trim($dept_row['department']);
                            $norm = strtoupper($raw_dept);
                            $override = [
                                'IT DEPARTMENT' => 'IT Department',
                                'HR' => 'HR',
                                'ICT' => 'ICT',
                            ];
                            if (isset($override[$norm])) {
                                $assigned_dept = $override[$norm];
                            } elseif ($raw_dept === strtolower($raw_dept)) {
                                $assigned_dept = ucwords($raw_dept);
                            } else {
                                $assigned_dept = $raw_dept;
                            }
                            $assigned_dept = htmlspecialchars($assigned_dept);
                        } else {
                            $assigned_dept = '-';
                        }
                    }
                }
                ?>
                <p><strong>Intern's Name</strong><span class="colon">:</span> <?= $intern_name ?></p>
                <p><strong>Status</strong><span class="colon">:</span> <?= $intern_status ?></p>
                <p><strong>Assigned Department</strong><span class="colon">:</span> <?= $assigned_dept ?></p>

                <br><br>
                
                <p class="required-hours"><span>Required Internship Hours</span><span class="colon">:</span> 300hrs</p>
                <p class="hours-info"><span>Total rendered Hours</span><span class="colon">:</span> 16hrs</p>
                <p class="hours-info"><span>Total remaining Hours</span><span class="colon">:</span> 286hrs</p>
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
                        <input type="text" class="search-input" placeholder="Search here..." style="border-radius: 6px 0 0 6px; padding-left: 2.2rem;">
                    </div>
                    <button class="search-btn" style="border-radius: 0 6px 6px 0;">Search</button>
                </div>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="5">
                                <div class="table-header">Daily Time Record</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="column-headers">
                            <td>Date</td>
                            <td>AM Time In</td>
                            <td>AM Time Out</td>
                            <td>PM Time In</td>
                            <td>PM Time Out</td>
                        </tr>
                        <?php
                        if (!$student_id) {
                            echo "<tr><td colspan='5'>No student selected</td></tr>";
                            exit;
                        }
                        $query = "SELECT date, am_time_in, am_time_out, pm_time_in, pm_time_out FROM daily_time_records1 WHERE student_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([$student_id]);
                        $result = $stmt->fetchAll();

                        if($result){
                            foreach($result as $row){
                                echo "<tr class='data-row'>";
                                echo "<td>" . date('d/m/Y', strtotime($row['date'])) . "</td>";
                                echo "<td>" . ($row['am_time_in'] ? date('h:i A', strtotime($row['am_time_in'])) : '-') . "</td>";
                                echo "<td>" . ($row['am_time_out'] ? date('h:i A', strtotime($row['am_time_out'])) : '-') . "</td>";
                                echo "<td>" . ($row['pm_time_in'] ? date('h:i A', strtotime($row['pm_time_in'])) : '-') . "</td>";
                                echo "<td>" . ($row['pm_time_out'] ? date('h:i A', strtotime($row['pm_time_out'])) : '-') . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const searchBtn = document.querySelector('.search-btn');
            const downloadOptions = document.querySelectorAll('.option-btn');
            const downloadContainer = document.querySelector('.download-container');
            const downloadBtn = document.querySelector('.download-btn');
            const downloadOpts = document.querySelector('.download-options');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('.table tr.data-row');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
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
        });
    </script>
</body>
</html>