<?php 

$servername = "localhost";
$username = "root";
$password = "";
$database = "ojtwebportal";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$database;", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {

}
?>