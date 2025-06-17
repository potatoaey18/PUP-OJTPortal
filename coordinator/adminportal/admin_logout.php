<?php 
include '../connection/config.php';
session_start();

if(isset($_SESSION['auth_user']['admin_id'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

date_default_timezone_set('Asia/Manila');
$date = date('F / d l / Y');
$time = date('g:i A');
$logs = 'You successfully logged out to your account.';
$online_offline_status = 'Offline';

$sql = $conn->prepare("INSERT INTO admin_system_notification(admin_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
$sql->execute([$admin_id, $logs, $date, $time]);

$sql2 = $conn->prepare("UPDATE admin_account SET online_offlineStatus = ? WHERE id = ?");
$sql2->execute([$online_offline_status, $admin_id]);

}

session_destroy();

header("Location: index.php");

?>