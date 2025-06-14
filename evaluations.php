<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if($_SESSION['auth_user']['supervisor_id']==0){
    echo"<script>window.location.href='index.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OJT Web Portal: Daily Time Record</title>
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

        .table td {
            border-right: 1px solid #700000;
            border-bottom: 1px solid #700000;
            height: 50px;
            padding: 0.8rem 1.5rem;
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            color: #000000;
            font-size: 1rem;
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

        .table td:nth-child(1) { /* Assigned Department */
            min-width: 200px;
        }

        .table td:nth-child(2) { /* SIS No. */
            min-width: 150px;   
        }

        .table td:nth-child(3) { /* Section */
            min-width: 150px;
        }

        .table td:nth-child(4) { /* Full Name */
            min-width: 400px;
        }

        .table td:nth-child(5) { /* Status */
            min-width: 150px;
        }

        .table td:nth-child(6), /* DTR */
        .table td:nth-child(7) { /* View Profile */
            min-width: 150px;
        }

        .table td:last-child {
            border-right: none;
            text-align: center;
            min-width: 150px;
        }

        .view-profile-btn {
            background-color: #700000;
            color: white;
            border: none;
            padding: 0.15rem 1.5rem;
            border-radius: 16px;
            cursor: pointer;
            font-size: 0.8rem;
            display: inline-block;
            width: 100%;
            max-width: 120px;
            text-decoration: none;
        }

        .view-profile-btn:hover {
            background-color: #900000;
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
            width: 300px;
            font-size: 0.9rem;
        }

        .search-btn {
            background-color: #700000;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .search-btn:hover {
            background-color: #900000;
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
            <h1 style="font-size: 16px;"><b>Daily Time Record</b></h1>
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
                            <th colspan="7">
                                <div class="table-header">Daily Time Record</div>
                            </th>
                        </tr>
                    </thead>    
                    <tbody>
                        <tr class="column-headers">
                            <td>Full Name</td>
                            <td>Section</td>
                            <td>Department</td>
                            <td>Total Rendered Hours</td>
                            <td>Evaluation</td>
                            <td>View Profile</td>
                        </tr>
                        <?php
                       
                        $supervisorID = $_SESSION['auth_user']['supervisor_id'];
                        $stmt = $conn->prepare("SELECT company_name FROM supervisor WHERE id = ?");
                        $stmt->execute([$supervisorID]);
                        $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
                        $companyName = $supervisor ? $supervisor['company_name'] : '';

                       
                        $students = [];
                        if ($companyName !== '') {
                            $studStmt = $conn->prepare("SELECT * FROM students_data WHERE stud_hte = ?");
                            $studStmt->execute([$companyName]);
                            $students = $studStmt->fetchAll(PDO::FETCH_ASSOC);
                        }

                        if (!empty($students)) {
                            foreach ($students as $student) {
                                echo "<tr class='data-row'>";
                                $full_name = htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . $student['middle_name']);
                                $section = htmlspecialchars($student['stud_section']); 
                                $department = 'IT Department'; 
                                $total_hours = '320'; 
                                echo "<td>$full_name</td>";
                                echo "<td>$section</td>";
                                echo "<td>$department</td>";
                                echo "<td>$total_hours</td>";
                                echo "<td><a href='evaluation_interns.php?student_ID=" . urlencode($student['student_ID']) . "' class='view-profile-btn evaluate-btn'>Evaluate</a></td>";
                                echo "<td><a href='view_student_profile.php?intern_id=" . urlencode($student['student_ID']) . "' class='view-profile-btn'>View Profile</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No students found for your company.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            document.querySelectorAll('.evaluate-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var internId = this.getAttribute('data-intern-id');
                    window.location.href = 'evaluation_interns.php?intern_id=' + encodeURIComponent(internId);
                });
            });
        });
    </script>
</body>
</html>