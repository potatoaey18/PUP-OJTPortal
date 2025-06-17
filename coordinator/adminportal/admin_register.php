<?php

include '../connection/config.php';
error_reporting(0);

session_start();
?>


<!DOCTYPE html>
<html style="font-size: 16px;" lang="en"><head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title>Register - OJT Web Portal</title>
    <link rel="stylesheet" href="css/nicepage.css" media="screen">
<link rel="stylesheet" href="css/Page-4.css" media="screen">
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
  <body data-path-to-root="./" data-include-products="false" class="u-body u-xl-mode" data-lang="en">
    <section class="u-clearfix u-section-1" id="sec-e85b">
      <div class="u-clearfix u-sheet u-sheet-1">
        <div class="u-border-3 u-border-grey-75 u-container-style u-expanded-width-xs u-group u-radius-17 u-shape-round u-group-1">
          <div class="u-container-layout u-container-layout-1">
          <img class="u-image u-image-contain u-image-default u-image-1" src="images/pupLogo.png" alt="" style="max-height: 100px; align-items: center; justify-content: center; display: flex; margin: 10px auto;">
            <h5>Polytechnic University of the Philippines</h5>
            <i>Institute of Technology</i>
            <h5>Administrator Registration</h5>
          <div class="u-form u-form-1">

              <form action="../php/admin_registerCode.php" method="POST" enctype="multipart/form-data" class="u-clearfix u-form-spacing-20 u-form-vertical u-inner-form" style="padding: 10px">

                <div class="u-form-group u-form-name u-form-partition-factor-3 u-label-none">
                  <label for="name-3b9a" class="u-label">First Name</label>
                  <input type="text" placeholder="First Name" id="name-3b9a" name="f_name" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-1" required="true">
                </div>
                <div class="u-form-email u-form-group u-form-partition-factor-3 u-label-none">
                  <label for="email-3b9a" class="u-label">Middle Name</label>
                  <input type="text" placeholder="Middle Name" id="email-3b9a" name="m_name" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-2" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-3 u-label-none u-form-group-3">
                  <label for="text-743c" class="u-label">Last Name</label>
                  <input type="text" placeholder="Last Name" id="text-743c" name="l_name" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-3" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-3 u-label-none u-form-group-4">
                  <label for="text-7a3c" class="u-label">ID Number</label>
                  <input type="text" placeholder="ID Number" id="text-7a3c" name="id_number" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-4" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-3 u-label-none u-form-group-5">
                  <label for="text-bb89" class="u-label">Department</label>
                  <div class="u-form-select-wrapper">
                    <select id="select-f65c" name="position" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-5" required="true">
                      <option value="">Department</option>
                      <option value="College of Engineering">College of Engineering</option>
                      <option value="College of Education">College of Education</option>
                      <option value="College of Arts">College of Arts</option>
                      <option value="College of Science">College of Science</option>
                    </select>
                  </div>
                </div>
                <div class="u-form-group u-form-partition-factor-3 u-label-none u-form-group-6">
                  <label for="text-63ba" class="u-label">Complete Address</label>
                  <input type="text" placeholder="Complete Address" id="text-63ba" name="completeADDRESS" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-6" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-2 u-label-none u-form-group-7">
                  <label for="text-fd88" class="u-label">Contact Number</label>
                  <input type="number" placeholder="Contact Number" id="text-fd88" name="cpNum" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-7" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-2 u-label-none u-form-group-8">
                  <label for="text-1a97" class="u-label">Email</label>
                  <input type="email" placeholder="Email" id="text-1a97" name="eMail" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-8" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-3 u-label-none u-form-group-9">
                  <label for="text-124c" class="u-label">Password</label>
                  <input type="password" placeholder="Password" id="text-124c" name="pword" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-9" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-3 u-label-none u-form-group-10">
                  <label for="text-da7f" class="u-label">Repeat Password</label>
                  <input type="password" placeholder="Repeat Password" id="text-da7f" name="cpword" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-10" required="true">
                </div>
                <div class="u-form-group u-form-partition-factor-3 u-label-none u-form-group-11">
                  <label for="text-df1c" class="u-label">Image File</label>
                  <input type="file" accept="image/*" id="text-df1c" name="admin_pic" class="u-border-2 u-border-black u-border-no-left u-border-no-right u-border-no-top u-input u-input-rectangle u-input-11" required="true">
                </div>
                <div class="u-align-center u-form-group u-form-submit">
                  <button name="register" class="u-border-none u-btn u-btn-round u-btn-submit u-button-style u-grey-70 u-hover-palette-1-dark-1 u-radius-28 u-btn-1">Register</button>
                </div>
              </form>
            </div>
            <p class="u-text u-text-default u-text-1">Do you have an account? <a href="index.php"> Log In</a></p>
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


</body></html>