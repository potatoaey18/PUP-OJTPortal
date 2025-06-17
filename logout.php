<?php 
include '../connection/config.php';
session_start();

if(isset($_SESSION['auth_user']['user_id'])){

    $stud_id = $_SESSION['auth_user']['user_id'];

date_default_timezone_set('Asia/Manila');
$date = date('F / d l / Y');
$time = date('g:i A');
$logs = 'You successfully logged out to your account.';
$online_offline_status = 'Offline';

$sql = $conn->prepare("INSERT INTO system_notification(user_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
$sql->execute([$stud_id, $logs, $date, $time]);

$sql2 = $conn->prepare("UPDATE pending_users SET online_offlineStatus = ? WHERE id = ?");
$sql2->execute([$online_offline_status, $stud_id]);

}

session_destroy();

header("Location: login.php");

?>