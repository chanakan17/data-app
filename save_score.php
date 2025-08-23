<?php
include 'db_config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['user_id'], $_POST['game_name'], $_POST['game_title'], $_POST['score'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$user_id = $_POST['user_id'];
$game_name = $_POST['game_name'];
$game_title = $_POST['game_title'];
$score = $_POST['score'];

$sql = "INSERT INTO game_scores (user_id, game_name, game_title, score) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => $conn->error]);
    exit;
}

$stmt->bind_param("issi", $user_id, $game_name, $game_title, $score);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
