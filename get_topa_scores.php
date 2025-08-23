<?php
include 'db_config.php';

$data = [];

// ดึงคะแนน top 3 ของแต่ละเกม
$sql = "SELECT gs.user_id, u.username, gs.game_name, gs.game_title, gs.score, gs.play_time_str, gs.created_at
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.id
        ORDER BY gs.game_name, gs.score DESC, gs.created_at ASC";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $gameName = $row['game_name'];   // ชื่อเกม
    if (!isset($data[$gameName])) {
        $data[$gameName] = [];
    }

    // เก็บเฉพาะ top 3
    if (count($data[$gameName]) < 3) {
        $data[$gameName][] = [
            "username" => $row['username'],
            "category" => $row['game_title'],
            "score" => (int)$row['score'],
            "time" => $row['play_time_str']
        ];
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
