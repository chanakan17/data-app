<?php
include 'db_config.php';

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>รายชื่อผู้ใช้</title>
</head>
<body>
  <h2>รายชื่อผู้ใช้</h2>
  <a href="add_user.html">เพิ่มผู้ใช้ใหม่</a><br /><br />
  <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <tr>
      <th>ID</th>
      <th>ชื่อผู้ใช้งาน</th>
      <th>Email</th>
      <th>วันเกิด</th>
      <th>รหัสผ่าน</th>
      <th>วันที่สร้าง</th>
      <th>ลบ</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td><?= htmlspecialchars($row['username']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['birthday']) ?></td>
      <td><?= htmlspecialchars($row['password']) ?></td>
      <td><?= $row['created_at'] ?></td>
      <td>
        <form method="post" action="delete_user.php" onsubmit="return confirm('แน่ใจว่าต้องการลบผู้ใช้นี้?');">
          <input type="hidden" name="id" value="<?= $row['id'] ?>" />
          <input type="submit" value="ลบ" />
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

<?php $conn->close(); ?>
