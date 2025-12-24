<?php
header('Content-Type: application/json');

$host = " localhost:3306";
$user = "chontun_data_app";
$pass = "HXz2dWJQGSkQKeM";
$db = "data_app";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB Connection Failed: ' . $conn->connect_error]);
    exit;
}
$user_id = 0;
$image_number = 0;

if (isset($_POST['user_id']) && isset($_POST['image_number'])) {
    $user_id = intval($_POST['user_id']);
    $image_number = intval($_POST['image_number']);
} else {
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    if (isset($data['user_id']) && isset($data['image_number'])) {
        $user_id = intval($data['user_id']);
        $image_number = intval($data['image_number']);
    }
}

if ($user_id == 0) { 
    echo json_encode(['success' => false, 'error' => 'Missing parameters or Invalid Data']);
    exit;
}

$stmtUser = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$stmtUser->store_result();

if ($stmtUser->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}
$stmtUser->close();

$stmtCheck = $conn->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
$stmtCheck->bind_param("i", $user_id);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    $stmtCheck->close();
    $stmtUpdate = $conn->prepare("UPDATE user_profiles SET selected_image = ? WHERE user_id = ?");
    $stmtUpdate->bind_param("ii", $image_number, $user_id);
    
    if ($stmtUpdate->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile image updated']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update DB']);
    }
    $stmtUpdate->close();

} else {
    // B. ถ้ายังไม่มี -> ให้ INSERT
    $stmtCheck->close();
    $stmtInsert = $conn->prepare("INSERT INTO user_profiles (user_id, selected_image) VALUES (?, ?)");
    $stmtInsert->bind_param("ii", $user_id, $image_number);
    
    if ($stmtInsert->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile created']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to insert DB']);
    }
    $stmtInsert->close();
}

$conn->close();
?>