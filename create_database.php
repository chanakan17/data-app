<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "flutter_app_db";

$conn = new mysqli($host, $user, $pass);
$conn->set_charset("utf8");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// สร้างฐานข้อมูลถ้ายังไม่มี
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql_create_db) === TRUE) {
  echo "✅ สร้างฐานข้อมูลเรียบร้อย<br>";
} else {
  echo "❌ สร้างฐานข้อมูลไม่สำเร็จ: " . $conn->error . "<br>";
}

$conn->select_db($db);

// สร้างตาราง users ตามฟอร์ม
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  username VARCHAR(20) NOT NULL,
  birthday DATE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql_create_table) === TRUE) {
  echo "✅ สร้างตาราง users เรียบร้อย<br>";
} else {
  echo "❌ สร้างตาราง users ไม่สำเร็จ: " . $conn->error . "<br>";
}

$sql_create_scores = "
CREATE TABLE IF NOT EXISTS game_scores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  game_name VARCHAR(50) NOT NULL, -- เพิ่มตรงนี้
  game_title VARCHAR(50) NOT NULL,
  score INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_create_scores) === TRUE) {
  echo "✅ สร้างตาราง game_scores เรียบร้อย<br>";
} else {
  echo "❌ สร้างตาราง game_scores ไม่สำเร็จ: " . $conn->error . "<br>";
}

$conn->close();
?>
