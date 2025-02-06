<?php
session_start();
$host = 'localhost';
$db = 'jobs';
$user = 'akhil';
$pass = 'password';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";
?>
