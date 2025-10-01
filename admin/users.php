<?php
session_start();
include '../config/connectdb.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการผู้ใช้</title>
  <?php include 'style.php'; ?> <!-- ใส่ style ส่วนกลาง -->
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h4>📚 Admin Panel</h4>
      <a href="index.php">🏠 Dashboard</a>
      <a href="users.php" class="active">👥 จัดการผู้ใช้</a>
      <a href="products.php">📦 จัดการสินค้า</a>
      <a href="orders.php">🛒 คำสั่งซื้อ</a>
      <a href="reports.php">📊 รายงาน</a>
      <hr>
      <a href="adminout.php" class="text-danger">🚪 ออกจากระบบ</a>
    </div>

    <!-- Content -->
    <div class="col-md-9 col-lg-10 content">
      <h1 class="fw-bold mb-4">👥 จัดการผู้ใช้</h1>
      <div class="card p-3">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>ชื่อผู้ใช้</th>
              <th>อีเมล</th>
              <th>สิทธิ์</th>
              <th>การจัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>admin</td>
              <td>admin@example.com</td>
              <td><span class="badge bg-danger">Admin</span></td>
              <td>
                <button class="btn btn-sm btn-outline-warning">แก้ไข</button>
                <button class="btn btn-sm btn-outline-danger">ลบ</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>somchai</td>
              <td>somchai@example.com</td>
              <td><span class="badge bg-primary">User</span></td>
              <td>
                <button class="btn btn-sm btn-outline-warning">แก้ไข</button>
                <button class="btn btn-sm btn-outline-danger">ลบ</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
