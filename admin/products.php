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
  <title>จัดการสินค้า</title>
  <?php include 'style.php'; ?>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h4> Admin Panel</h4>
      <a href="index.php"> Dashboard</a>
      <a href="users.php"> จัดการผู้ใช้</a>
      <a href="products.php" class="active"> จัดการสินค้า</a>
      <a href="orders.php"> คำสั่งซื้อ</a>
      <a href="reports.php"> รายงาน</a>
      <hr>
      <a href="adminout.php" class="text-danger"> ออกจากระบบ</a>
    </div>

    <!-- Content -->
    <div class="col-md-9 col-lg-10 content">
      <h1 class="fw-bold mb-4"> จัดการสินค้า</h1>
      <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-success">+ เพิ่มสินค้าใหม่</button>
      </div>
      <div class="card p-3">
        <table class="table table-striped table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>ชื่อสินค้า</th>
              <th>ราคา</th>
              <th>สต็อก</th>
              <th>การจัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>101</td>
              <td>หนังสือ A</td>
              <td>350 บาท</td>
              <td>20</td>
              <td>
                <button class="btn btn-sm btn-outline-warning">แก้ไข</button>
                <button class="btn btn-sm btn-outline-danger">ลบ</button>
              </td>
            </tr>
            <tr>
              <td>102</td>
              <td>หนังสือ B</td>
              <td>220 บาท</td>
              <td>15</td>
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
