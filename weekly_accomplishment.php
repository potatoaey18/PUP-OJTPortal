<?php
include '../connection/config.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
error_log("Session auth_user: " . print_r($_SESSION['auth_user'] ?? [], true));

// Validate student ID (numeric id from students_data)
if (!isset($_SESSION['auth_user']['student_id']) || !is_numeric($_SESSION['auth_user']['student_id']) || $_SESSION['auth_user']['student_id'] <= 0) {
    error_log("Redirecting to ../pending/login.php: Invalid or missing student_id");
    $_SESSION['status'] = "Please log in to access this page.";
    $_SESSION['status-code'] = "error";
    $_SESSION['alert'] = "Error"; // Added for consistency with HTE Evaluation
    echo "<script>window.location.href='../pending/login.php'</script>";
    exit;
}

// Verify student_id exists in students_data
$studID = $_SESSION['auth_user']['student_id'];
try {
    $stmt = $conn->prepare("SELECT id FROM students_data WHERE id = ?");
    $stmt->execute([$studID]);
    if ($stmt->rowCount() == 0) {
        error_log("Invalid student_id: $studID not found in students_data");
        $_SESSION['status'] = "Invalid student account.";
        $_SESSION['status-code'] = "error";
        $_SESSION['alert'] = "Error"; // Added for consistency
        echo "<script>window.location.href='../pending/login.php'</script>";
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error validating student_id: " . $e->getMessage());
    $_SESSION['status'] = "Error validating account.";
    $_SESSION['status-code'] = "error";
    $_SESSION['alert'] = "Error"; // Added for consistency
    echo "<script>window.location.href='../pending/login.php'</script>";
    exit;
}

error_log("Fetching records for studID: " . $studID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-name" content="focus" />
    <title>OJT Web Portal: Weekly Accomplishment</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/Picture1.png">
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
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        /* Back Button */
        .back-button {
            color: #666;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-button:hover {
            color: #333;
        }

        /* Page Title */
        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        /* Input Row Layout */
        .input-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        /* Input Group Styling */
        .input-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .input-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        /* Form Input Fields */
        select,
        input[type="date"],
        input[type="time"],
        input[type="text"],
        input[type="number"],
        textarea {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-width: 150px;
            color: #333;
        }

        textarea {
            min-width: 300px;
            min-height: 100px;
        }

        /* Save Button */
        .save-btn {
            background-color: #e0e0e0;
            color: #333;
            border: none;
            padding: 8px 25px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 28px;
            height: 50px;
            width: 130px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .save-btn.enabled:hover {
            background-color: rgb(0, 141, 0);
            color: white;
        }

        /* Download Button */
        .download-btn {
            background-color: #ffc107;
            color: #333;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            height: 50px;
            width: 130px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .download-btn:hover {
            background-color: #e0a800;
        }

        /* Search Container */
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
            width: 130px;
        }

        /* Action Row */
        .action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Table Header */
        .table-header {
            background-color: #800000;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }

        /* Table Container */
        .table-container {
            width: 100%;
            margin-bottom: 20px;
        }

        /* Record Table */
        .record-table {
            width: 100%;
            border-collapse: collapse;
        }

        .record-table th {
            background-color: #fff;
            color: #800020;
            text-align: center;
            padding: 20px;
            border: 1px solid #800020;
            font-weight: 600;
        }

        .record-table td {
            padding: 20px;
            border: 1px solid #ddd;
            text-align: center;
            color: #333;
            word-wrap: break-word;
        }

        .record-table tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        /* Table Column Widths */
        .record-table th:nth-child(1) {
            width: 10%;
        }

        .record-table th:nth-child(2) {
            width: 10%;
        }

        .record-table th:nth-child(3) {
            width: 45%;
        }

        .record-table th:nth-child(4) {
            width: 30%;
        }

        .record-table th:nth-child(5) {
            width: 5%;
        }

        /* Export Buttons */
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
    <?php require_once 'templates/stud_navbar.php'; ?>
    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div class="content-container">
                <div>
                    <a href="documentation.php" class="back-button">
                        <img src="images/less-than.png" alt="Back"> Back
                    </a>
                </div>
                <div class="page-header">
                    <div class="page-title">
                        <h1 style="font-size: 16px;"><b>Weekly Accomplishment</b></h1>
                    </div>
                </div>
                <form action="save_weekly_accomplishment.php" method="POST">
                    <div class="input-row">
                        <div class="input-group">
                            <label for="week">Select week number:</label>
                            <div style="display: flex; align-items: center;">
                                <select name="week_number" id="week" required>
                                    <option value="">Select Week</option>
                                    <?php for ($i = 1; $i <= 20; $i++) { ?>
                                        <option value="<?php echo $i; ?>">Week <?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="date">Select Date & Time:</label>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php date_default_timezone_set('Asia/Manila'); ?>
                                <input type="date" name="selected_date" id="selected_date" value="<?php echo date('Y-m-d'); ?>" required>
                                <input type="time" name="selected_time" id="selected_time" value="<?php echo date('H:i'); ?>" required>
                            </div>
                        </div>
                        <div class="input-group" style="flex-basis: 100%;">
                            <label for="accomplishment">Daily Accomplishment Report / Output:</label>
                            <textarea name="accomplishment" id="accomplishment" required></textarea>
                        </div>
                        <div class="input-group" style="flex-basis: 48%;">
                            <label for="coworkers">Name/Number of Co-workers in the Activity:</label>
                            <input type="text" name="coworkers" id="coworkers" required>
                        </div>
                        <div class="input-group" style="flex-basis: 48%;">
                            <label for="working_hours">No. of Working Hours:</label>
                            <input type="number" name="working_hours" id="working_hours" min="1" required>
                        </div>
                        <button type="submit" name="save" class="save-btn">Save</button>
                    </div>
                </form>
                <div class="action-row">
                    <div style="display: flex; align-items: center;">
                        <button class="download-btn" id="toggleDownloadBtn">
                            <img src="images/download.png" alt="Download" style="height: 16px; width: 16px;"> Download
                        </button>
                        <span class="export-buttons" id="exportButtons"></span>
                    </div>
                    <div class="search-container">
                        <input type="text" placeholder="Search here..." id="searchInput" class="search-input">
                        <button class="search-btn" onclick="searchTable()">Search</button>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-header">
                        Weekly Accomplishment
                    </div>
                    <table class="record-table" id="accomplishmentTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Week #</th>
                                <th>Daily Accomplishment Report / Output</th>
                                <th>Name/Number of Co-workers in the Activity</th>
                                <th>No. of Working Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT * FROM weekly_accomplishment WHERE stud_id = ? ORDER BY date DESC, time DESC");
                                $stmt->execute([$studID]);
                                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                error_log("Fetched " . count($data) . " records for stud_id: " . $studID);

                                foreach ($data as $row) {
                            ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                                        <td>Week <?= htmlspecialchars($row['week_number']) ?></td>
                                        <td><?= htmlspecialchars($row['accomplishment']) ?></td>
                                        <td><?= htmlspecialchars($row['coworkers']) ?></td>
                                        <td><?= htmlspecialchars($row['working_hours']) ?></td>
                                    </tr>
                            <?php
                                }
                            } catch (PDOException $e) {
                                error_log("Database error fetching records: " . $e->getMessage());
                                echo "<tr><td colspan='5'>Error loading records: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
    <script src="js/customAlert.js?t=<?php echo time(); ?>"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables with export buttons and disable default search
            var table = $('#accomplishmentTable').DataTable({
                dom: 'frtip',
                searching: false,
                pageLength: 10,
                lengthChange: false,
                order: [[0, "desc"]],
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

            // Function to check if all required fields are filled
            function checkFormCompletion() {
                var week = $('#week').val();
                var selectedDate = $('#selected_date').val();
                var selectedTime = $('#selected_time').val();
                var accomplishment = $('#accomplishment').val().trim();
                var coworkers = $('#coworkers').val().trim();
                var workingHours = $('#working_hours').val();

                // Check if all fields are filled and working_hours is at least 1
                var isComplete = week !== '' &&
                                 selectedDate !== '' &&
                                 selectedTime !== '' &&
                                 accomplishment !== '' &&
                                 coworkers !== '' &&
                                 workingHours !== '' && workingHours >= 1;

                // Toggle the enabled class on the save button
                if (isComplete) {
                    $('.save-btn').addClass('enabled');
                } else {
                    $('.save-btn').removeClass('enabled');
                }
            }

            // Monitor changes on all required fields
            $('#week, #selected_date, #selected_time, #accomplishment, #coworkers, #working_hours').on('input change', function() {
                checkFormCompletion();
            });

            // Initial check on page load
            checkFormCompletion();
        });

        function searchTable() {
            var table = $('#accomplishmentTable').DataTable();
            table.search($('#searchInput').val()).draw();
        }

        // Handle session status alerts
        <?php if (isset($_SESSION['status']) && $_SESSION['status'] != '') { ?>
            document.addEventListener('DOMContentLoaded', function() {
                console.log("Alert script running. Status code: <?php echo $_SESSION['status-code']; ?>");
                try {
                    const title = "<?php echo isset($_SESSION['alert']) ? addslashes($_SESSION['alert']) : ($_SESSION['status-code'] === 'success' ? 'Success' : 'Error'); ?>";
                    const message = "<?php echo addslashes($_SESSION['status']); ?>";
                    if ("<?php echo $_SESSION['status-code']; ?>" === "success") {
                        if (typeof showSuccessAlert === 'function') {
                            console.log("Calling showSuccessAlert with title: " + title + ", message: " + message);
                            showSuccessAlert(title, message);
                        } else {
                            console.warn("showSuccessAlert is not defined. Falling back to native alert.");
                            alert("Success: " + message);
                        }
                    } else {
                        if (typeof showErrorAlert === 'function') {
                            console.log("Calling showErrorAlert with title: " + title + ", message: " + message);
                            showErrorAlert(title, message);
                        } else {
                            console.warn("showErrorAlert is not defined. Falling back to native alert.");
                            alert("Error: " + message);
                        }
                    }
                } catch (e) {
                    console.error("Error displaying alert: ", e);
                    alert("An error occurred: <?php echo addslashes($_SESSION['status']); ?>");
                }
            });
        <?php
            unset($_SESSION['status']);
            unset($_SESSION['status-code']);
            unset($_SESSION['alert']);
        } ?>
    </script>
</body>
</html>