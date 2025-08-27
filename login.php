<?php
session_start();
include 'db_config.php';

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
        if ($row["password"] == md5($password)) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            header("Location: home.php");
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        $error = "ไม่พบผู้ใช้นี้!";
    }
}
?>
<form method="post">
  <input type="text" name="username" placeholder="Username"><br>
  <input type="password" name="password" placeholder="Password"><br>
  <button type="submit">Login</button>
</form>
<?php if (!empty($error)) echo $error; ?>
