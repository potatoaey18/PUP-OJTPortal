<?php
include '../connection/config.php';
session_start();

if($_SESSION['auth_user']['supervisor_id']==0){
    echo"<script>window.location.href='index.php'</script>";
}

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='daily_time_records.php'</script>";
    exit;
}

$id = $_GET['id'];
$query = "SELECT * FROM daily_time_records WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    echo "<script>window.location.href='daily_time_records.php'</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: View DTR</title>
    <link rel="shortcut icon" href="images/pupLogo.png">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #F1F1F1;
            margin: 0;
            padding: 0;
        }
        .content-wrap {
            margin-left: 19.5rem;
            margin-top: 7rem;
            padding: 2rem;
            background-color: #fff;
            min-height: calc(100vh - 7rem);
        }
        .dtr-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .back-button {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
            color: #333;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        .back-button:hover {
            color: #700000;
        }
        .dtr-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #700000;
        }
        .dtr-info {
            margin-bottom: 2rem;
        }
        .dtr-info p {
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .dtr-info strong {
            color: #700000;
        }
    </style>
</head>
<body>
    <?php include('templates/supervisor_navbar.php'); ?>
    
    <div class="content-wrap">
        <a href="daily_time_records.php" class="back-button">
            <img src="images/less-than.png" alt="Back" style="width: 30px; height: 30px;">
            Back
        </a>
        
        <div class="dtr-container">
            <div class="dtr-header">
                <h2>Daily Time Record</h2>
            </div>
            
            <div class="dtr-info">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($record['full_name']); ?></p>
                <p><strong>SIS No:</strong> <?php echo htmlspecialchars($record['sis_no']); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($record['assigned_dep']); ?></p>
                <p><strong>Section:</strong> <?php echo htmlspecialchars($record['section']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($record['status']); ?></p>
            </div>
            
      
            <div class="dtr-details">
            </div>
        </div>
    </div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
</body>
</html>
