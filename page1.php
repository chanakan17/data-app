<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Vocabulary App</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style/homena.css">
    
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
        <li><a href="page1.php" class="active">หน้าหลัก</a></li>
        <li><a href="show_users.php">ผู้ใช้</a></li>
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

        <div class="content">
            <div class="AppEng">
                <div class="tnx">
                    <h2><i class="fas fa-cubes"></i></h2>
                </div>
                <div class="txt">
                    <h1>Unique</h1>
                    <h1>Monkey</h1>
                </div>
                <p>
                    แอปพลิเคชันนี้ถูกออกแบบมาเพื่อเป็นสื่อการเรียนรู้คำศัพท์ภาษาอังกฤษ
                    โดยมีโหมดการเล่นที่หลากหลาย เช่น การทายคำศัพท์ การจับคู่คำศัพท์
                    และการเติมคำศัพท์ ทั้งยังแบ่งหมวดหมู่คำศัพท์เป็นยานพาหนะ สัตว์ ของใช้ในบ้าน
                    และกีฬา เพื่อช่วยให้ผู้ใช้เรียนรู้ได้อย่างสนุกสนานและมีประสิทธิภาพมากยิ่งขึ้น
                </p>
            </div>
            <div class="card">
                <a href="https://drive.google.com/drive/folders/1oVc8SJivjdQ0tWUs53MOiLiaudeWCGYb?usp=sharing" target="_blank">
                    <div class="box2">
                        <h2>ดาวน์โหลดแอพ</h2>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>