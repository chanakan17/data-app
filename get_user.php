<?php
header('Content-Type: application/json');
include 'db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing ID']);
    exit;
}

$user_id = intval($_GET['id']);

// ใช้ LEFT JOIN เพื่อดึงข้อมูลจาก user_profiles มาด้วย
$sql = "SELECT 
            u.username, 
            u.email, 
            p.selected_image 
        FROM users u 
        LEFT JOIN user_profiles p ON u.id = p.user_id 
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // ถ้า selected_image เป็น NULL (ยังไม่เคยตั้งค่า) ให้ส่งกลับเป็น 0 (Default)
    $image_id = $row['selected_image'] !== null ? $row['selected_image'] : 0;
    
    echo json_encode([
        "username" => $row['username'],
        "email" => $row['email'],
        "image_id" => $image_id // ส่งค่าตัวเลขกลับไปให้ Flutter
    ]);
} else {
    echo json_encode(["error" => "User not found"]);
}

$conn->close();
?>