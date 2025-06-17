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

    <title>OJT Web Portal: Student Profile</title>
    <!-- ================= Favicon ================== -->
    <link rel="shortcut icon" href="images/pupLogo.png">
    
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
        }
        
        .container-fluid {
            padding: 0;
            height: 100%;
        }

        .row {
            margin: 0;
            height: 100%;
        }

        .col-lg-12 {
            padding: 0;
            height: 100%;
        }
        
        .card {
            background: #fff;
            position: relative;
            border: 0;
            border-radius: 0;
            padding: 20px;
            height: 100%;
            width: 100%;
            box-sizing: border-box;
            margin: 0;
        }

        .page-title {
            padding: 10px;
            margin-bottom: 20px;
        }

        .feature-box {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 430px;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .feature-box:hover {
            transform: translateY(-5px);
        }

        .feature-content {
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .feature-icon {
            width: 75px;
            height: 75px;
            margin-right: 25px;
            flex-shrink: 0;
        }

        .feature-icon1 {
            width: 85px;
            height: 85px;
            margin-right: 25px;
            margin-top: -10px;
            flex-shrink: 0;
        }

        .feature-icon img {
            width: 75px;
            height: 75px;
            object-fit: contain;
            filter: invert(13%) sepia(80%) saturate(4729%) hue-rotate(349deg) brightness(56%) contrast(83%);
        }

        .feature-icon1 img {
            width: 85px;
            height: 85px;
            object-fit: contain;
            filter: invert(13%) sepia(80%) saturate(4729%) hue-rotate(349deg) brightness(56%) contrast(83%);
        }

        .feature-text {
            flex: 1;
            min-width: 0;
            margin-top: 15px;
        }

        .feature-text1 {
            flex: 1;
            min-width: 0;
        }

        .feature-title {
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 12px;
            color: #333;
        }

        .feature-description {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
        }

        .features-container {
            display: flex;
            justify-content: center;
            gap: 10rem;
            padding: 20px 0;
            box-sizing: border-box;
            height: calc(100% - 15rem);
            align-items: center;
            position: relative;
        }

        .features-container::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 1px;
            height: 40%;
            background-color: rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
<?php // include 'chatbot.php'; ?>
    <?php include('templates/supervisor_navbar.php'); ?>

    <div class="content-wrap">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="page-title">
                            <h1 style="font-size: 16px;"><b>Check Intern's Update</b></h1>
                        </div>
                        
                        <div class="features-container">
                     
                            <a href="daily_time_records.php" class="feature-box">
                                <div class="feature-content">
                                    <div class="feature-icon">
                                        <img src="images/clock1.png" alt="Clock Icon" id="clockIcon">
                                    </div>
                                    <div class="feature-text">
                                        <h4 class="feature-title">Daily Time Record</h4>
                                        <p class="feature-description">Check the intern's Daily Time Record for transparency. If you notice any dishonesty, please report or message the OJT adviser.</p>
                                    </div>
                                </div>
                            </a>

               
                            <a href="weekly_accomplishment.php" class="feature-box">
                                <div class="feature-content">
                                    <div class="feature-icon1">
                                        <img src="images/calendar.png" alt="Calendar Icon" id="calendarIcon">
                                    </div>
                                    <div class="feature-text1">
                                        <h4 class="feature-title">Weekly Accomplishment</h4>
                                        <p class="feature-description">Check the intern's Weekly Accomplishment for transparency. If you notice any dishonesty, please report or message the OJT adviser.</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>

    <script src="js/lib/preloader/pace.min.js"></script>\
    <script src="js/lib/bootstrap.min.js"></script>

    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</body>
</html>