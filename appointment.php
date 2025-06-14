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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .appointment-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .appointment-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
        }
        
        .meetings-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        
        .meeting-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .meeting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .meeting-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .meeting-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            color: #4a90e2;
        }
        
        .meeting-type {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .meeting-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .detail-row {
            display: flex;
            align-items: flex-start;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
            min-width: 80px;
            margin-right: 10px;
        }
        
        .detail-value {
            color: #666;
            flex: 1;
            word-break: break-all;
        }
        
        .meeting-link {
            color: #4a90e2;
            text-decoration: none;
        }
        
        .meeting-link:hover {
            text-decoration: underline;
        }
        
        .no-meetings {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .meetings-grid {
                flex-direction: column;
                align-items: center;
            }
            
            .meeting-card {
                width: 100%;
                max-width: 400px;
            }
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

        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            overflow-y: auto;
        }

        .page-title {
            padding: 10px;
            margin-bottom: 20px;
        }

    </style>
    </style>
</head>

</head>
<body>
    <?php require_once 'templates/supervisor_navbar.php'; ?>

    <div class="content-wrap">
        <div class="profile-container">
            <div class="appointment-container">
                <div class="page-header">
                <div class="page-title">
                            <h1 style="font-size: 16px;"><b>Appointment Meetings</b></h1>
                        </div>
                </div>
                <div class="meetings-grid">
                <?php
                try {
                    function getMeetingPlatform($url) {
                        $url = strtolower($url);
                        if (strpos($url, 'zoom.') !== false) {
                            return [
                                'name' => 'Zoom Meeting',
                                'icon' => 'images/platform-icons/zoom1.png'
                            ];
                        } elseif (strpos($url, 'meet.google.') !== false || strpos($url, 'google.com/meet') !== false) {
                            return [
                                'name' => 'Google Meeting',
                                'icon' => 'images/platform-icons/zoom1.png'
                            ];
                        } elseif (strpos($url, 'teams.') !== false || strpos($url, 'microsoft.com/teams') !== false) {
                            return [
                                'name' => 'Teams Meeting',
                                'icon' => 'images/platform-icons/zoom1.png'
                            ];
                        } elseif (strpos($url, 'webex.') !== false) {
                            return [
                                'name' => 'Webex Meeting',
                                'icon' => 'images/platform-icons/zoom1.png'
                            ];
                        } elseif (strpos($url, 'skype.') !== false) {
                            return [
                                'name' => 'Skype Meeting',
                                'icon' => 'images/platform-icons/zoom1.png'
                            ];
                        } else {
                            return [
                                'name' => 'Online Meeting',
                                'icon' => 'images/platform-icons/zoom1.png'
                            ];
                        }
                    }

                    $supervisor_id = $_SESSION['auth_user']['supervisor_id'];
                    
                    $query = "SELECT * FROM meetings 
                             WHERE supervisor_id = :supervisor_id 
                             AND meeting_date >= CURRENT_DATE()
                             ORDER BY meeting_date ASC, meeting_time ASC 
                             LIMIT 3";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':supervisor_id', $supervisor_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($result) > 0) {
                        foreach ($result as $row) {
                            $meetingPlatform = getMeetingPlatform($row['meeting_link']);
                            ?>
                            <div class="meeting-card">
                                <div class="meeting-header">
                                    <img src="<?php echo htmlspecialchars($meetingPlatform['icon']); ?>" class="meeting-icon" alt="">
                                    <span class="meeting-type"><?php echo htmlspecialchars($meetingPlatform['name']); ?></span>
                                </div>
                                
                                <div class="meeting-details">
                                    <div class="detail-row">
                                        <span class="detail-label">Link</span>
                                        <span class="detail-value"><a href="<?php echo htmlspecialchars($row['meeting_link']); ?>" class="meeting-link" target="_blank"><?php echo htmlspecialchars($row['meeting_link']); ?></a></span>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <span class="detail-label">Passcode</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($row['meeting_passcode']); ?></span>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <span class="detail-label">Date</span>
                                        <span class="detail-value"><?php echo date('F j, Y', strtotime($row['meeting_date'])); ?></span>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <span class="detail-label">Time</span>
                                        <span class="detail-value"><?php echo date('g:i A', strtotime($row['meeting_time'])); ?></span>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <span class="detail-label">Agenda</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($row['meeting_agenda']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="no-meetings">There are no pending meetings.</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="no-meetings">There are no pending meetings.</div>';
                }
                ?>
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