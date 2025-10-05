<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';

// ดึงยอดขายรายเดือน
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as sales 
        FROM orders GROUP BY month ORDER BY month ASC";
$result = $conn->query($sql);
$months = [];
$sales = [];
while($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $sales[] = $row['sales'];
}

// ดึงจำนวนผู้ใช้ / สินค้า
$users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายงาน</title>
  <?php include 'layout.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php">แดชบอร์ด</a>
    <a href="users.php">ผู้ใช้</a>
    <a href="products.php">สินค้า</a>
    <a href="orders.php">คำสั่งซื้อ</a>
    <a href="reports.php" class="active">รายงาน</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <div class="content flex-grow-1">
    <h2>📊 รายงาน</h2>
    <div class="row g-4 mb-4">
      <div class="col-md-4"><div class="card p-3 text-center">ผู้ใช้: <h3><?= $users; ?></h3></div></div>
      <div class="col-md-4"><div class="card p-3 text-center">สินค้า: <h3><?= $products; ?></h3></div></div>
      <div class="col-md-4"><div class="card p-3 text-center">คำสั่งซื้อ: <h3><?= $orders; ?></h3></div></div>
    </div>

    <div class="card p-4">
      <h4>ยอดขายรายเดือน</h4>
      <canvas id="salesChart"></canvas>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($months); ?>,
    datasets: [{
      label: 'ยอดขาย (บาท)',
      data: <?= json_encode($sales); ?>,
      backgroundColor: 'rgba(54, 162, 235, 0.6)',
      borderRadius: 6
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>
</body>
</html>
