<?php
include 'db_config.php';

$user_id = $_GET['user_id'] ?? 0;

// ดึง Top 3 คะแนนสูงสุด พร้อม username จากตาราง users
$sql = "
SELECT gs.id, gs.game_title, gs.score, gs.created_at, u.username
FROM game_scores gs
JOIN users u ON gs.user_id = u.id
WHERE gs.user_id = ?
ORDER BY gs.score DESC
LIMIT 3
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$scores = [];
while ($row = $result->fetch_assoc()) {
    $scores[] = $row;
}

echo json_encode($scores);
?>
