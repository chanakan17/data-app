<?php
header('Content-Type: application/json');
include 'db_config.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$newName = isset($_POST['username']) ? trim($_POST['username']) : "";

if ($id > 0 && !empty($newName)) {

    // ตรวจสอบว่าชื่อผู้ใช้ซ้ำหรือไม่ (ยกเว้นตัวเอง)
    $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $newName, $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // ชื่อซ้ำ
        echo json_encode([
            "success" => false,
            "error" => "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว"
        ]);
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();

    // อัปเดตชื่อผู้ใช้
    $stmt = $conn->prepare("UPDATE users SET username=? WHERE id=?");
    $stmt->bind_param("si", $newName, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
}

$conn->close();
?>
