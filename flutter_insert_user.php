<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'db_config.php';

$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($email) || empty($username) || empty($birthday) || empty($password) || empty($confirm_password)) {
  echo json_encode(["success" => false, "message" => "❌ กรุณากรอกข้อมูลให้ครบทุกช่อง"]);
  exit;
}

if ($password !== $confirm_password) {
  echo json_encode(["success" => false, "message" => "❌ รหัสผ่านไม่ตรงกัน"]);
  exit;
}

// 🔎 ตรวจสอบว่า email ซ้ำหรือไม่
$check_email_sql = "SELECT id FROM users WHERE email = ?";
$check_email_stmt = $conn->prepare($check_email_sql);
$check_email_stmt->bind_param("s", $email);
$check_email_stmt->execute();
$check_email_stmt->store_result();

if ($check_email_stmt->num_rows > 0) {
  echo json_encode(["success" => false, "message" => "❌ อีเมลนี้ถูกใช้งานแล้ว"]);
  $check_email_stmt->close();
  $conn->close();
  exit;
}
$check_email_stmt->close();

// 🔎 ตรวจสอบว่า username ซ้ำหรือไม่
$check_username_sql = "SELECT id FROM users WHERE username = ?";
$check_username_stmt = $conn->prepare($check_username_sql);
$check_username_stmt->bind_param("s", $username);
$check_username_stmt->execute();
$check_username_stmt->store_result();

if ($check_username_stmt->num_rows > 0) {
  echo json_encode(["success" => false, "message" => "❌ ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว"]);
  $check_username_stmt->close();
  $conn->close();
  exit;
}
$check_username_stmt->close();

// ✅ บันทึกข้อมูล
$plain_password = $password;

$sql = "INSERT INTO users (email, username, birthday, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $email, $username, $birthday, $plain_password);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "✅ เพิ่มผู้ใช้เรียบร้อยแล้ว"]);
} else {
  echo json_encode(["success" => false, "message" => "❌ เกิดข้อผิดพลาด: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
