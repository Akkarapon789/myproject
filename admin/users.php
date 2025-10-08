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
  <title>ผู้ใช้</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="d-flex">
  <!-- 🧭 Sidebar -->
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php">แดชบอร์ด</a>
    <a href="users.php" class="active">ผู้ใช้</a>
    <a href="products.php">สินค้า</a>
    <a href="orders.php">คำสั่งซื้อ</a>
    <a href="reports.php">รายงาน</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <!-- 📄 Content -->
  <div class="content flex-grow-1">
    <h2>👥 จัดการผู้ใช้</h2>
    <a href="add_user.php" class="btn btn-success mb-3">+ เพิ่มผู้ใช้</a>

    <div class="card p-3 shadow-sm">
      <table id="userTable" class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>ชื่อ-นามสกุล</th>
            <th>Email</th>
            <th>เบอร์โทร</th>
            <th>สิทธิ์</th>
            <th>การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT * FROM user ORDER BY user_id DESC");
          while ($row = $result->fetch_assoc()):
          ?>
          <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['phone']); ?></td>
            <td>
              <span class="badge <?= $row['role']=='admin'?'bg-danger':'bg-primary' ?>">
                <?= ucfirst($row['role']); ?>
              </span>
            </td>
            <td>
              <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
              <a href="delete_user.php?id=<?= $row['user_id'] ?>" 
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('ยืนยันการลบผู้ใช้นี้หรือไม่?');">ลบ</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- 📊 DataTable Init -->
<script>
$(document).ready(function() {
  $('#userTable').DataTable({
    language: {
      search: "🔍 ค้นหา:",
      lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
      info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
      infoEmpty: "ไม่มีข้อมูล",
      zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
      paginate: {
        first: "หน้าแรก",
        last: "หน้าสุดท้าย",
        next: "ถัดไป",
        previous: "ก่อนหน้า"
      }
    },
    pageLength: 10,
    order: [[0, "desc"]],
    responsive: true
  });
});
</script>
</body>
</html>
