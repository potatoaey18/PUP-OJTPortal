<?php

include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if($_SESSION['auth_user']['supervisor_id']==0){
  echo"<script>window.location.href='index.php'</script>";
}

if (isset($_POST['upload']) && isset($_FILES['img_student']) && $_FILES['img_student']['error'] == UPLOAD_ERR_OK) {
    $supervisorID = $_SESSION['auth_user']['supervisor_id'];

    $uploadDirectory = '../student_file_images/';
    $uniqueFilename = uniqid() . '-' . basename($_FILES['img_student']['name']);
    $imagePath = $uploadDirectory . $uniqueFilename;

 
    $sql = $conn->prepare("SELECT supervisor_profile_picture FROM supervisor WHERE id = ?");
    $sql->execute([$supervisorID]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $currentImagePath = isset($row['supervisor_profile_picture']) ? $row['supervisor_profile_picture'] : '';


    if (!empty($currentImagePath) && file_exists($currentImagePath)) {
        unlink($currentImagePath);
    }

    if (move_uploaded_file($_FILES['img_student']['tmp_name'], $imagePath)) {
        $sql = $conn->prepare("UPDATE supervisor SET supervisor_profile_picture = ? WHERE id = ?");
        if ($sql->execute([$imagePath, $supervisorID])) {
            date_default_timezone_set('Asia/Manila');
            $date = date('F / d l / Y');
            $time = date('g:i A');
            $logs = 'Profile picture updated successfully.';
           

            $_SESSION['alert'] = "Success...";
            $_SESSION['status'] = "Image Updated";
            $_SESSION['status-code'] = "success";
        } else {
            $_SESSION['alert'] = "Failed!";
            $_SESSION['status'] = "Database update failed";
            $_SESSION['status-code'] = "error";
        }
    } else {
        $_SESSION['status'] = "Failed to move the uploaded image";
        $_SESSION['status-code'] = "error";
    }
}

$supervisorID = $_SESSION['auth_user']['supervisor_id'];
$stmt = $conn->prepare("SELECT * FROM supervisor WHERE id = ?");
$stmt->execute([$supervisorID]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$skillsStmt = $conn->prepare("SELECT * FROM supervisor_skills WHERE supervisor_id = ?");
$skillsStmt->execute([$supervisorID]);
$skills = $skillsStmt->fetchAll(PDO::FETCH_ASSOC);

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

        .profile-card {
            height: 100%;
        }

        .profile-content {
            height: 100%;
            display: flex;
            padding: 20px 20px 0;
        }
        
        .profile-image {
            flex: 0 0 400px;
            text-align: center;
            padding: 20px;
        }
        
        .image-placeholder {
            width: 400px;
            height: 400px;
            margin: 0 auto;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
            background-color: #f8f8f8;
            display: flex;
            align-items: left;
            justify-content: center;
            overflow: hidden;
            border: 10px solid #D9D9D9;
        }
        
        .image-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .placeholder-icon {
            width: 100px;
            height: auto;
            opacity: 0.3;
        }
        
        .choose-text {
            margin-top: 10px;
            color: #888;
            font-size: 14px;
        }
        
        .student-info {
            flex: 1;
            padding: 10px 120px;
        }
        
        .student-name {
            color: #000000;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 10px;
        }

        .company-info {
            flex: 1;
            max-width: 60%;
        }
        
        .company-address {
            font-size: 16px;
            margin-top: 10px;
            font-weight: normal;
            color: #555;
        }
        
        .student-badge {
            background-color: #ffc107;
            color: #333;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            white-space: nowrap;
            margin-left: -5px;
        }
        
        .student-id {
            Address: <?php echo isset($data['company_address']) ? $data['company_address'] : 'N/A'; ?>
        }
        <br>
        .student-details {
            margin-top: 20px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 4px;
            align-items: baseline;
        }
        
        .detail-label {
            width: 140px;
            color: rgba(0, 0, 0, 0.8);
            white-space: nowrap;
        }
        
        .detail-value {
            flex: 1;
            color: #000000;
            font-weight: bold;
            margin-left: 30px;
        }

        .detail-value:before {
            content: ': ';
            margin-right: 4px;
        }
        
        .abnormal {
            color: #dc3545;
            font-weight: bold;
        }
        
        .upload-form {
            margin-top: 15px;
        }
        
        .upload-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
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

    <div class="content-wrap" style="height: 100%; width: 100%; margin: 0 auto; overflow-y: auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
    <div class="page-header">
                            <div class="page-title">
                                <h1 style="font-size: 16px;"><b>PROFILE</b></h1>
                            </div>
                        </div>
                  <div class="profile-card">
                      <div class="profile-content">
                      <div class="profile-image">
                              <div class="image-placeholder" onclick="document.getElementById('profile-input').click();">
                                  <?php if(!empty($data['supervisor_profile_picture']) && file_exists($data['supervisor_profile_picture'])): ?>
                                      <img src="<?php echo $data['supervisor_profile_picture']; ?>" alt="Profile Image">
                                  <?php else: ?>
                                      <img src="images/placeholder.png" alt="Profile Placeholder" class="placeholder-icon">
                                  <?php endif; ?>
                              </div>
                              
                              <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
                                  <input type="file" name="img_student" id="profile-input" onchange="uploadImage(event)" required accept="image/*" style="display: none;">
                                  <input type="submit" name="upload" id="upload-submit" style="display: none;">
                              </form>
                          </div>
                    
                    <div class="student-info">
                        <div class="student-name">
                            <div class="company-info">
                                <?php echo isset($data['company_name']) ? $data['company_name'] : 'N/A'; ?>
                                <div class="company-address" style="font-size: 16px; margin-top: 10px;">
                                    Address: <?php echo isset($data['company_address']) ? $data['company_address'] : 'N/A'; ?>
                                </div>
                            </div>
                            <span class="student-badge">HTE</span>
                        </div>
                        <br><br><br>
                        <div class="student-details">
                            <div class="detail-row">
                                <div class="detail-label">About</div>
                                <div class="detail-value"><?php echo isset($data['about']) ? $data['about'] : 'N/A'; ?></div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Name of Contact Person</div>
                                <div class="detail-value">
                                    <?php 
                                    $fullName = [];
                                    if (isset($data['first_name'])) $fullName[] = $data['first_name'];
                                    if (isset($data['middle_name']) && !empty(trim($data['middle_name']))) $fullName[] = $data['middle_name'];
                                    if (isset($data['last_name'])) $fullName[] = $data['last_name'];
                                    
                                    echo !empty($fullName) ? htmlspecialchars(implode(' ', $fullName), ENT_QUOTES, 'UTF-8') : 'N/A'; 
                                    ?>
                                </div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">Contact Number</div>
                                <div class="detail-value"><?php echo isset($data['phone_number']) ? $data['phone_number'] : 'N/A'; ?></div>
                            </div>
                            
                            
                            
                            <div class="detail-row">
                                <div class="detail-label">Email Address</div>
                                <div class="detail-value"><?php echo isset($data['supervisor_email']) ? $data['supervisor_email'] : 'N/A'; ?></div> 
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">Position</div>
                                <div class="detail-value"><?php echo isset($data['position']) ? $data['position'] : 'N/A'; ?></div>
                            </div>
                            
                            <br><br><br>


                            <?php
                            $moa_details = [];
                            $start_date = 'N/A';
                            $end_date = 'N/A';
                            $validity_years = 'N/A';
                            $remaining_time = 'N/A';
                            
                            if (isset($_SESSION['auth_user']['supervisor_id'])) {
                                $supervisor_id = $_SESSION['auth_user']['supervisor_id'];
                                require_once '../connection/config.php';
                                
                                $moa_stmt = $conn->prepare("SELECT start_date_validity, end_date_validity FROM moa_form WHERE supervisor_id = ? ORDER BY created_at DESC LIMIT 1");
                                $moa_stmt->execute([$supervisor_id]);
                                $moa_details = $moa_stmt->fetch(PDO::FETCH_ASSOC);
                                
                                if ($moa_details && !empty($moa_details['start_date_validity']) && !empty($moa_details['end_date_validity'])) {
                                    $start_date_obj = new DateTime($moa_details['start_date_validity']);
                                    $end_date_obj = new DateTime($moa_details['end_date_validity']);
                                    $today = new DateTime();
                                    
                                    $start_date = $start_date_obj->format('F j, Y');
                                    $end_date = $end_date_obj->format('F j, Y');
                                    
                                    $interval = $start_date_obj->diff($end_date_obj);
                                    $validity_years = $interval->y . ($interval->y != 1 ? ' Years' : ' Year');
                                    
                                }
                            }
                            ?>
                            
                            <div class="detail-row">
                                <div class="detail-label">MOA Validated</div>
                                <div class="detail-value"><?php echo $start_date; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">MOA Expiration</div>
                                <div class="detail-value"><?php echo $end_date; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">MOA Validity</div>
                                <div class="detail-value"><?php echo $validity_years; ?></div>
                            </div>
                            
                        </div><br><br><br><br><br><br><br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.querySelector('.image-placeholder img');
                output.src = reader.result;
                output.classList.remove('placeholder-icon');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    <script>
      function uploadImage(event) {

          previewImage(event);
          

          document.getElementById('upload-submit').click();
      }

      function previewImage(event) {
          const file = event.target.files[0];
          if (file) {
              const reader = new FileReader();
              reader.onload = function(e) {
                  const placeholder = document.querySelector('.image-placeholder img');
                  placeholder.src = e.target.result;
              }
              reader.readAsDataURL(file);
          }
      }
    </script>

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