<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
</head>
<body>

    <ul>
        <li><a href="show_users.php">ดูรายชื่อผู้ใช้ทั้งหมด</a></li>
        <li><a href="score.php">ดูคะแนนผู้ใช้ทั้งหมด</a></li>
        <li><a href="logout.php">ออกจากระบบ</a></li>
    </ul>
</body>
</html>
