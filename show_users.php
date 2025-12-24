<?php
session_start();
include 'db_config.php';
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้ - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="style/homena.css">
    
    <style>
        body { font-family: 'Kanit', sans-serif; }

        .content-wrapper {
            display: flex;
            justify-content: center;
            padding-top: 80px;
            padding-bottom: 50px;
        }

        .user-card {
            background: #ffffff;
            width: 95%;
            max-width: 1200px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 30px;
            position: relative;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.8rem;
        }

        .btn-add {
            background: linear-gradient(45deg, #11998e, #38ef7d);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(56, 239, 125, 0.4);
            transition: transform 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add:hover {
            transform: translateY(-2px);
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .custom-table thead {
            background-color: #2c3e50;
            color: white;
        }

        .custom-table th, .custom-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .custom-table th {
            font-weight: 500;
        }

        .custom-table tr:hover {
            background-color: #f8f9fa;
        }

        .btn-delete {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Kanit', sans-serif;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-delete:hover {
            background-color: #cc0000;
        }

        .navbar { z-index: 1000; }
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
                <li><a href="show_users.php" class="active">ผู้ใช้</a></li>
                <li><a href="score.php">คะแนนผู้ใช้</a></li>
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

        <div class="content-wrapper">
            <div class="user-card">
                <div class="card-header">
                    <h2><i class="fas fa-users-cog"></i> จัดการผู้ใช้งาน</h2>
                    <a href="add_user.html" class="btn-add">
                        <i class="fas fa-plus-circle"></i> เพิ่มผู้ใช้ใหม่
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ชื่อผู้ใช้งาน</th>
                                <th>Email</th>
                                <th>วันเกิด</th>
                                <th>รหัสผ่าน</th>
                                <th>วันที่สร้าง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($row['id']) ?></td>
                                <td style="font-weight:bold; color:#2c3e50;">
                                    <i class="fas fa-user-circle" style="color:#aaa; margin-right:5px;"></i>
                                    <?= htmlspecialchars($row['username']) ?>
                                </td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['birthday']) ?></td>
                                <td style="font-family: monospace; color: #666;">
                                    <?= htmlspecialchars($row['password']) ?>
                                </td>
                                <td style="font-size: 0.9em; color:#888;">
                                    <?= date("d/m/Y H:i", strtotime($row['created_at'])) ?>
                                </td>
                                <td>
                                    <form method="post" action="delete_user.php" onsubmit="return confirm('ยืนยันการลบผู้ใช้ <?= htmlspecialchars($row['username']) ?> ?');">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>