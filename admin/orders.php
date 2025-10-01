<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>คำสั่งซื้อ</title>
  <?php include 'style.php'; ?>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h4>📚 Admin Panel</h4>
      <a href="index.php">🏠 Dashboard</a>
      <a href="users.php">👥 จัดการผู้ใช้</a>
      <a href="products.php">📦 จัดการสินค้า</a>
      <a href="orders.php" class="active">🛒 คำสั่งซื้อ</a>
      <a href="reports.php">📊 รายงาน</a>
      <hr>
      <a href="adminout.php" class="text-danger">🚪 ออกจากระบบ</a>
    </div>

    <!-- Content -->
    <div class="col-md-9 col-lg-10 content">
      <h1 class="fw-bold mb-4">🛒 จัดการคำสั่งซื้อ</h1>
      <div class="card p-3">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>ลูกค้า</th>
              <th>สินค้า</th>
              <th>ยอดรวม</th>
              <th>สถานะ</th>
              <th>การจัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1001</td>
              <td>สมชาย ใจดี</td>
              <td>หนังสือ A</td>
              <td>350 บาท</td>
              <td><span class="badge bg-warning text-dark">รอดำเนินการ</span></td>
              <td>
                <button class="btn btn-sm btn-success">✅ อนุมัติ</button>
                <button class="btn btn-sm btn-danger">❌ ยกเลิก</button>
              </td>
            </tr>
            <tr>
              <td>1002</td>
              <td>สมหญิง สายบุญ</td>
              <td>หนังสือ B</td>
              <td>220 บาท</td>
              <td><span class="badge bg-success">สำเร็จ</span></td>
              <td>
                <button class="btn btn-sm btn-secondary" disabled>✔ เสร็จสิ้น</button>
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
