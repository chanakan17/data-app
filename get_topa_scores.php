<?php
include 'db_config.php';
header('Content-Type: application/json; charset=utf-8');

$sql = "
SELECT *
FROM (
    SELECT 
        gs.id, 
        gs.game_title, 
        gs.score, 
        u.username,
        ROW_NUMBER() OVER (PARTITION BY gs.game_title ORDER BY gs.score DESC) AS rank_pos
    FROM game_scores gs
    JOIN users u ON gs.user_id = u.id
) ranked
WHERE rank_pos <= 3
ORDER BY game_title, rank_pos
";

$result = $conn->query($sql);

$scores = [];
while ($row = $result->fetch_assoc()) {
    $game = $row['game_title'];
    if (!isset($scores[$game])) {
        $scores[$game] = [];
    }
    $scores[$game][] = [
        "username" => $row["username"],
        "score" => (int)$row["score"],
        "rank" => (int)$row["rank_pos"]
    ];
}

echo json_encode($scores, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
