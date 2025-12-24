<?php
$host = " localhost:3306";
$user = "chontun_data_app";
$pass = "HXz2dWJQGSkQKeM";
$db = "data_app";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
