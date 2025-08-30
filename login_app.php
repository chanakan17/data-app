<?php
require "db_config.php";

header("Content-Type: application/json; charset=UTF-8");

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    echo json_encode(["status" => "Missing email or password"]);
    exit;
}

$plain_password = $password;

$sql = "SELECT * FROM users WHERE email = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $plain_password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "status" => "Success",
        "id" => $user['id'] // 👈 ส่ง id กลับไปให้ Flutter
    ]);
} else {
    echo json_encode(["status" => "Error"]);
}
?>
