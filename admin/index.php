<?php
// แสดง error ถ้ามี (สำหรับ debug)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
include '../config/connectdb.php';

// ดึงข้อมูลสรุป
$users       = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'] ?? 0;
$products    = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'] ?? 0;
$orders      = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'] ?? 0;
$total_sales = $conn->query("SELECT SUM(total) AS s FROM orders")->fetch_assoc()['s'] ?? 0;

// ดึงยอดขายรายเดือน
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as sales 
        FROM orders GROUP BY month ORDER BY month ASC";
$result = $conn->query($sql);
$months = []; $sales = [];
while ($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $sales[] = $row['sales'];
}

// ดึงยอดขายตามหมวดหมู่
$sql2 = "SELECT c.title AS category, SUM(od.qty * od.price) AS total_sales
         FROM order_detail od
         JOIN products p ON od.product_id = p.id
         JOIN categories c ON p.category_id = c.id
         GROUP BY c.id";
$res2 = $conn->query($sql2);
$cats = []; $cat_sales = [];
while ($r = $res2->fetch_assoc()) {
    $cats[] = $r['category'];
    $cat_sales[] = $r['total_sales'];
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
  <!-- Sidebar -->
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php" class="active">แดชบอร์ด</a>
    <a href="users.php">ผู้ใช้</a>
    <a href="products.php">สินค้า</a>
    <a href="orders.php">คำสั่งซื้อ</a>
    <a href="reports.php">รายงาน</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <!-- Content -->
  <div class="content flex-grow-1">
    <h2>📊 แดชบอร์ดสรุป</h2>

    <!-- Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card p-3 text-center bg-primary text-white">
          ผู้ใช้ทั้งหมด <h3><?= $users; ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3 text-center bg-success text-white">
          สินค้า <h3><?= $products; ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3 text-center bg-warning text-dark">
          คำสั่งซื้อ <h3><?= $orders; ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3 text-center bg-danger text-white">
          ยอดขายรวม <h3><?= number_format($total_sales, 2); ?> ฿</h3>
        </div>
      </div>
    </div>

    <!-- Graphs -->
    <div class="row g-4">
      <div class="col-md-8">
        <div class="card p-4">
          <h5>ยอดขายรายเดือน</h5>
          <canvas id="salesChart"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <h5>สัดส่วนยอดขายตามหมวดหมู่</h5>
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Latest Orders -->
    <div class="row g-4 mt-4">
      <div class="col-md-12">
        <div class="card p-4">
          <h5>คำสั่งซื้อล่าสุด</h5>
          <table class="table table-sm">
            <tr><th>ID</th><th>ลูกค้า</th><th>ยอดรวม</th></tr>
            <?php while ($o = $latest->fetch_assoc()): ?>
              <tr>
                <td>#<?= $o['id']; ?></td>
                <td><?= htmlspecialchars($o['fullname']); ?></td>
                <td><?= number_format($o['total'], 2); ?> ฿</td>
              </tr>
            <?php endwhile; ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js Scripts -->
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
  options: { responsive: true }
});

const ctx2 = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx2, {
  type: 'pie',
  data: {
    labels: <?= json_encode($cats); ?>,
    datasets: [{
      data: <?= json_encode($cat_sales); ?>,
      backgroundColor: [
        'rgba(255, 99, 132, 0.6)',
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)'
      ]
    }]
  }
});
</script>
</body>
</html>
