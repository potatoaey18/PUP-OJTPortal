<?php

include '../connection/config.php';
error_reporting(0);

session_start();

if(ISSET($_POST['verifyNow'])){
		
    $otp_num = $_POST['verification_number'];
    $userid = $_POST['userid'];
    $verified = 'Verified';

$stmt = $conn->prepare("SELECT verification_code FROM pending_users WHERE id=?");
$stmt->execute([$userid]);
$user = $stmt->fetch(); # get users data

if($user["verification_code"]==$otp_num)
{
$stmt = $conn->prepare("UPDATE pending_users SET verify_status=? WHERE id=?");
    $stmt->execute([$verified, $userid]);
$_SESSION['alert'] = "Success!";
$_SESSION['status'] = "User Account Verified. Log In Again.";
$_SESSION['status-code'] = "success"; 
header("location: ../pending/login.php");
}


else {
    $_SESSION['alert'] = "Error!";
    $_SESSION['status'] = "Wrong Verification Number";
    $_SESSION['status-code'] = "error"; 
    header("location: ../pending/pending_verify_code.php?id=$userid");
}




}

?>