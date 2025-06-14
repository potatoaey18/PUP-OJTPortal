<?php

include '../../connection/config.php';
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
if($_SESSION['auth_user']['student_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- theme meta -->
    <meta name="theme-name" content="focus" />
    <title>OJT Web Portal: Student DTR</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/Picture1.png">
    <!-- Retina iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <!-- Retina iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <!-- Standard iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
    <!-- Standard iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff">
    <!-- Styles -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="endorsement-css/endorsement-moa.css" rel="stylesheet">

    <!---------------------DATATABLES------------------------->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <!----------------------CAMERA---------------------------->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        .content-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .back-button {
            color: #666;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            color: #333;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        
        .note-text {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .hours-info {
            margin-bottom: 20px;
        }
        
        .required-hours {
            color: #a00;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .rendered-hours, .remaining-hours {
            color: #333;
            margin-bottom: 5px;
        }
        
        .time-input-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }
        
        .select-container {
            margin-right: 15px;
        }
        
        select, input[type="date"], input[type="time"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-width: 150px;
        }
        
        .save-btn {
            background-color: #e0e0e0;
            color: #333;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .save-btn:hover {
            background-color: #d0d0d0;
        }
        
        .download-btn {
            background-color: #ffc107;
            color: #333;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .download-btn:hover {
            background-color: #e0a800;
        }
        
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: flex-end;
        }
        
        .search-input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-width: 250px;
        }
        
        .search-btn {
            background-color: #800000;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .table-header {
            background-color: #800000;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }
        
        .record-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .record-table th {
            padding: 20px;
            text-align: center;
            border: 1px solid #800020;
        }
        
        .record-table td {
            padding: 20px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        #camera-container {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/stud_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->
<div class="content-wrap" style="height: 80%; width: 100%;margin: 0 auto;">
    <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
    <div>
    <div>
        <a href="documentation.php" class="back-button">
            <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                Back    
            </a>
        </div>
        
        <div class="page-header">
            <div class="page-title">
                <h1 style="font-size: 16px;"><b>Daily Time Record</b></h1>
            </div>
        </div>
        
        <p class="note-text">
            Note: Be sure to review your input, as you won't have a chance to edit it. The date will also be set to the default date when you log in, so make sure to log in every day. If you encounter issues such as forgetting to time in or out on a given day or needing to edit your time in and time out, you will need to coordinate with your adviser for corrections.
        </p>
        
        <div class="hours-info">
            <?php
            $actualHours = 16; 
            $remainingHours = 300 - $actualHours;
            ?>
            <p class="required-hours">Required Internship Hours : 300hrs</p>
            <p class="rendered-hours">Total rendered Hours : <?php echo $actualHours; ?>hrs</p>
            <p class="remaining-hours">Total remaining Hours : <?php echo $remainingHours; ?>hrs</p>
        </div>
        
        <form action="save_daily_time_records.php" method="POST">
            <div class="time-input-section">
                <div class="select-container">
                    <label>Select Time in or Time out:</label>
                    <select name="time_type" id="timeType">
                        <option value="">Select here</option>
                        <option value="time_in">Time In</option>
                        <option value="time_out">Time Out</option>
                    </select>
                </div>
                
                <div class="select-container">
                    <label>Select Date & Time:</label>
                    <?php date_default_timezone_set('Asia/Manila'); ?>
                    <input type="date" name="selected_date" value="<?php echo date('Y-m-d'); ?>">
                    <input type="time" name="selected_time" value="<?php echo date('H:i'); ?>">
                </div>
                
                <button type="submit" name="save_time" class="save-btn">Save Time</button>
            </div>
            
            <!-- Hidden camera for taking photo on time in/out -->
            <div id="camera-container">
                <div id="my_camera"></div>
                <input type="hidden" name="image" class="image-tag">
                <div id="results"></div>
            </div>
        </form>
        
        <div class="action-row">
            <button class="download-btn">
                <i class="fa fa-download"></i> Download
            </button>
            
            <div class="search-container">
                <input type="text" placeholder="Search here..." id="searchInput" class="search-input">
                <button class="search-btn" onclick="searchTable()">Search</button>
            </div>
        </div>
        
        <div class="table-container">
            <div class="table-header">
                Daily Time Record
            </div>
            <table class="record-table" id="dtrTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>AM Time In</th>
                        <th>AM Time Out</th>
                        <th>PM Time In</th>
                        <th>PM Time Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['auth_user']['student_id'])) {
                        $studID = $_SESSION['auth_user']['student_id'];
                        $stmt = $conn->prepare("SELECT recordDate, AM_time_IN, AM_time_OUT, PM_time_IN, PM_time_OUT FROM stud_daily_time_records WHERE stud_id = ?");
                        $stmt->execute([$studID]);
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($data as $result) {
                    ?>
                            <tr>
                                <td><?= $result['recordDate'] ?></td>
                                <td><?= $result['AM_time_IN'] ?></td>
                                <td><?= $result['AM_time_OUT'] ?></td>
                                <td><?= $result['PM_time_IN'] ?></td>
                                <td><?= $result['PM_time_OUT'] ?? '' ?></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- DataTables and Webcam -->
    <script>
        $(document).ready(function() {
            
            // Initialize webcam
            Webcam.set({
                width: 320,
                height: 240,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            
            // Only attach webcam when needed
            $('#timeType').change(function() {
                if ($(this).val() !== '') {
                    $('#camera-container').show();
                    Webcam.attach('#my_camera');
                } else {
                    $('#camera-container').hide();
                    Webcam.reset();
                }
            });
            
            // Time in/out form submission
            $('form').submit(function(e) {
                if ($('#timeType').val() !== '') {
                    take_snapshot();
                }
            });
        });

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="' + data_uri + '" style="width: 320px; height: 240px;"/>';
            });
        }
        
        function searchTable() {
            var input = document.getElementById("searchInput").value;
            $('#dtrTable').DataTable().search(input).draw();
        }
    </script>

    <?php
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
        <script>
            alert("<?php echo $_SESSION['status']; ?>");
        </script>
    <?php
        unset($_SESSION['status']);
    }
    ?>
</body>

</html>