<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "flutter_app_db";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
