<?php 
include '../connection/config.php';
session_start();

if(isset($_SESSION['auth_user']['supervisor_id'])){

    $supervisor_id = $_SESSION['auth_user']['supervisor_id'];

date_default_timezone_set('Asia/Manila');

$date = date('Y-m-d');  // Format: YYYY-MM-DD
$time = date('H:i:s');   // Format: HH:MM:SS
$logs = 'You successfully logged out to your account.';
$online_offline_status = 'Offline';

$sql = $conn->prepare("INSERT INTO supervisor_system_notification(supervisor_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
$sql->execute([$supervisor_id, $logs, $date, $time]);

$sql2 = $conn->prepare("UPDATE supervisor SET online_offlineStatus = ? WHERE id = ?");
$sql2->execute([$online_offline_status, $supervisor_id]);

}

session_destroy();
echo "<script>
  localStorage.removeItem('chatbot_history');
  localStorage.removeItem('chatbot_isOpen');
  window.location.href = 'index.php';
</script>";
exit;
?>