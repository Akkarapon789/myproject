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
  <title>Admin Panel - The Bookmark Society</title>
  <?php include 'style.php'; ?>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h4 class="mb-4">📚 Admin Panel</h4>
      <a href="index.php">🏠 Dashboard</a>
      <a href="users.php">👥 จัดการผู้ใช้</a>
      <a href="products.php">📦 จัดการสินค้า</a>
      <a href="orders.php">🛒 จัดการคำสั่งซื้อ</a>
      <a href="reports.php">📊 รายงาน</a>
      <hr>
      <a href="../admin/adminout.php" class="text-danger">🚪 ออกจากระบบ</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 content">
      <h1 class="fw-bold mb-4">ยินดีต้อนรับ Admin: 
        <span class="text-primary"><?php echo htmlspecialchars($_SESSION['firstname']); ?></span>
      </h1>

      <!-- Cards Overview -->
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card shadow text-center p-3">
            <h5>ผู้ใช้งาน</h5>
            <h2 class="text-success">120</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow text-center p-3">
            <h5>สินค้า</h5>
            <h2 class="text-info">85</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow text-center p-3">
            <h5>คำสั่งซื้อ</h5>
            <h2 class="text-warning">34</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow text-center p-3">
            <h5>รายได้</h5>
            <h2 class="text-danger">฿45,000</h2>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">📌 คำสั่งซื้อล่าสุด</h5>
        </div>
        <div class="card-body">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>ชื่อลูกค้า</th>
                <th>สินค้า</th>
                <th>ยอดรวม</th>
                <th>สถานะ</th>
                <th>วันที่</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1001</td>
                <td>สมชาย ใจดี</td>
                <td>หนังสือ A</td>
                <td>฿350</td>
                <td><span class="badge bg-success">สำเร็จ</span></td>
                <td>2025-10-01</td>
              </tr>
              <tr>
                <td>1002</td>
                <td>สมหญิง สายบุญ</td>
                <td>หนังสือ B</td>
                <td>฿220</td>
                <td><span class="badge bg-warning text-dark">รอดำเนินการ</span></td>
                <td>2025-09-30</td>
              </tr>
              <tr>
                <td>1003</td>
                <td>อนันต์ คำดี</td>
                <td>หนังสือ C</td>
                <td>฿580</td>
                <td><span class="badge bg-danger">ยกเลิก</span></td>
                <td>2025-09-29</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
