<?php
require "db_config.php";

if (!$conn) {
    echo json_encode("Connection error");
    exit;
}

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    echo json_encode("Missing email or password");
    exit;
}

// ไม่เข้ารหัสรหัสผ่าน
$plain_password = $password;

$sql = "SELECT * FROM users WHERE email = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $plain_password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    echo json_encode("Success");
} else {
    echo json_encode("Error");
}
?>
