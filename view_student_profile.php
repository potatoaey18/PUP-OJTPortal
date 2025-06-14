<?php

include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if($_SESSION['auth_user']['supervisor_id']==0){
  echo"<script>window.location.href='index.php'</script>";
  exit;
}

$studID = $_GET['intern_id'];
$stmt = $conn->prepare("SELECT * FROM students_data WHERE student_ID = ?");
$stmt->execute([$studID]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$skillsStmt = $conn->prepare("SELECT * FROM stud_skills WHERE stud_id = ?");
$skillsStmt->execute([$studID]);
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
            font-family: Arial, sans-serif;
            color: #000;
            overflow: hidden;
        }
        
        .profile-card {
            max-width: 1500px;
            margin: 0 auto;
            background-color: white;
            overflow: hidden;
        }
        
        .profile-header {
            padding: 15px;
            font-weight: bold;
        }
        
        .profile-content {
            display: flex;
            padding: 20px;
            margin-top: 40px;
        }
        
        .profile-image {
            flex: 0 0 300px;
            text-align: center;
            padding: 20px;
        }
        
        .image-placeholder {
            width: 400px;
            height: 400px;
            margin: 0 auto;
            border-radius: 50%;
            border: 10px solid #D9D9D9;
            overflow: hidden;
        }
        
        .image-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .student-info {
            flex: 1;
            padding: 10px 120px;
        }
        
        .student-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 50px;
        }
        
        .student-badge {
            background-color: #ffc107;
            color: #333;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .student-details {
            margin-top: 20px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .detail-label {
            flex: 0 0 150px;
            font-weight: 500;
        }
        
        .detail-value {
            flex: 1;
            font-weight: bold;
        }
        
        .abnormal {
            color: #dc3545;
            font-weight: bold;
        }
        

    </style>
</head>

<body>
    <!---------NAVIGATION BAR-------->
    <?php
    require_once 'templates/supervisor_navbar.php';
    ?>
    <!---------NAVIGATION BAR ENDS-------->

    <div class="content-wrap" style="height: 80%; width: 100%;margin: 0 auto;">
    <div style="background-color: white; margin-top: 7rem; margin-left: 19.5rem; padding: 2rem;">
        <div style="margin-bottom: 1.5rem;">
            <a href="javascript:history.back()" style="text-decoration: none; color: #333; font-size: 20px; display: flex; align-items: center; gap: 8px; opacity: 0.7; transition: opacity 0.2s;">
                <img src="images/less-than.png" alt="Back" style="width: 35px; height: 35px; opacity: 0.7;">
                Back
            </a>
        </div>
        <div class="page-header">
                            <div class="page-title">
                                <h1 style="font-size: 21px; margin-left: 1rem;"><b>View Profile</b></h1>
                            </div>
                        </div>
                  <div class="profile-card">
                      <div class="profile-content">
                      <div class="profile-image">
                              <div class="image-placeholder">
                                  <?php if(!empty($data['profile_picture']) && file_exists($data['profile_picture'])): ?>
                                      <img src="<?php echo $data['profile_picture']; ?>" alt="Profile Image">
                                  <?php else: ?>
                                      <img src="images/placeholder.png" alt="Profile Placeholder" class="placeholder-icon">
                                  <?php endif; ?>
                              </div>
                          </div>
                    
                    <div class="student-info">
                        <div class="student-name">
                            <?php echo isset($data['first_name']) ? $data['first_name'] : ''; ?> 
                            <?php echo isset($data['middle_name']) ? $data['middle_name'] : ''; ?> 
                            <?php echo isset($data['last_name']) ? $data['last_name'] : ''; ?>
                            <span class="student-badge">Student</span>
                        </div>
                        
                        <div class="student-id">
                            Student No.: <?php echo isset($data['student_ID']) ? $data['student_ID'] : 'N/A'; ?>
                        </div>
                        <br>
                        <div class="student-details">
                            <div class="detail-row">
                                <div class="detail-label">Course</div>
                                <div class="detail-value">: <?php echo isset($data['stud_course']) ? $data['stud_course'] : 'N/A'; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">Year</div>
                                <div class="detail-value">: <?php echo isset($data['year_level']) ? $data['year_level'] : 'N/A'; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">Section</div>
                                <div class="detail-value">: <?php echo isset($data['stud_section']) ? $data['stud_section'] : 'N/A'; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">Age</div>
                                <div class="detail-value">: <?php echo isset($data['age']) ? $data['age'] . ' years old' : 'N/A'; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">OJT Adviser</div>
                                <div class="detail-value">: <?php echo isset($data['ojt_adviser']) ? $data['ojt_adviser'] : 'N/A'; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">HTE</div>
                                <div class="detail-value">: <?php echo isset($data['company']) ? $data['company'] : 'N/A'; ?></div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">Total rendered hours</div>
                                <div class="detail-value">: <?php echo isset($data['rendered_hours']) ? $data['rendered_hours'] : 'N/A'; ?></div>
                            </div>
                            <br>
                            <div class="detail-row">
                                <div class="detail-label">Medical Condition</div>
                                <div class="detail-value <?php echo (isset($data['medical_condition']) && $data['medical_condition'] == 'Abnormal') ? 'abnormal' : ''; ?>">
                                    : <?php echo isset($data['medical_condition']) ? $data['medical_condition'] : 'N/A'; ?>
                                    <br><br><br><br><br><br><br><br><br><br><br><br>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
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
        sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>", {
            allowOutsideClick: true,
            allowEnterKey: true,
            allowEscapeKey: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
        </script>
    <?php
    unset($_SESSION['status']);
    }
    ?>
</body>
</html>