<?php
// admin/index.php (Corrected Card Height)
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
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ยอดขายทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" data-count="<?= $total_sales ?>">฿0.00</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800" data-count="<?= $total_orders ?>">0</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800" data-count="<?= $total_products ?>">0</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800" data-count="<?= $total_users ?>">0</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-body-tertiary"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
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
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">5 หมวดหมู่ขายดี (ตามจำนวนชิ้น)</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">5 คำสั่งซื้อล่าสุด</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อลูกค้า</th>
                        <th class="text-end">ยอดรวม</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                        <?php while($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td><a href="order_details.php?id=<?= $order['id'] ?>">#<?= $order['id'] ?></a></td>
                            <td><?= htmlspecialchars($order['fullname']) ?></td>
                            <td class="text-end">฿<?= number_format($order['total'], 2) ?></td>
                            <td class="text-center"><span class="badge bg-<?= ($order['status'] == 'completed' ? 'success' : 'warning text-dark') ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">ยังไม่มีคำสั่งซื้อ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // --- Animated Counters ---
    $('[data-count]').each(function () {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).data('count')
        }, {
            duration: 1500,
            easing: 'swing',
            step: function (now) {
                if ($(this).data('count').toString().includes('.')) {
                    $(this).text('฿' + parseFloat(now).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                } else {
                    $(this).text(Math.ceil(now).toLocaleString('en-US'));
                }
            }
        });
    });

    // --- Live Clock ---
    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        $('#live-clock').text(now.toLocaleDateString('th-TH', options));
    }
    updateClock();
    setInterval(updateClock, 1000);

    // --- Charts ---
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
});
</script>