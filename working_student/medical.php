<?php
include '../../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['student_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Medical Preference</title>
    <link rel="shortcut icon" href="images/Picture1.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="endorsement-css/endorsement-moa.css" rel="stylesheet">
    <style>
        .medical-process {
            display: flex;
            justify-content: space-between;
            padding: 20px 100px;
        }
        .medical-private, .medical-campus {
            flex: 1;
            margin: 0 1rem;
            text-align: center;
        }
        .medical-private a, .medical-campus a {
            display: block;
            background-color: #a00000;
            color: white;
            padding: 2rem;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: bold;
            height: 300px;
            align-content: center;
            justify-content: center;
        }
        .medical-private a:hover, .medical-campus a:hover {
            background-color: #800000;
        }
        .medical-note {
            display: flex;
            justify-content: space-between;
            margin: 1rem;
            padding: 0px 120px;
        }
        .private-medical, .campus-medical {
            flex: 1;
            margin: 0 1rem;
            color: #666;
            font-size: 0.9rem;
        }
        .page-header p {
            color: #666;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php require_once 'templates/stud_navbar.php'; ?>

    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto; position: relative;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div>
                <div>
                    <a href="pre-internship_documentations.php" class="back-button">
                        <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                        Back
                    </a>
                </div>
                <div class="page-header">
                    <div class="page-title">
                        <h1 style="font-size: 16px;"><b>Medical</b></h1>
                        <p>Please choose your preference.</p>
                    </div>
                </div>
                    <div class="medical-process">
                        <div class="medical-private">
                            <a href="medical-private.php">Private Medical Facility</a>
                        </div>
                        <div class="medical-campus">
                            <a href="medical-campus.php">At the Campus</a>
                        </div>
                    </div>
                <div class="medical-note">
                    <div class="private-medical">
                        <p>Note: If you choose this, you may now begin your medical examination outside the campus. Simply upload your medical certificate afterwards.</p>
                    </div>
                    <div class="campus-medical">
                        <p>Note: By choosing this, you'll be listed on the medical schedule and just need to wait for further announcement regarding your section's medical schedule.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>

    <?php
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
        <script>
            sweetAlert("<?php echo $_SESSION['alert'] ?? 'Notice'; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
        </script>
    <?php
        unset($_SESSION['status']);
    }
    ?>
</body>
</html>