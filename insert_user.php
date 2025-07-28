<?php
include 'db_config.php';

// รับค่าจากฟอร์ม
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// ตรวจสอบว่ากรอกครบ
if (empty($email) || empty($username) || empty($birthday) || empty($password) || empty($confirm_password)) {
  die("❌ กรุณากรอกข้อมูลให้ครบทุกช่อง");
}

// ตรวจสอบว่ารหัสผ่านตรงกัน
if ($password !== $confirm_password) {
  die("❌ รหัสผ่านไม่ตรงกัน");
}

// เข้ารหัสรหัสผ่านด้วย MD5 (ไม่ปลอดภัยมากนัก แต่ตามคำขอ)
$hashed_password = md5($password);

// เตรียมคำสั่ง SQL
$sql = "INSERT INTO users (email, username, birthday, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $email, $username, $birthday, $hashed_password);

// ดำเนินการเพิ่มผู้ใช้
if ($stmt->execute()) {
  echo "✅ เพิ่มผู้ใช้เรียบร้อยแล้ว<br>";
  echo '<a href="add_user.html">เพิ่มผู้ใช้อีก</a> | <a href="show_users.php">ดูรายชื่อทั้งหมด</a>';
} else {
  echo "❌ เกิดข้อผิดพลาด: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
