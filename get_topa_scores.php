<?php
include 'db_config.php';

$data = [];

// SQL: ดึงทั้งหมด (ยังคง JOIN รูปภาพเหมือนเดิม)
$sql = "SELECT 
            gs.user_id, 
            u.username, 
            gs.game_name, 
            gs.game_title, 
            gs.score, 
            gs.play_time_str, 
            gs.created_at,
            COALESCE(up.selected_image, 0) as image_id 
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.id
        LEFT JOIN user_profiles up ON u.id = up.user_id 
        ORDER BY gs.game_name, gs.game_title, gs.score DESC, 
                 STR_TO_DATE(gs.play_time_str, '%i:%s:%f') ASC,
                 gs.created_at ASC";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $gameName = $row['game_name'];
    $category = $row['game_title'];

    if (!isset($data[$gameName])) {
        $data[$gameName] = [];
    }

    if (!isset($data[$gameName][$category])) {
        $data[$gameName][$category] = [];
    }

    // ❌ ลบเงื่อนไข if (count(...) < 3) ออกไปเลยครับ
    // เพื่อให้มันเก็บข้อมูลต่อเรื่อยๆ ไม่จำกัด
    $data[$gameName][$category][] = [
        "username" => $row['username'],
        "score" => (int)$row['score'],
        "time" => $row['play_time_str'],
        "image_id" => (int)$row['image_id']
    ];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>