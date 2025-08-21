<?php
include 'db_config.php';

$user_id = $_POST['user_id'];
$game_title = $_POST['game_title'];
$score = $_POST['score'];

$sql = "INSERT INTO game_scores (user_id, game_title, score) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $user_id, $game_title, $score);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
