<?php
header('Content-Type: application/json; charset=utf-8');

include 'db_config.php';

$username  = $_POST['username']  ?? '';
$email     = $_POST['email']     ?? '';
$birthdate = $_POST['birthdate'] ?? '';

$response = ['success' => false];

if (empty($username) || empty($email) || empty($birthdate)) {
    $response['message'] = 'ข้อมูลไม่ครบถ้วน';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "SELECT id FROM users WHERE username = ? AND email = ? AND birthday = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'SQL Error: ' . $conn->error;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("sss", $username, $email, $birthdate);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $response['success'] = true;
    $response['message'] = 'ข้อมูลถูกต้อง';
} else {
    $response['message'] = 'ไม่พบข้อมูลผู้ใช้งาน';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>
