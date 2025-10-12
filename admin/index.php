<?php
// admin/index.php (Corrected with Data Check)
session_start();
include '../config/connectdb.php';
include 'header.php';

// --- (โค้ด PHP สำหรับดึงข้อมูลทั้งหมดเหมือนเดิม) ---
function getSingleValue($conn, $sql) {
    $result = $conn->query($sql);
    $value = ($result && $result->num_rows > 0) ? $result->fetch_array()[0] : 0;
    return $value ?? 0;
}
$total_sales    = getSingleValue($conn, "SELECT SUM(total) FROM orders WHERE status = 'completed'");
$total_orders   = getSingleValue($conn, "SELECT COUNT(id) FROM orders");
$total_products = getSingleValue($conn, "SELECT COUNT(id) FROM products");
$total_users    = getSingleValue($conn, "SELECT COUNT(user_id) FROM `user`");
$sales_by_month_labels = [];
$sales_by_month_data = [];
$sql_sales = "SELECT DATE_FORMAT(created_at, '%b %y') AS month, SUM(total) AS monthly_sales FROM orders WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC";
$result_sales = $conn->query($sql_sales);
if ($result_sales) {
    while($row = $result_sales->fetch_assoc()) {
        $sales_by_month_labels[] = $row['month'];
        $sales_by_month_data[] = $row['monthly_sales'];
    }
}
$category_labels = [];
$category_data = [];
$sql_cats = "SELECT c.title, SUM(oi.quantity) AS total_quantity FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN categories c ON p.category_id = c.id GROUP BY c.id, c.title ORDER BY total_quantity DESC LIMIT 5";
$result_cats = $conn->query($sql_cats);
if ($result_cats) {
    while($row = $result_cats->fetch_assoc()){
        $category_labels[] = $row['title'];
        $category_data[] = $row['total_quantity'];
    }
}
$recent_orders = $conn->query("SELECT id, fullname, total, status FROM orders ORDER BY created_at DESC LIMIT 5");

?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">แดชบอร์ด</h1>
    <span class="d-none d-sm-inline-block text-muted" id="live-clock"></span>
</div>

<div class="row">
    </div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4 d-flex flex-column">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ภาพรวมยอดขายรายเดือน</h6>
            </div>
            <div class="card-body flex-grow-1 d-flex align-items-center justify-content-center">
                <?php if (!empty($sales_by_month_data)): ?>
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>ยังไม่มีข้อมูลยอดขาย</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4 d-flex flex-column">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">5 หมวดหมู่ขายดี (ตามจำนวนชิ้น)</h6>
            </div>
            <div class="card-body flex-grow-1 d-flex align-items-center justify-content-center">
                <?php if (!empty($category_data)): ?>
                    <div class="chart-pie pt-4">
                        <canvas id="categoryChart"></canvas>
                    </div>
                <?php else: ?>
                     <div class="text-center text-muted">
                        <i class="fas fa-chart-pie fa-3x mb-3"></i>
                        <p>ยังไม่มีข้อมูลการขาย</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 mt-4">
    </div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // ... (โค้ด Animated Counters และ Live Clock เหมือนเดิม) ...

    // --- Charts ---
    // ⭐️⭐️⭐️ 3. เพิ่มเงื่อนไขใน JavaScript ด้วย ⭐️⭐️⭐️
    <?php if (!empty($sales_by_month_data)): ?>
    new Chart(document.getElementById("salesChart"), {
        type: 'line',
        data: {
            labels: <?= json_encode($sales_by_month_labels) ?>,
            datasets: [{
                label: "ยอดขาย",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                data: <?= json_encode($sales_by_month_data) ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { ticks: { callback: (value) => '฿' + new Intl.NumberFormat().format(value) } } },
            plugins: { legend: { display: false } }
        }
    });
    <?php endif; ?>

    <?php if (!empty($category_data)): ?>
    new Chart(document.getElementById("categoryChart"), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($category_labels) ?>,
            datasets: [{
                data: <?= json_encode($category_data) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            }],
        },
        options: { 
            maintainAspectRatio: false, 
            cutout: '80%', 
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: { font: { family: 'Prompt' } }
                } 
            } 
        }
    });
    <?php endif; ?>
});
</script>