<?php
// เปิด Error Reporting เพื่อหาปัญหา
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../config/connectdb.php';
include 'header.php'; // 1. เรียกใช้ส่วนหัว

// ตรวจสอบการเชื่อมต่อฐานข้อมูลก่อน
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// --- ฟังก์ชันช่วยดึงข้อมูลตัวเลขเดียว เพื่อลดการเขียนโค้ดซ้ำ ---
function getSingleValue($conn, $sql) {
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $value = $result->fetch_array()[0];
        return $value ?? 0; // คืนค่าที่ได้ หรือ 0 ถ้าเป็น null
    }
    return 0; // คืนค่า 0 ถ้า query ไม่สำเร็จ
}

// === 1. ดึงข้อมูลสำหรับ Stat Cards ===
$total_sales    = getSingleValue($conn, "SELECT SUM(total) FROM orders WHERE status = 'completed'");
$total_orders   = getSingleValue($conn, "SELECT COUNT(id) FROM orders");
$total_products = getSingleValue($conn, "SELECT COUNT(id) FROM products");
$total_users    = getSingleValue($conn, "SELECT COUNT(user_id) FROM `user`");


// === 2. ดึงข้อมูลสำหรับกราฟ ===
// กราฟยอดขายรายเดือน (12 เดือนล่าสุด)
$sales_by_month_labels = [];
$sales_by_month_data = [];
$sql_sales = "SELECT DATE_FORMAT(created_at, '%b %Y') AS month, SUM(total) AS monthly_sales 
              FROM orders 
              WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
              ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC";
$result_sales = $conn->query($sql_sales);
while($row = $result_sales->fetch_assoc()) {
    $sales_by_month_labels[] = $row['month'];
    $sales_by_month_data[] = $row['monthly_sales'];
}

// กราฟ 5 หมวดหมู่ขายดี
$category_labels = [];
$category_data = [];
$sql_cats = "SELECT c.title, SUM(oi.quantity) AS total_quantity
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             JOIN categories c ON p.category_id = c.id
             GROUP BY c.title
             ORDER BY total_quantity DESC LIMIT 5";
$result_cats = $conn->query($sql_cats);
while($row = $result_cats->fetch_assoc()){
    $category_labels[] = $row['title'];
    $category_data[] = $row['total_quantity'];
}


// === 3. ดึงข้อมูลออเดอร์ล่าสุด ===
$recent_orders = [];
$sql_recent = "SELECT o.id, o.fullname, o.total, o.status, u.firstname, u.lastname
               FROM orders o
               LEFT JOIN `user` u ON o.user_id = u.user_id
               ORDER BY o.created_at DESC LIMIT 5";
$result_recent = $conn->query($sql_recent);
while($row = $result_recent->fetch_assoc()){
    $recent_orders[] = $row;
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">แดชบอร์ด</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> สร้างรายงาน</a>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ยอดขายทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">฿<?= number_format($total_sales, 2) ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-body-tertiary"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">คำสั่งซื้อทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_orders) ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-body-tertiary"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">สินค้าทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_products) ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-box fa-2x text-body-tertiary"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ผู้ใช้งานทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_users) ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-body-tertiary"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">ภาพรวมยอดขายรายเดือน</h6></div>
            <div class="card-body"><div class="chart-area"><canvas id="salesChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">5 หมวดหมู่ขายดี (ตามจำนวนชิ้น)</h6></div>
            <div class="card-body"><div class="chart-pie pt-4"><canvas id="categoryChart"></canvas></div></div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">5 คำสั่งซื้อล่าสุด</h6></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อลูกค้า</th>
                        <th>ยอดรวม</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars((!empty($order['firstname'])) ? $order['firstname'].' '.$order['lastname'] : $order['fullname']) ?></td>
                        <td>฿<?= number_format($order['total'], 2) ?></td>
                        <td><span class="badge bg-<?= ($order['status'] == 'completed' ? 'success' : 'warning text-dark') ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sales Chart (Line)
    new Chart(document.getElementById("salesChart"), {
        type: 'line',
        data: {
            labels: <?= json_encode($sales_by_month_labels) ?>,
            datasets: [{
                label: "ยอดขาย",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                data: <?= json_encode($sales_by_month_data) ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { ticks: { callback: (value) => '฿' + new Intl.NumberFormat().format(value) } } },
            plugins: { legend: { display: false } }
        }
    });

    // Category Chart (Doughnut)
    new Chart(document.getElementById("categoryChart"), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($category_labels) ?>,
            datasets: [{
                data: <?= json_encode($category_data) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            }],
        },
        options: { maintainAspectRatio: false, cutout: '80%', plugins: { legend: { position: 'bottom' } } }
    });
});
</script>

<?php 
include 'footer.php'; // 2. เรียกใช้ส่วนท้าย
?>