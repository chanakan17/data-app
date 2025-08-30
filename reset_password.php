<?php
include 'db_config.php';

$email = $_POST['email'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (!$email || !$new_password) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบ']);
    exit;
}

$sql = "UPDATE users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $new_password, $email);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'อัปเดตรหัสผ่านเรียบร้อย']);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถอัปเดตรหัสผ่านได้ หรือไม่พบอีเมลนี้ในระบบ'
    ]);
}

$stmt->close();
$conn->close();
