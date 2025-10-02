<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการผู้ใช้</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php">แดชบอร์ด</a>
    <a href="users.php" class="active">จัดการผู้ใช้</a>
    <a href="products.php">จัดการสินค้า</a>
    <a href="orders.php">คำสั่งซื้อ</a>
    <a href="reports.php">รายงาน</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <div class="content flex-grow-1">
    <h2>👥 จัดการผู้ใช้</h2>
    <a href="add_user.php" class="btn btn-success mb-3">+ เพิ่มผู้ใช้</a>

    <div class="card p-3">
      <table class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>ชื่อ-นามสกุล</th>
            <th>Email</th>
            <th>เบอร์</th>
            <th>สิทธิ์</th>
            <th>การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
          while ($row = $result->fetch_assoc()):
          ?>
          <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['phone']); ?></td>
            <td><span class="badge <?= $row['role']=='admin'?'bg-danger':'bg-primary' ?>">
                <?= $row['role']; ?></span></td>
            <td>
              <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
              <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('ยืนยันการลบ?');">ลบ</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
