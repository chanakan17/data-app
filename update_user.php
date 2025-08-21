<?php
header('Content-Type: application/json');
include 'db_config.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$newName = isset($_POST['username']) ? $_POST['username'] : "";

if ($id > 0 && !empty($newName)) {
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
