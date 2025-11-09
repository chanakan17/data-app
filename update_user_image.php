<?php
header('Content-Type: application/json');
include 'db.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB Connection Failed: ' . $conn->connect_error]);
    exit;
}

// ตรวจสอบว่าได้รับ user_id และไฟล์ image
if (!isset($_POST['user_id']) || !isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$user_id = intval($_POST['user_id']);
$image = $_FILES['image'];

// ตรวจสอบว่า user_id มีอยู่จริงในตาราง users
$stmtUser = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$stmtUser->store_result();

if ($stmtUser->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}
$stmtUser->close();

// สร้างโฟลเดอร์ uploads/user/ ถ้ายังไม่มี
$uploadDir = 'uploads/user/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ตรวจสอบว่าโฟลเดอร์เขียนได้
if (!is_writable($uploadDir)) {
    echo json_encode(['success' => false, 'error' => 'Uploads folder not writable']);
    exit;
}

// ตรวจสอบชนิดไฟล์
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
if (!in_array($image['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
    exit;
}

// สร้างชื่อไฟล์ใหม่ไม่ซ้ำ
$ext = pathinfo($image['name'], PATHINFO_EXTENSION);
$newFileName = 'user_' . $user_id . '_' . time() . '.' . $ext;
$targetPath = $uploadDir . $newFileName;

// ย้ายไฟล์ไปที่โฟลเดอร์ uploads/user/
if (!move_uploaded_file($image['tmp_name'], $targetPath)) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to move uploaded file',
        'tmp_name' => $image['tmp_name'],
        'target' => $targetPath
    ]);
    exit;
}

// ตรวจสอบว่ามี record ของ user นี้ในตาราง user_profiles หรือไม่
$stmtCheck = $conn->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
$stmtCheck->bind_param("i", $user_id);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    // ถ้ามีแล้ว update
    $stmtCheck->close();
    $stmtUpdate = $conn->prepare("UPDATE user_profiles SET avatar = ? WHERE user_id = ?");
    $stmtUpdate->bind_param("si", $targetPath, $user_id);
    if ($stmtUpdate->execute()) {
        echo json_encode(['success' => true, 'path' => $targetPath, 'message' => 'Profile updated']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update DB']);
    }
    $stmtUpdate->close();
} else {
    // ถ้าไม่มี record insert ใหม่
    $stmtCheck->close();
    $stmtInsert = $conn->prepare("INSERT INTO user_profiles (user_id, avatar) VALUES (?, ?)");
    $stmtInsert->bind_param("is", $user_id, $targetPath);
    if ($stmtInsert->execute()) {
        echo json_encode(['success' => true, 'path' => $targetPath, 'message' => 'Profile created']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to insert DB']);
    }
    $stmtInsert->close();
}

$conn->close();
?>
