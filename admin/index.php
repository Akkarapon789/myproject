<?php
// index.php (Upgraded)
include 'header.php';

// --- ดึงข้อมูลสำหรับ Stat Cards ---
// ใช้ prepared statements เพื่อความปลอดภัยและประสิทธิภาพ
function getSingleValue($conn, $sql, $types = null, $params = []) {
    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    // Return the first value from the result array, or 0 if null
    return $result ? array_values($result)[0] ?? 0 : 0;
}

$total_sales = getSingleValue($conn, "SELECT SUM(total) FROM orders WHERE status = 'completed'");
$total_orders = getSingleValue($conn, "SELECT COUNT(id) FROM orders");
$total_users = getSingleValue($conn, "SELECT COUNT(user_id) FROM users");
$total_products = getSingleValue($conn, "SELECT COUNT(id) FROM products");


// --- ดึงข้อมูลสำหรับกราฟยอดขายรายเดือน ---
$sales_by_month_labels = [];
$sales_by_month_data = [];
$sql_sales = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total) AS monthly_sales 
              FROM orders 
              WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
              GROUP BY month 
              ORDER BY month ASC";
$result_sales = $conn->query($sql_sales);
while($row = $result_sales->fetch_assoc()) {
    $sales_by_month_labels[] = $row['month'];
    $sales_by_month_data[] = $row['monthly_sales'];
}

// --- ดึงข้อมูลสำหรับกราฟหมวดหมู่สินค้าขายดี ---
$category_labels = [];
$category_data = [];
$sql_cats = "SELECT c.title, SUM(oi.quantity * oi.price) AS category_sales
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             JOIN categories c ON p.category_id = c.id
             GROUP BY c.title
             ORDER BY category_sales DESC LIMIT 5"; // แสดง 5 หมวดหมู่ขายดีที่สุด
$result_cats = $conn->query($sql_cats);
while($row = $result_cats->fetch_assoc()){
    $category_labels[] = $row['title'];
    $category_data[] = $row['category_sales'];
}


// --- ดึงข้อมูลออเดอร์ล่าสุด ---
$recent_orders = [];
$sql_recent = "SELECT o.id, u.firstname, u.lastname, o.total, o.status 
               FROM orders o
               JOIN users u ON o.user_id = u.user_id
               ORDER BY o.created_at DESC LIMIT 5";
$result_recent = $conn->query($sql_recent);
while($row = $result_recent->fetch_assoc()){
    $recent_orders[] = $row;
}
?>

<div class="container-fluid">

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
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
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
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
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
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
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
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">ภาพรวมยอดขายรายเดือน</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">5 หมวดหมู่ขายดี</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">5 คำสั่งซื้อล่าสุด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อลูกค้า</th>
                            <th>ยอดรวม (บาท)</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) ?></td>
                            <td><?= number_format($order['total'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= ($order['status'] == 'completed' ? 'success' : 'warning') ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php include 'footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sales Chart (Line)
    var ctxSales = document.getElementById("salesChart").getContext('2d');
    var salesChart = new Chart(ctxSales, {
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
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                data: <?= json_encode($sales_by_month_data) ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    ticks: {
                        callback: function(value, index, values) {
                            return '฿' + new Intl.NumberFormat().format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Category Chart (Pie)
    var ctxCategory = document.getElementById("categoryChart").getContext('2d');
    var categoryChart = new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($category_labels) ?>,
            datasets: [{
                data: <?= json_encode($category_data) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#c73024'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '80%',
             plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        },
    });
});
</script>