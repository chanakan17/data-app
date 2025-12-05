<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include 'db_config.php';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die(json_encode(["error" => "❌ การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error]));
}

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$sql = "
SELECT 
    id,
    word,
    meaning,
    image,
    category_id
FROM dictionary
WHERE category_id = $category_id
ORDER BY word ASC
";

$result = $conn->query($sql);

$words = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['image'])) {
           $row['image_url'] = "http://10.161.225.68/dataweb/" . $row['image'];
        } else {
            $row['image_url'] = "";
        }
        $words[] = $row;
    }
} else {
    echo json_encode(["message" => "ไม่มีข้อมูลคำศัพท์"]);
    exit;
}

$conn->close();

echo json_encode($words, JSON_UNESCAPED_UNICODE);
?>
