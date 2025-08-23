<?php
include 'db_config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['user_id'], $_POST['game_name'], $_POST['game_title'], $_POST['score'], $_POST['play_time_str'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$user_id = $_POST['user_id'];
$game_name = $_POST['game_name'];
$game_title = $_POST['game_title'];
$score = $_POST['score'];
$play_time_str = $_POST['play_time_str'];

$sql = "INSERT INTO game_scores (user_id, game_name, game_title, score, play_time_str) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => $conn->error]);
    exit;
}

$stmt->bind_param("issis", $user_id, $game_name, $game_title, $score, $play_time_str);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
