<?php
header("Access-Control-Allow-Origin: *"); // ✅ ให้ Flutter หรือมือถือเข้าถึงได้
header("Content-Type: application/json; charset=UTF-8"); // ✅ ตอบกลับเป็น JSON

include 'db_config.php';

// รับค่าจาก Flutter หรือเว็บ (POST)
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// ตรวจสอบว่ากรอกครบ
if (empty($email) || empty($username) || empty($birthday) || empty($password) || empty($confirm_password)) {
  echo json_encode(["success" => false, "message" => "❌ กรุณากรอกข้อมูลให้ครบทุกช่อง"]);
  exit;
}

// ตรวจสอบว่ารหัสผ่านตรงกัน
if ($password !== $confirm_password) {
  echo json_encode(["success" => false, "message" => "❌ รหัสผ่านไม่ตรงกัน"]);
  exit;
}

// ตรวจสอบว่า email ซ้ำหรือไม่
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
  echo json_encode(["success" => false, "message" => "❌ อีเมลนี้ถูกใช้งานแล้ว"]);
  $check_stmt->close();
  $conn->close();
  exit;
}
$check_stmt->close();

// เข้ารหัสรหัสผ่าน (แนะนำเปลี่ยนจาก md5 เป็น password_hash เพื่อความปลอดภัย)
$hashed_password = md5($password);

// เพิ่มข้อมูลผู้ใช้
$sql = "INSERT INTO users (email, username, birthday, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $email, $username, $birthday, $hashed_password);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "✅ เพิ่มผู้ใช้เรียบร้อยแล้ว"]);
} else {
  echo json_encode(["success" => false, "message" => "❌ เกิดข้อผิดพลาด: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
