<?php
include 'db_config.php';

$data = [];

$sql = "SELECT gs.user_id, u.username, gs.game_name, gs.game_title, gs.score, gs.play_time_str, gs.created_at
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.id
        ORDER BY gs.game_name, gs.game_title, gs.score DESC, 
                 STR_TO_DATE(gs.play_time_str, '%i:%s:%f') ASC,
                 gs.created_at ASC";

$result = $conn->query($sql);

$currentGame = '';
$currentCategory = '';
$count = 0;

while ($row = $result->fetch_assoc()) {
    $gameName = $row['game_name'];
    $category = $row['game_title'];

    if (!isset($data[$gameName])) {
        $data[$gameName] = [];
    }

    if (!isset($data[$gameName][$category])) {
        $data[$gameName][$category] = [];
        $count = 0;
    }
    if (count($data[$gameName][$category]) < 3) {
        $data[$gameName][$category][] = [
            "username" => $row['username'],
            "score" => (int)$row['score'],
            "time" => $row['play_time_str']
        ];
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
