<?php
include 'db_config.php';

$id = $_POST['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
  die("ID ไม่ถูกต้อง");
}

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo "ลบข้อมูลเรียบร้อยแล้ว<br>";
  echo '<a href="show_users.php">กลับไปดูรายชื่อ</a>';
} else {
  echo "เกิดข้อผิดพลาด: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
