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
  <title>คำสั่งซื้อ</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php">แดชบอร์ด</a>
    <a href="users.php">ผู้ใช้</a>
    <a href="products.php">สินค้า</a>
    <a href="orders.php" class="active">คำสั่งซื้อ</a>
    <a href="reports.php">รายงาน</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <div class="content flex-grow-1">
    <h2>🛒 จัดการคำสั่งซื้อ</h2>
    <div class="card p-3">
      <table class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>ชื่อลูกค้า</th>
            <th>Email</th>
            <th>ยอดรวม</th>
            <th>วันที่</th>
            <th>การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");
          while($o=$orders->fetch_assoc()):
          ?>
          <tr>
            <td><?= $o['id']; ?></td>
            <td><?= htmlspecialchars($o['fullname']); ?></td>
            <td><?= htmlspecialchars($o['email']); ?></td>
            <td><?= number_format($o['total'],2); ?> บาท</td>
            <td><?= $o['created_at']; ?></td>
            <td>
              <a href="edit_order.php?id=<?= $o['id']; ?>" class="btn btn-sm btn-warning">ดู / แก้ไข</a>
              <a href="delete_order.php?id=<?= $o['id']; ?>" class="btn btn-sm btn-danger"
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
