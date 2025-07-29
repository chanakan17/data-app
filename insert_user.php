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

// ตรวจสอบว่าอีเมลซ้ำหรือไม่
$sql_check = "SELECT id FROM users WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
  // ถ้าเจออีเมลนี้แล้ว ให้แจ้งเตือนและหยุด
  die("❌ อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้เมลอื่น");
}
$stmt_check->close();

// เพิ่มข้อมูลผู้ใช้ใหม่
$sql = "INSERT INTO users (email, username, birthday, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $email, $username, $birthday, $password);

if ($stmt->execute()) {
  echo "✅ เพิ่มผู้ใช้เรียบร้อยแล้ว<br>";
  echo '<a href="add_user.html">เพิ่มผู้ใช้อีก</a> | <a href="show_users.php">ดูรายชื่อทั้งหมด</a>';
} else {
  echo "❌ เกิดข้อผิดพลาด: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
