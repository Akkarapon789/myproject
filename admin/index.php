<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../connectdb.php';

// ดึงสรุปข้อมูล
$users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$total_sales = $conn->query("SELECT SUM(total) as s FROM orders")->fetch_assoc()['s'] ?? 0;

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

// ดึงคำสั่งซื้อล่าสุด 5 รายการ
$latest = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <?php include 'layout.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php" class="active">แดชบอร์ด</a>
    <a href="users.php">ผู้ใช้</a>
    <a href="products.php">สินค้า</a>
    <a href="orders.php">คำสั่งซื้อ</a>
    <a href="reports.php">รายงาน</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <div class="content flex-grow-1">
    <h2>📊 แดชบอร์ดสรุป</h2>

    <div class="row g-4 mb-4">
      <div class="col-md-3"><div class="card p-3 text-center bg-primary text-white">ผู้ใช้ทั้งหมด <h3><?= $users; ?></h3></div></div>
      <div class="col-md-3"><div class="card p-3 text-center bg-success text-white">สินค้า <h3><?= $products; ?></h3></div></div>
      <div class="col-md-3"><div class="card p-3 text-center bg-warning text-dark">คำสั่งซื้อ <h3><?= $orders; ?></h3></div></div>
      <div class="col-md-3"><div class="card p-3 text-center bg-danger text-white">ยอดขายรวม <h3><?= number_format($total_sales,2); ?>฿</h3></div></div>
    </div>

    <div class="row g-4">
      <div class="col-md-8">
        <div class="card p-4">
          <h5>ยอดขายรายเดือน</h5>
          <canvas id="salesChart"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5>คำสั่งซื้อล่าสุด</h5>
          <table class="table table-sm">
            <tr><th>ID</th><th>ลูกค้า</th><th>ยอดรวม</th></tr>
            <?php while($o=$latest->fetch_assoc()): ?>
              <tr>
                <td>#<?= $o['id']; ?></td>
                <td><?= htmlspecialchars($o['fullname']); ?></td>
                <td><?= number_format($o['total'],2); ?>฿</td>
              </tr>
            <?php endwhile; ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($months); ?>,
    datasets: [{
      label: 'ยอดขาย (บาท)',
      data: <?= json_encode($sales); ?>,
      borderColor: 'rgba(75, 192, 192, 1)',
      backgroundColor: 'rgba(75, 192, 192, 0.3)',
      fill: true,
      tension: 0.3
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
</body>
</html>
