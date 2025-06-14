<?php
include '../../connection/config.php';
// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if ($_SESSION['auth_user']['student_id'] == 0) {
    echo "<script>window.location.href='index.php'</script>";
    exit();
}

// Handle form submission to save weekly accomplishment
if (isset($_POST['save'])) {
    // Get the student ID and uniqueID from the session
    if (isset($_SESSION['auth_user']['student_id']) && isset($_SESSION['auth_user']['uniqueID'])) {
        $studID = $_SESSION['auth_user']['student_id'];
        $uniqueID = $_SESSION['auth_user']['uniqueID'];

        // Debug: Print the uniqueID to verify its value
        error_log("Debug: uniqueID from session = " . $uniqueID);

        // Verify if the uniqueID exists in the students_data table
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM students_data WHERE uniqueID = ?");
        $stmt_check->execute([$uniqueID]);
        $uniqueID_exists = $stmt_check->fetchColumn();

        if (!$uniqueID_exists) {
            $_SESSION['status'] = "Error: Invalid uniqueID. Please log in again.";
            header("Location: index.php");
            exit();
        }

        // Sanitize and retrieve form data
        $week_number = filter_input(INPUT_POST, 'week_number', FILTER_SANITIZE_NUMBER_INT);
        $selected_date = filter_input(INPUT_POST, 'selected_date', FILTER_SANITIZE_STRING);
        $selected_time = filter_input(INPUT_POST, 'selected_time', FILTER_SANITIZE_STRING);
        $accomplishment = filter_input(INPUT_POST, 'accomplishment', FILTER_SANITIZE_STRING);
        $coworkers = filter_input(INPUT_POST, 'coworkers', FILTER_SANITIZE_STRING);
        $working_hours = filter_input(INPUT_POST, 'working_hours', FILTER_SANITIZE_NUMBER_INT);

        // Validate required fields
        if (empty($week_number) || empty($selected_date) || empty($selected_time) || empty($accomplishment) || empty($coworkers) || empty($working_hours)) {
            $_SESSION['status'] = "All fields are required!";
        } else {
            try {
                // Prepare the SQL statement to insert the data into the weekly_accomplishment table
                $stmt = $conn->prepare("INSERT INTO weekly_accomplishment (stud_id, uniqueID, week_number, date, time, accomplishment, coworkers, working_hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                
                // Execute the statement with the form data
                $stmt->execute([$studID, $uniqueID, $week_number, $selected_date, $selected_time, $accomplishment, $coworkers, $working_hours]);

                // Set a success message
                $_SESSION['status'] = "Weekly accomplishment saved successfully!";
            } catch (PDOException $e) {
                // Set an error message if the query fails
                $_SESSION['status'] = "Error saving accomplishment: " . $e->getMessage();
            }
        }
    } else {
        // If the student ID or uniqueID is not set in the session, redirect to the login page
        $_SESSION['status'] = "You must be logged in to save accomplishments.";
        echo "<script>window.location.href='index.php'</script>";
        exit();
    }

    // Redirect to the same page to refresh the table and show the status message
    header("Location: weekly_accomplishment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Theme meta -->
    <meta name="theme-name" content="focus" />
    <title>OJT Web Portal: Weekly Accomplishment</title>
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
        }

        .input-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .input-group label {
            margin-bottom: 5px;
            font-weight: 500;
        }

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
        }

        textarea {
            min-width: 300px;
            min-height: 100px;
        }

        .save-btn {
            background-color: #e0e0e0;
            color: #333;
            border: none;
            padding: 8px 28px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 28px;
        }

        .save-btn:hover {
            background-color: #d0d0d0;
        }

        .download-btn {
            background-color: #f8c968;
            color: #333;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .download-btn:hover {
            background-color: #e0a800;
        }

        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            margin-left: auto;
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

        .action-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .table-header {
            background-color: #800000;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .record-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px; 
        }

        .record-table th {
            background-color: #fff;
            color: #700000;
            text-align: center;
            padding: 20px 50px;
            border: 2px solid #700000;
            font-weight: 600;
        }

        .record-table td {
            padding: 20px 50px;
            border: 2px solid #700000;
            text-align: center;
            color: #000;
        }

        .record-table tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        /* Adjust column widths to match the table structure */
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

        .clock-icon {
            width: 16px;
            height: 16px;
            margin-left: 4px;
        }
    </style>
</head>

<body>
    <!---------NAVIGATION BAR-------->
    <?php
    require_once 'templates/stud_navbar.php';
    ?>
    <!---------NAVIGATION BAR ENDS-------->
    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div>
                <a href="documentation.php" class="back-button">
                    <img src="images/less-than.png" alt="Back"> Back
                </a>
            </div>

            <div class="page-header">
                <div class="page-title">
                    <h1 style="font-size: 20px;"><b>Weekly Accomplishment</b></h1>
                </div>
            </div>

            <form action="weekly_accomplishment.php" method="POST">
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
                            <input type="date" name="selected_date" value="<?php echo date('Y-m-d'); ?>" required>
                            <input type="time" name="selected_time" value="<?php echo date('H:i'); ?>" required>
                            <img src="images/clock.png" alt="Clock" class="clock-icon">
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
                <button class="download-btn">
                    <img src="images/download.png" alt="Download" style="width: 16px; height: 16px;"> Download
                </button>

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
                        if (isset($_SESSION['auth_user']['student_id'])) {
                            $studID = $_SESSION['auth_user']['student_id'];

                            $stmt = $conn->prepare("SELECT * FROM weekly_accomplishment WHERE stud_id = ? ORDER BY date DESC, time DESC");
                            $stmt->execute([$studID]);
                            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($data as $row) {
                        ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                                    <td>Week <?= $row['week_number'] ?></td>
                                    <td><?= $row['accomplishment'] ?></td>
                                    <td><?= $row['coworkers'] ?></td>
                                    <td><?= $row['working_hours'] ?? 'N/A' ?></td>
                                </tr>
                        <?php
                            }

                            if (count($data) == 0) {
                        ?>
                                <tr>
                                    <td>16/03/2025</td>
                                    <td>Week 1</td>
                                    <td>Lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum</td>
                                    <td>Juan Dela cruz and Shiela De leon</td>
                                    <td>8</td>
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

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables -->
    <script>
        $(document).ready(function() {
            $('#accomplishmentTable').DataTable({
                "pageLength": 10,
                "searching": false, // Disable default DataTables search input
                "lengthChange": false, // Disable Show Entries dropdown
                "order": [[0, "desc"]],
                "scrollX": true 
            });

            $('#searchInput').on('keyup37', function() {
                $('#accomplishmentTable').DataTable().search(this.value).draw();
            });

            $('.download-btn').on('click', function() {
                alert("Download functionality needs to be implemented separately.");
            });
        });

        function searchTable() {
            var input = document.getElementById("searchInput").value;
            $('#accomplishmentTable').DataTable().search(input).draw();
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