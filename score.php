<?php
session_start();
include 'db_config.php';

$sql = "SELECT gs.id, u.username, gs.game_name, gs.game_title, gs.score, gs.play_time_str, gs.created_at
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.id
        ORDER BY gs.score DESC, gs.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Board - English Vocabulary App</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="style/homena.css">
    
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }

        .score-content-wrapper {
            display: flex;
            justify-content: center;
            padding-top: 80px;
            padding-bottom: 50px;
        }

        .score-card {
            background: #ffffff;
            width: 90%;
            max-width: 1000px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .page-header span {
            display: block;
            font-size: 1rem;
            color: #7f8c8d;
            margin-top: 5px;
        }
        .table-responsive {
            overflow-x: auto;
            border-radius: 15px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .custom-table thead {
            background: linear-gradient(45deg, #11998e, #38ef7d);
            color: white;
        }

        .custom-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        .custom-table th:first-child {
            border-top-left-radius: 10px;
            text-align: center;
        }
        .custom-table th:last-child {
            border-top-right-radius: 10px;
        }

        .custom-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #555;
            vertical-align: middle;
        }

        .custom-table tr:hover {
            background-color: #f8fcf9;
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        .rank-circle {
            background: #eee;
            color: #555;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto;
        }

        .rank-1 .rank-circle { background: #FFD700; color: #fff; box-shadow: 0 0 10px #FFD700; }
        .rank-2 .rank-circle { background: #C0C0C0; color: #fff; }
        .rank-3 .rank-circle { background: #CD7F32; color: #fff; }

        .score-badge {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .date-text {
            font-size: 0.85rem;
            color: #999;
        }

        .navbar {
            z-index: 1000;
        }

    </style>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("nav-menu");
            if (menu.className === "show") {
                menu.className = "";
            } else {
                menu.className = "show";
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="bg"></div>
        
        <div class="navbar">
            <div class="menu-toggle" onclick="toggleMenu()">
                <i class="fas fa-bars"></i> เมนู
            </div>
            <ul id="nav-menu">
                <li><a href="page1.php">หน้าหลัก</a></li>
                <li><a href="show_users.php">ผู้ใช้</a></li>
                <li><a href="score.php" class="active">คะแนนผู้ใช้</a></li>
                <li><a href="word.php">หมวดหมู่และคำศัพท์</a></li>

                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li>
                        <a href="logout.php" style="color: #ff4444;">
                            <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="login.php" style="color: #00ff00;"> 
                            <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="content score-content-wrapper">
            <div class="score-card">
                <div class="page-header">
                    <h1><i class="fas fa-crown" style="color: gold;"></i> Leaderboard</h1>
                    <span>ตารางคะแนนยอดเยี่ยมประจำสัปดาห์</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th width="10%">ลำดับ</th>
                                <th><i class="fas fa-user"></i> ผู้เล่น</th>
                                <th><i class="fas fa-gamepad"></i> เกม</th>
                                <th><i class="fas fa-layer-group"></i> หมวดหมู่</th>
                                <th><i class="fas fa-star"></i> คะแนน</th>
                                <th><i class="fas fa-clock"></i> เวลาที่ใช้</th>
                                <th><i class="far fa-calendar-alt"></i> วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $rankClass = "rank-" . $rank;
                                    $date = date("d/m/Y H:i", strtotime($row['created_at']));
                                    
                                    echo "<tr class='{$rankClass}'>
                                            <td><div class='rank-circle'>{$rank}</div></td>
                                            <td style='font-weight: 600;'>{$row['username']}</td>
                                            <td>{$row['game_name']}</td>
                                            <td>{$row['game_title']}</td>
                                            <td><span class='score-badge'>{$row['score']}</span></td>
                                            <td>{$row['play_time_str']}</td>
                                            <td><span class='date-text'>{$date}</span></td>
                                          </tr>";
                                    $rank++;
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align:center; padding: 40px; color:#999;'>
                                        <i class='fas fa-ghost' style='font-size: 2em; margin-bottom:10px;'></i><br>
                                        ยังไม่มีข้อมูลคะแนนในขณะนี้
                                      </td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>