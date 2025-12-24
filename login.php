<?php
session_start();
include 'db_config.php'; // ตรวจสอบว่าไฟล์นี้มีอยู่จริงและเชื่อมต่อ DB ได้

$error = ""; // ประกาศตัวแปรกัน Error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM andmins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // ถ้าใช้ md5 (ไม่แนะนำแต่ถ้าฐานข้อมูลเป็นแบบนี้ก็ใช้ได้)
        if ($row["password"] == md5($password)) { 
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            
            // ล็อกอินผ่านแล้ว ให้กระโดดไปหน้าหลัก
            header("Location: page.php"); 
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        $error = "ไม่พบผู้ใช้นี้!";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url("https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(3px);
        }

        .bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .login-box {
            position: relative;
            width: 400px;
            padding: 40px;
            background: rgba(0, 0, 0, 0.75);
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.6);
            color: #fff;
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 30px;
            font-size: 2rem;
            color: #ff5722;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .input-group {
            position: relative;
            margin-bottom: 30px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #ccc;
        }

        .input-group input {
            width: 100%;
            padding: 10px 0;
            font-size: 1rem;
            color: #fff;
            background: transparent;
            border: none;
            border-bottom: 2px solid #555;
            outline: none;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-bottom: 2px solid #ff5722;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border: none;
            background: #ff5722;
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(255, 87, 34, 0.3);
        }

        .btn-login:hover {
            background: #e64a19;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 87, 34, 0.5);
        }

        .error-msg {
            color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 0.9rem;
            display: block;
        }

        .input-icon {
            position: absolute;
            right: 0;
            top: 35px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="bg"></div>
    <div class="login-box">
        <h2>Admin Login</h2>
        
        <form method="post" action="">
            <div class="input-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" name="username" required autocomplete="off">
            </div>
            <div class="input-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
        </form>

        <?php if (!empty($error)): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
