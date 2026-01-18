<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); 
    exit;
}
include 'db_config.php';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลหมวดหมู่
$sql_categories = "SELECT id, name FROM categories ORDER BY id";
$result_categories = $conn->query($sql_categories);

$selected_category = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Logic เพิ่มคำศัพท์
if (isset($_POST['add_word'])) {
    $word   = $conn->real_escape_string($_POST['word']);
    $meaning = $conn->real_escape_string($_POST['meaning']);
    $category_id = intval($_POST['category_id']);

    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $target_file;
        }
    }

    $sql = "INSERT INTO dictionary (word, meaning, image, category_id) 
            VALUES ('$word','$meaning','$image',$category_id)";
    $conn->query($sql);
    header("Location: word.php?category_id=$category_id");
    exit;
}

// Logic ลบคำศัพท์
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $sql = "SELECT image FROM dictionary WHERE id=$delete_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image'];

        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath); 
        }

        $conn->query("DELETE FROM dictionary WHERE id=$delete_id");
    }

    header("Location: word.php?category_id=$selected_category");
    exit;
}

// ดึงคำศัพท์ตามหมวดหมู่
$words = [];
if ($selected_category > 0) {
    $sql_words = "
        SELECT d.id, d.word, d.meaning, d.image, c.name AS category_name
        FROM dictionary d
        JOIN categories c ON d.category_id = c.id
        WHERE d.category_id = $selected_category
        ORDER BY d.id DESC
    ";
    $result_words = $conn->query($sql_words);
    while ($row = $result_words->fetch_assoc()) {
        $words[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หมวดหมู่และคำศัพท์</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style/homena.css">
    
    <style>
        /* Custom Styles สำหรับหน้า Dictionary ให้เข้ากับ homena.css */
        body {
            font-family: 'Kanit', sans-serif;
            /* background จัดการโดย homena.css (.bg) */
        }
        
        .content-area {
            padding: 20px;
            color: #fff;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 { color: #fd7e13; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        h2 { color: #ffae5d; margin-top: 30px; margin-bottom: 15px; }

        /* Buttons Styling */
        .btn-cat {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 50px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            text-decoration: none;
            transition: 0.3s;
            border: 1px solid #fd7e13;
        }
        .btn-cat:hover, .btn-cat.active {
            background: #fd7e13;
            color: #fff;
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(253, 126, 19, 0.5);
        }

        .add-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Kanit', sans-serif;
            font-size: 1rem;
            transition: 0.3s;
        }
        .add-btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        /* Form Styling */
        #form-add {
            background: rgba(0, 0, 0, 0.8);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid #444;
            max-width: 500px;
        }
        input[type="text"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border-radius: 5px;
            border: none;
            background: #333;
            color: #fff;
            font-family: 'Kanit', sans-serif;
            box-sizing: border-box; /* Fix padding width issue */
        }

        /* Card Grid Styling */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center; /* จัดกึ่งกลาง */
        }

        .vocab-card {
            width: 200px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .vocab-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .vocab-card strong {
            display: block;
            font-size: 1.4rem;
            color: #fd7e13;
            margin-bottom: 5px;
        }

        .vocab-card span {
            font-size: 1rem;
            color: #ddd;
        }

        .vocab-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 10px;
            border: 2px solid #fd7e13;
        }

        .delete-btn {
            display: inline-block;
            margin-top: 15px;
            color: #ff4444;
            text-decoration: none;
            font-size: 0.9rem;
            border: 1px solid #ff4444;
            padding: 5px 15px;
            border-radius: 20px;
            transition: 0.3s;
        }
        .delete-btn:hover {
            background: #ff4444;
            color: white;
        }

        hr {
            border: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #fd7e13, transparent);
            margin: 30px 0;
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
    <li><a href="page1.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'page1.php' ? 'active' : ''; ?>">หน้าหลัก</a></li>

    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        
        <li><a href="show_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'show_users.php' ? 'active' : ''; ?>">ผู้ใช้</a></li>
        <li><a href="score.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'score.php' ? 'active' : ''; ?>">คะแนนผู้ใช้</a></li>
        <li><a href="word.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'word.php' ? 'active' : ''; ?>">หมวดหมู่และคำศัพท์</a></li>

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
            <div class="content-area">
                <h1><i class="fas fa-book"></i> คลังคำศัพท์ภาษาอังกฤษ</h1>

                <button class="add-btn" onclick="document.getElementById('form-add').style.display='block'">
                    <i class="fas fa-plus-circle"></i> เพิ่มคำศัพท์ใหม่
                </button>

                <div id="form-add" style="display:none;">
                    <h3 style="color:#fd7e13; margin-top:0;">เพิ่มคำศัพท์</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <label>คำศัพท์ (English):</label>
                        <input type="text" name="word" required placeholder="Ex. Monkey">
                        
                        <label>คำแปล (Thai):</label>
                        <input type="text" name="meaning" required placeholder="Ex. ลิง">
                        
                        <label>หมวดหมู่:</label>
                        <select name="category_id" required>
                            <?php
                            // Reset pointer just in case
                            $result_categories->data_seek(0);
                            while ($cat = $result_categories->fetch_assoc()) {
                                echo "<option value='{$cat['id']}' " . ($cat['id']==$selected_category?"selected":"") . ">{$cat['name']}</option>";
                            }
                            ?>
                        </select>
                        
                        <label>อัปโหลดรูปภาพ:</label>
                        <input type="file" name="image">
                        
                        <br><br>
                        <button type="submit" name="add_word" class="add-btn" style="width:100%;">บันทึกข้อมูล</button>
                        <button type="button" onclick="document.getElementById('form-add').style.display='none'" style="background:#555; width:100%; margin-top:10px;" class="add-btn">ยกเลิก</button>
                    </form>
                </div>

                <hr>

                <div style="text-align: center;">
                    <h3 style="color: #fff; margin-bottom: 15px;">เลือกหมวดหมู่</h3>
                    <?php 
                    $result_categories->data_seek(0); // Reset pointer
                    if ($result_categories->num_rows > 0): ?>
                        <?php while ($cat = $result_categories->fetch_assoc()): ?>
                            <a class="btn-cat <?= ($cat['id'] == $selected_category) ? 'active' : '' ?>" href="?category_id=<?= $cat['id'] ?>">
                                <i class="fas fa-folder"></i> <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

                <br>

                <div>
                    <?php if ($selected_category > 0): ?>
                        <h2><i class="fas fa-folder-open"></i> หมวดหมู่: <?= htmlspecialchars($words[0]['category_name'] ?? '') ?></h2>
                        
                        <?php if (!empty($words)): ?>
                            <div class="card-container">
                                <?php foreach ($words as $w): ?>
                                    <div class="vocab-card">
                                        <strong><?= htmlspecialchars($w['word']) ?></strong>
                                        <span><?= htmlspecialchars($w['meaning']) ?></span>
                                        <?php if (!empty($w['image'])): ?>
                                            <img src="<?= htmlspecialchars($w['image']) ?>" alt="<?= htmlspecialchars($w['word']) ?>">
                                        <?php else: ?>
                                            <div style="height:150px; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.2); margin-top:10px; border-radius:10px; color:#555;">No Image</div>
                                        <?php endif; ?>
                                        
                                        <a class="delete-btn" href="?delete_id=<?= $w['id'] ?>&category_id=<?= $selected_category ?>"
                                           onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบคำศัพท์นี้?')">
                                           <i class="fas fa-trash"></i> ลบ
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align:center; padding: 40px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                                <h3 style="color: #aaa;">❌ ไม่มีคำศัพท์ในหมวดนี้</h3>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div style="text-align:center; padding: 50px;">
                            <i class="fas fa-hand-point-up" style="font-size: 3rem; color: #fd7e13;"></i>
                            <h3>กรุณาเลือกหมวดหมู่ด้านบนเพื่อดูคำศัพท์</h3>
                        </div>
                    <?php endif; ?>
                </div>

            </div> </div> </div> </body>
</html>