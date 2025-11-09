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
  game_name VARCHAR(50) NOT NULL,
  game_title VARCHAR(50) NOT NULL,
  score INT NOT NULL,
  play_time_str VARCHAR(20) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_create_scores) === TRUE) {
  echo "✅ สร้างตาราง game_scores เรียบร้อย<br>";
} else {
  echo "❌ สร้างตาราง game_scores ไม่สำเร็จ: " . $conn->error . "<br>";
}

$sql_create_categories = "
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
";

if ($conn->query($sql_create_categories) === TRUE) {
  echo "✅ สร้างตาราง categories เรียบร้อย<br>";
} else {
  echo "❌ สร้างตาราง categories ไม่สำเร็จ: " . $conn->error . "<br>";
}

// ตาราง dictionary
$sql_create_dictionary = "
CREATE TABLE IF NOT EXISTS dictionary (
  id INT AUTO_INCREMENT PRIMARY KEY,
  word VARCHAR(100) NOT NULL,
  meaning VARCHAR(255) NOT NULL,
  image VARCHAR(255) DEFAULT '',
  category_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_create_dictionary) === TRUE) {
  echo "✅ สร้างตาราง dictionary เรียบร้อย<br>";
} else {
  echo "❌ สร้างตาราง dictionary ไม่สำเร็จ: " . $conn->error . "<br>";
}

$sql_create_user_profiles = "
CREATE TABLE IF NOT EXISTS user_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  avatar VARCHAR(255) DEFAULT '',
  selected_image INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_create_user_profiles) === TRUE) {
  echo "✅ สร้างตาราง user_profiles เรียบร้อย<br>";
} else {
  echo "❌ สร้างตาราง user_profiles ไม่สำเร็จ: " . $conn->error . "<br>";
}

$conn->close();
?>
