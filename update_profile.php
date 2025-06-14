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

    <title>OJT Web Portal: Appointment</title>
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
            background-color: #f0f0f0;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .content-wrap {
            height: 100vh;
            width: 100%;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        .profile-container {
            background-color: white;
            position: fixed;
            top: 7rem;
            left: 19.5rem;
            right: 0;
            bottom: 0;
            padding: 2rem;
            overflow: hidden;
        }

        .meetings-container {
            margin-top: 8rem;
            display: flex;
            flex-wrap: nowrap;
            gap: 8.5rem;
            justify-content: center;
            padding: 0 1rem;
            height: calc(55% - 25px);
            position: relative;
            overflow: hidden;
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        .meeting-box {
            background: #fff;
            border: 1px solid #000000;
            border-radius: 12px;
            padding: 2rem;
            width: 350px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .meeting-box h3 {
            margin-top: 0;
            color: #333;
            font-size: 22px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4rem;
            position: relative;
            text-align: center;
            min-height: 40px;
        }

        .meeting-box h3 img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            position: absolute;
            left: 0;
        }

        .meeting-box h3 span {
            margin: 0 auto;
        }

        .meeting-box hr {
            border: none;
            border-top: 1px solid #000000;
            margin: 20px 0;
            margin-left: -32px;
            width: 123%;
        }

        .meeting-details {
            margin-bottom: 16px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
        }

        .meeting-details-container {
            margin-top: 30px;
        }

        .meeting-details label {
            font-weight: bold;
            width: 90px;
            color: #000000;
            font-size: 16px;
            flex-shrink: 0;
            position: relative;
        }

        .meeting-details label::after {
            content: ':';
            position: absolute;
            right: 10px;
            left: 100px;
        }

        .meeting-details a {
            color: #000000;
            text-decoration: none;
            word-break: break-all;
            margin-left: 25px;
            transition: all 0.3s ease;
            position: relative;
            padding: 2px 0;
        }

        .meeting-details a:hover {
            text-decoration: underline;
            color: #0056b3; 
            transform: translateX(2px);
        }

        .meeting-details a:hover::after {
            position: absolute;
            right: -10px;
            top: 50%;
            transform: translateY(-50%);
            color: #0056b3;
        }

        .meeting-details span {
            color: #000000;
            word-break: break-all;
            margin-left: 25px;
        }



        .no-meetings {
            text-align: center;
            padding: 40px;
            color: rgb(92, 92, 92);
            font-size: 16px;
            font-weight: bold;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
        }
    </style>
</head>

<body>
    <!---------NAVIGATION BAR-------->
    <?php // include 'chatbot.php'; ?>
    <?php
    require_once 'templates/supervisor_navbar.php';
    ?>
    <!---------NAVIGATION BAR ENDS-------->

    <div class="content-wrap">
        <div class="profile-container">
            <div class="page-header">
                <div class="page-title">
                    <h1 style="font-size: 16px;"><b>Settings</b></h1>
<p style="font-size: 15px; color: #555; margin-top: 10px; margin-bottom: 18px;">
    Note: You are only allowed to edit some of your basic information. If you notice any incorrect non-editable information, kindly message the admin so they can make the necessary changes.
</p>

<form style="margin-top: 18px;">
  <div style="display: flex; flex-wrap: wrap; gap: 48px;">
    <div style="flex: 1 1 320px; min-width: 320px; max-width: 480px;">
      <label for="about" style="font-size:15px; color:#232323; margin-bottom:6px; display:block;">About</label>
      <input type="text" id="about" name="about" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" value="IT Services and IT Consulting">
      <label for="contact_number" style="font-size:15px; color:#232323; margin-bottom:6px; display:block;">Contact Number</label>
      <input type="text" id="contact_number" name="contact_number" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" value="(+63)9196058276">
      <label for="moa_validated" style="font-size:15px; color:#232323; margin-bottom:6px; display:block;">MOA Validated</label>
      <input type="text" id="moa_validated" name="moa_validated" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" value="April 4, 2024">
    </div>
    <div style="flex: 1 1 320px; min-width: 320px; max-width: 480px;">
      <label for="contact_person" style="font-size:15px; color:#232323; margin-bottom:6px; display:block;">Name of Contact Person</label>
      <input type="text" id="contact_person" name="contact_person" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" value="Ian Caranao">
      <label for="email" style="font-size:15px; color:#232323; margin-bottom:6px; display:block;">Email Address</label>
      <input type="email" id="email" name="email" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" value="careers.pixel8@gmail.com">
      <label for="moa_expiration" style="font-size:15px; color:#232323; margin-bottom:6px; display:block;">MOA Expiration</label>
      <input type="text" id="moa_expiration" name="moa_expiration" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" value="April 4, 2027">
    </div>
    <div style="flex: 1 1 320px; min-width: 320px; max-width: 480px;">
      <label for="position" style="font-size:15px; color:#232323; margin-bottom:6px; display:block;">Position</label>
      <input type="text" id="position" name="position" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" value="HR/Admin Assistant">
    </div>
  </div>
  <button type="submit" style="margin-top:32px; background:#161689; color:#fff; border:none; border-radius:4px; padding:12px 38px; font-size:18px; font-weight:500; cursor:pointer;">Update changes</button>
</form>

<br>

<p style="font-size: 15px; color: #555; margin-top: 10px; margin-bottom: 18px;">
    Change Password
</p>
<form style="margin-top: 10px;">
  <div style="display: flex; flex-wrap: wrap; gap: 48px;">
    <div style="flex: 1 1 320px; min-width: 320px; max-width: 480px;">
      <label for="current_password" style="font-size:14px; color:#232323; margin-bottom:6px; display:block;">Current Password</label>
      <input type="password" id="current_password" name="current_password" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" placeholder="Enter your current password">
    </div>
    <div style="flex: 1 1 320px; min-width: 320px; max-width: 480px;">
      <label for="new_password" style="font-size:14px; color:#232323; margin-bottom:6px; display:block;">New Password</label>
      <input type="password" id="new_password" name="new_password" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" placeholder="Enter new password">
    </div>
    <div style="flex: 1 1 320px; min-width: 320px; max-width: 480px;">
      <label for="repeat_password" style="font-size:14px; color:#232323; margin-bottom:6px; display:block;">Repeat New Password</label>
      <input type="password" id="repeat_password" name="repeat_password" class="form-control" style="margin-bottom:24px; border: 1px solid #222; border-radius:8px; font-size:16px; padding:16px 18px; height:50px;" placeholder="Re-Enter new password">
    </div>
  </div>
  <button type="submit" style="margin-top:32px; background:#161689; color:#fff; border:none; border-radius:4px; padding:12px 38px; font-size:18px; font-weight:500; cursor:pointer;">Update Password</button>
</form>

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
        sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
        </script>
    <?php
    unset($_SESSION['status']);
    }
    ?>
</body>
</html>