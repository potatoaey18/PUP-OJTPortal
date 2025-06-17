<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    echo "<script>window.location.href='index.php'</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Appointment</title>
    <link rel="shortcut icon" href="images/pupLogo.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        @import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap');
        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            overflow-y: auto;
        }
        .content-wrap {
            width: 100%;
            margin: 0 auto;
            position: relative;
            padding-left: 19.5rem; 
            padding-top: 7rem; 
        }

        .profile-container {
            background-color: #fff;
            position: fixed;
            top: 7rem;
            left: 19.5rem;
            right: 0;
            bottom: 0;
            padding: 2rem;
            overflow-y: auto;
        }


        .page-title p {
            color: #000;
            margin-bottom: 4px;
        }

        .back-button {
            font-size: 1rem;
            margin-left: -10px;
            position: relative;
            top: -8px;
        }
        .back-button:hover {
            color: #700000 !important;
        }
        .back-button img {
            opacity: 0.7;
            transition: opacity 0.2s, filter 0.2s;
        }
        .back-button:hover img {
            opacity: 1;
            filter: brightness(0) saturate(100%) invert(15%) sepia(91%) saturate(7476%) hue-rotate(346deg) brightness(47%) contrast(110%);
        }

        .moa-status-container {
            border: 3px solid #700000;
            border-radius: 15px;
            padding: 18px 20px;
            margin-top: 2rem;
            background: none;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }

        .moa-timeline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-top: 32px;
    margin-bottom: 8px;
    position: relative;
    width: 100%;
}
.moa-timeline::before {
    content: '';
    position: absolute;
    top: 18px;
    left: 11.5%; 
    right: 11.5%; 
    height: 6px;
    background: #70000022;
    z-index: 0;
    border-radius: 3px;
}

.moa-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1 1 0;
    position: relative;
    z-index: 2;
    background: transparent;
}
.moa-step:not(:first-child) .moa-step-icon::before {
    content: '';
    position: absolute;
    top: 50%;
    left: -50%;
    width: 100%;
    height: 6px;
    background: #70000022;
    z-index: -1;
    transform: translateY(-50%);
    border-radius: 3px;
    display: none;
}
.moa-step-active .moa-step-icon {
    background: #700000;
    border-color: #700000;
}
.moa-step-active .moa-step-icon svg {
    stroke: #fff;
}
.moa-step-label {
    color: #700000;
    font-size: 13px;
    text-align: center;
    margin-top: 6px;
    font-weight: 500;
}
.moa-timeline .moa-step.moa-step-active ~ .moa-step .moa-step-icon,
.moa-timeline .moa-step.moa-step-active .moa-step-icon {
    border-color: #700000;
}
.moa-timeline .moa-step.moa-step-active ~ .moa-step .moa-step-icon::before {
    background: #70000022;
}
.moa-timeline .moa-step.moa-step-active .moa-step-icon::before {
    background: #700000;
}
.moa-step-icon {
    background: #fff;
    border: 4px solid #700000;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 6px;
    position: relative;
    z-index: 3;
}
.moa-step-plain {
    background: #700000 !important;
    border-color: #700000 !important;
}

.moa-step-label {
    color: #700000;
    font-size: 13px;
    text-align: center;
    margin-top: 6px;
    font-weight: 500;
}
.moa-step-img {
    width: 25px !important;
    height: 25px !important;
    margin-top: 10px !important;
    display: block;
    filter: brightness(0) saturate(100%) invert(14%) sepia(99%) saturate(7499%) hue-rotate(349deg) brightness(40%) contrast(110%);
}
    </style>


</head>
<body>
    <?php require_once 'templates/supervisor_navbar.php'; ?>

    <div class="content-wrap">
        <div class="profile-container">
            <div class="page-header">
                <div class="page-title">
                    <a href="javascript:history.back()" class="back-button">
                        <img src="images/less-than.png" alt="Back" style="width: 30px; height: 30px; color: #333;">
                        Back
                    </a>
                    <br><br>
                    <h1 style="font-size: 17px;"><b>Application for renewal of MOA</b></h1>
                    <br>
                    <div class="page-information">
    <p class="required-hours">You can monitor your MOA application status here</p>
    <br>
    <div class="moa-status-container">
    <span style="color:#700000;font-weight:bold; font-size:22px; margin-left:15px; display:inline-block; margin-bottom:28px;">Process Timeline</span>
    <div class="moa-timeline">
        <div class="moa-step moa-step-active">
    <div class="moa-step-icon moa-step-plain"></div>
    <img class="moa-step-img" src="images/analyse.png" alt="Analyse" />
    <div class="moa-step-label">Checking the<br>Informations</div>
</div>
        <div class="moa-step">
    <div class="moa-step-icon moa-step-plain"></div>
    <img class="moa-step-img" src="images/review.png" alt="Review" />
    <div class="moa-step-label">For ULCO's review<br>and approval</div>
</div>
        <div class="moa-step">
    <div class="moa-step-icon moa-step-plain"></div>
    <img class="moa-step-img" src="images/return.png" alt="Return" />
    <div class="moa-step-label">MOA returned to<br>OJT coordinator</div>
</div>
        <div class="moa-step">
    <div class="moa-step-icon moa-step-plain"></div>
    <img class="moa-step-img" src="images/signature-pen.png" alt="Signature" />
    <div class="moa-step-label">For Dean and<br>VPAA's signature.</div>
</div>
        <div class="moa-step">
    <div class="moa-step-icon moa-step-plain"></div>
    <img class="moa-step-img" src="images/like (1).png" alt="Signed" />
    <div class="moa-step-label">MOA has already<br>signed.</div>
</div>
    </div>
</div>
<p class="moa-renew-note" style="margin-top:40px; font-size:13px; color:#700000; background:#fff6f6; border-left:3px solid #700000; padding:7px 13px; border-radius:4px; margin-bottom:18px;">
    <b>Note:</b> Make sure to submit the filled-out MOA template along with the HTE's SEC or DTI registration to avoid errors and repeating the process.
</p>
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


    <?php
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
        ?>
        <script>
            sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
        </script>
        <?php
        unset($_SESSION['status']);
    } 
    ?>
</body>
</html>

