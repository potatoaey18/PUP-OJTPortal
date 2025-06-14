<?php
include '../../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if ($_SESSION['auth_user']['student_id'] == 0) {
    echo "<script>window.location.href='index.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Internship Documentations</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="images/Picture1.png">

    <!-- Common CSS -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
            color: #000;
        }
        .note {
            text-align: center;
            color: gray;
            margin-bottom: 20px;
        }
        .internship-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px dotted #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: inline-block;
        }
        .section {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex; /* Use flexbox for horizontal alignment */
            align-items: center; /* Vertically center the content */
            height: 100%;
            transition: transform 0.2s;
        }
        .section:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .icon-container {
            margin-right: 20px; /* Space between icon and text */
        }
        .icon-container i {
            font-size: 50px;
        }
        .text-container {
            flex: 1; 
            text-align: left; 
        }
        .section p {
            color: #7f8c8d;
            margin: 0; /* Remove default margin for better alignment */
        }
        .section-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }
        .dtr-section i {
            color: #c0392b;
        }
        .dtr-label {
            font-size: 24px;
            font-weight: bold;
            color: #c0392b;
            margin-bottom: 5px;
        }
        .weekly-section i {
            color: #c0392b;
        }
        .row {
            margin-bottom: 20px;
        }
        .title-container {
            border-bottom: 1px solid #eaeaea;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php require_once 'templates/stud_navbar.php'; ?>

    <!-- Main Content -->
    <div class="content-wrap" style="height: 80%; width: 100%;margin: 0 auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
        <div class="page-header">
                <div class="page-title">
                    <h1 style="font-size: 16px;"><b>Internship Documentation</b></h1>
                </div>
            </div><br>

            <p>Note: The Internship Experience and Photo Documentation of the actual training will only be accessible once you have completed the required internship hours.</p>

            <div style="padding: 50px;">
                <!-- 2x2 Grid Layout -->
            <div class="row">
                <!-- Daily Time Record (DTR) -->
                <div class="col-md-6 mb-4">
                    <a href="DTR.php" class="section-link">
                        <div class="section dtr-section">
                            <div class="icon-container">
                                <img src="images/dtr.png" alt="">
                            </div>
                            <div>
                                <p style="color: #000; font-size: 16px; margin-bottom: 10px;">Daily Time Record</p>
                                <p>You are expected to time in and time out daily to accurately record your intended hours.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Internship Experience -->
                <div class="col-md-6 mb-4">
                    <a href="internship_experience.php" class="section-link">
                        <div class="section inactive-section">
                            <div class="icon-container">
                                <img src="images/intern_exp.png" alt="">
                            </div>
                            <div>
                                <p style="color: #000; font-size: 16px; margin-bottom: 10px;">Internship Experience</p>
                                <p>At the end of your internship, you are expected to summarize your experience as an intern at your HTE/ workplace.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Weekly Accomplishment -->
                <div class="col-md-6 mb-4">
                    <a href="weekly_accomplishment.php" class="section-link">
                        <div class="section weekly-section">
                            <div class="icon-container">
                                <img src="images/weekly.png" alt="">
                            </div>
                            <div>
                                <p style="color: #000; font-size: 16px; margin-bottom: 10px;">Weekly Accomplishment</p>
                                <p>You are expected to submit a weekly summary report/progress update, as it will be part of your portfolio.</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Photo Documentation -->
                <div class="col-md-6 mb-4">
                    <a href="photo_documentation.php" class="section-link">
                        <div class="section inactive-section">
                            <div class="icon-container">
                                <img src="images/media.png" alt="">
                            </div>
                            <div>
                                <p style="color: #000; font-size: 16px; margin-bottom: 10px;">Photo Documentation</p>
                                <p>This could be a selfie at your workplace or a screenshot of your daily tasks. Just make sure to cover any sensitive information.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Common Scripts -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>
</body>
</html>