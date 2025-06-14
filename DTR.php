<?php
include '../connection/config.php';
// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id']) || $_SESSION['auth_user']['student_id'] == 0) {
    echo "<script>window.location.href='../pending/login.php'</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-name" content="focus" />
    <title>OJT Web Portal: Student DTR</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/Picture1.png">
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
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
    <link href="endorsement-css/endorsement-moa.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <!-- Webcam -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <style>
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

        .rendered-hours,
        .remaining-hours {
            color: #333;
            margin-bottom: 5px;
        }

        .time-input-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        button {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-width: 150px;
            background-color: #e0e0e0;
            color: #333;
            cursor: pointer;
        }

        button:disabled {
            background-color: #f0f0f0;
            color: #999;
            cursor: not-allowed;
        }

        button:hover:not(:disabled) {
            background-color: #d0d0d0;
        }

        .download-btn {
            background-color: #ffc107;
            color: #333;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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

        .capture-save-btn {
            background-color: #e0e0e0;
            color: #333;
            border: none;
            margin-bottom: 20px;
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .capture-save-btn:hover {
            background-color: #d0d0d0;
        }

        .export-buttons {
            display: none;
            margin-left: 10px;
        }

        .export-buttons .dt-button {
            background-color: #ffffff;
            color: #333;
            border: 1px solid #ccc;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
            text-transform: capitalize;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .export-buttons .dt-button:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <!---------NAVIGATION BAR-------->
    <?php require_once 'templates/stud_navbar.php'; ?>
    <!---------NAVIGATION BAR ENDS-------->
    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div class="content-container">
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
                    Note: Be sure to review your input, as you won't have a chance to edit it. The system automatically records the current date and time when you log in or out. If you encounter issues such as forgetting to time in or out on a given day or needing to edit your time records, you will need to coordinate with your adviser for corrections.
                </p>

                <div class="hours-info">
                    <?php
                    $studID = $_SESSION['auth_user']['student_id'];
                    // Calculate total rendered hours
                    $stmt = $conn->prepare("SELECT SUM(total_working_hours) as total_hours FROM stud_daily_time_records WHERE stud_id = ? AND recordStatus = 'Approved'");
                    $stmt->execute([$studID]);
                    $actualHours = $stmt->fetch(PDO::FETCH_ASSOC)['total_hours'] ?? 0;
                    $requiredHours = 300;
                    $remainingHours = $requiredHours - $actualHours;
                    ?>
                    <p class="required-hours">Required Internship Hours: <?php echo $requiredHours; ?>hrs</p>
                    <p class="rendered-hours">Total Rendered Hours: <?php echo number_format($actualHours, 2); ?>hrs</p>
                    <p class="remaining-hours">Total Remaining Hours: <?php echo number_format($remainingHours, 2); ?>hrs</p>
                </div>

                <form action="save_daily_time_records.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="time_type" id="timeTypeHidden">
                    <div class="time-input-section">
                        <?php
                        $today = date('Y-m-d');
                        $stmt = $conn->prepare("SELECT AM_time_IN, AM_time_OUT, PM_time_IN, PM_time_OUT FROM stud_daily_time_records WHERE stud_id = ? AND recordDate = ?");
                        $stmt->execute([$studID, $today]);
                        $record = $stmt->fetch(PDO::FETCH_ASSOC);
                        $amInSet = $record && $record['AM_time_IN'] != '00:00:00';
                        $amOutSet = $record && $record['AM_time_OUT'] != '00:00:00';
                        $pmInSet = $record && $record['PM_time_IN'] != '00:00:00';
                        $pmOutSet = $record && $record['PM_time_OUT'] != '00:00:00';
                        ?>
                        <button type="button" name="time_type" value="AM_time_IN" <?php if ($amInSet) echo 'disabled'; ?>>AM Time In</button>
                        <button type="button" name="time_type" value="AM_time_OUT" <?php if ($amOutSet) echo 'disabled'; ?>>AM Time Out</button>
                        <button type="button" name="time_type" value="PM_time_IN" <?php if ($pmInSet) echo 'disabled'; ?>>PM Time In</button>
                        <button type="button" name="time_type" value="PM_time_OUT" <?php if ($pmOutSet) echo 'disabled'; ?>>PM Time Out</button>
                    </div>

                    <!-- Camera for taking photo on time in/out -->
                    <div id="camera-container" style="display: none;">
                        <div id="my_camera"></div>
                        <input type="hidden" name="image" class="image-tag">
                        <div id="results"></div>
                        <button type="button" id="saveBtn" class="capture-save-btn">Save</button>
                    </div>
                </form>

                <div class="action-row" style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center;">
                        <button class="download-btn" id="toggleDownloadBtn">
                            <i class="fa fa-download"></i> Download
                        </button>
                        <span class="export-buttons" id="exportButtons">
                        </span>
                    </div>
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
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_SESSION['auth_user']['student_id'])) {
                                $studID = $_SESSION['auth_user']['student_id'];
                                $stmt = $conn->prepare("SELECT recordDate, AM_time_IN, AM_time_OUT, PM_time_IN, PM_time_OUT, recordStatus FROM stud_daily_time_records WHERE stud_id = ? ORDER BY recordDate DESC");
                                $stmt->execute([$studID]);
                                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($data as $result) {
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($result['recordDate']); ?></td>
                                        <td><?php echo htmlspecialchars($result['AM_time_IN'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($result['AM_time_OUT'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($result['PM_time_IN'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($result['PM_time_OUT'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($result['recordStatus']); ?></td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/customAlert.js"></script>
    <script src="js/errorAlert.js"></script>

    <!-- DataTables and Webcam -->
    <script>
        $(document).ready(function() {
            // Initialize DataTables with export buttons and disable default search
            var table = $('#dtrTable').DataTable({
                dom: 'frtip', // Remove default buttons from DataTables layout
                searching: false, // Disable DataTables default search field
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Move DataTables buttons to the custom container
            table.buttons().container().appendTo('#exportButtons');

            // Toggle export buttons visibility on Download button click
            $('#toggleDownloadBtn').click(function() {
                $('#exportButtons').toggle();
            });

            // Custom search functionality for the external search input
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Initialize webcam
            Webcam.set({
                width: 320,
                height: 240,
                image_format: 'jpeg',
                jpeg_quality: 90
            });

            // Handle time button clicks
            $('button[name="time_type"]').click(function(e) {
                e.preventDefault(); // Prevent form submission
                var timeType = $(this).val();
                $('#timeTypeHidden').val(timeType); // Set the hidden input
                $('#camera-container').show();
                Webcam.attach('#my_camera');
            });

            // Handle save button click
            $('#saveBtn').click(function() {
                take_snapshot();
                $('form').submit(); // Submit the form
            });
        });

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="' + data_uri + '" style="width: 320px; height: 240px;"/>';
            });
        }

        function searchTable() {
            var table = $('#dtrTable').DataTable();
            table.search($('#searchInput').val()).draw();
        }
    </script>

    <?php
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
        <script>
            const statusMessage = "<?php echo addslashes($_SESSION['status']); ?>";
            if (statusMessage.toLowerCase().includes('success')) {
                showSuccessAlert('Success', statusMessage);
            } else {
                showErrorAlert('Error', statusMessage);
            }
        </script>
    <?php
        unset($_SESSION['status']);
    }
    ?>
</body>

</html>