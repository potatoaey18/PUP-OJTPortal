<?php

include '../connection/config.php';
error_reporting(0);

session_start();
?>

<!DOCTYPE html>
<html style="font-size: 16px;" lang="en"><head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>OJT Web Portal</title>
    <link rel="stylesheet" href="css/nicepage.css" media="screen">
<link rel="stylesheet" href="css/Page-1.css" media="screen">
    <script class="u-script" type="text/javascript" src="js/jquery.js" defer=""></script>
    <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 2rem;
            background: url(./images/itech.jpg);
            height: 94vh;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            overflow: hidden;
        }
        h5{
            font-size: 18px;
            font-weight: 400;
            margin-bottom: 20px;  
            align-items: center;
            justify-content: center;
            display: flex;
            line-height: 0.5;
        }
        i {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;  
            align-items: center;
            justify-content: center;
            display: flex;
            line-height: 0.5;
        }
    </style>
</head>
  <body>
    <section class="u-clearfix u-section-1" id="sec-ae5f">
      <div class="u-clearfix u-sheet u-sheet-1">
        <div class="u-border-3 u-border-grey-75 u-container-style u-group u-radius-17 u-shape-round u-group-1">
          <div class="u-container-layout u-container-layout-1">
          <img class="u-image u-image-contain u-image-default u-image-1" src="images/pupLogo.png" alt="" style="max-height: 100px; align-items: center; justify-content: center; display: flex; margin: 10px auto;">
            <h5>Polytechnic University of the Philippines</h5>
            <i>Institute of Technology</i>
            <h5>Administrator Login</h5>
            <div class="u-form u-form-1">
            <form action="../php/admin_loginCode.php" method="POST">
                <div class="u-form-group u-form-name u-label-none">
                  <label for="studEMAIL" class="u-label">Email</label>
                  <input type="email" placeholder="Enter your Email" id="studEMAIL" name="adminEmail" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-1" required="">
                </div>
                <div class="u-form-email u-form-group u-label-none">
                  <label for="pword" class="u-label">Password</label>
                  <input type="password" placeholder="Enter Password" id="pword" name="adminPword" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-2" required="">
                </div>
                <br>
                <div class="u-align-center u-form-group u-form-submit">
                <button name="LogIn" class="u-border-none u-btn u-btn-round u-btn-submit u-button-style u-grey-70 u-hover-palette-1-dark-1 u-radius-28 u-btn-1">Log In</button>
                </div>
              </form>
              <br>
            </div>
            <p class="u-text u-text-default u-text-1">Don't have account ? <a href="admin_register.php"> Sign Up Here</a></p>
            <p class="u-text u-text-default u-text-2"><a href="#">Forgotten Password?</a></p>
          </div>
        </div>
      </div>
    </section>
  
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
	<script src="js/scripts.js"></script>

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

</html>