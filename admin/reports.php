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
  <title>รายงาน</title>
  <?php include 'style.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h4> Admin Panel</h4>
      <a href="index.php"> Dashboard</a>
      <a href="users.php"> จัดการผู้ใช้</a>
      <a href="products.php"> จัดการสินค้า</a>
      <a href="orders.php"> คำสั่งซื้อ</a>
      <a href="reports.php" class="active"> รายงาน</a>
      <hr>
      <a href="adminout.php" class="text-danger"> ออกจากระบบ</a>
    </div>

    <!-- Content -->
    <div class="col-md-9 col-lg-10 content">
      <h1 class="fw-bold mb-4"> รายงานสรุป</h1>

      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card text-center p-3">
            <h5>ยอดขายเดือนนี้</h5>
            <h2 class="text-success">฿45,000</h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center p-3">
            <h5>คำสั่งซื้อเดือนนี้</h5>
            <h2 class="text-info">34</h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center p-3">
            <h5>ผู้ใช้ใหม่เดือนนี้</h5>
            <h2 class="text-warning">15</h2>
          </div>
        </div>
      </div>

      <div class="card p-4">
        <h5 class="mb-3">📈 กราฟยอดขาย 6 เดือนล่าสุด</h5>
        <canvas id="salesChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.'],
    datasets: [{
      label: 'ยอดขาย (บาท)',
      data: [12000, 15000, 11000, 18000, 20000, 22000],
      backgroundColor: 'rgba(54, 162, 235, 0.6)',
      borderRadius: 6
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
</script>
</body>
</html>
