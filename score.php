<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

include 'db_config.php';

$sql = "SELECT gs.id, u.username, gs.game_name, gs.game_title, gs.score, gs.play_time_str, gs.created_at
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.id
        ORDER BY gs.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Game Scores</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #555;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        caption {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>🎮 Game Scores</h1>
    <table>
        <caption>คะแนนผู้เล่นทั้งหมด</caption>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Game Name</th>
            <th>Game Title</th>
            <th>Score</th>
            <th>Play Time</th>
            <th>Created At</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['game_name']}</td>
                        <td>{$row['game_title']}</td>
                        <td>{$row['score']}</td>
                        <td>{$row['play_time_str']}</td>
                        <td>{$row['created_at']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>ไม่มีข้อมูลคะแนน</td></tr>";
        }
        ?>
    </table>
    <br>
    <a href="home.php">🔙 กลับหน้า Home</a>
</body>
</html>
