<?php
// admin/index.php (Corrected Chart Layout - FINAL)
session_start();
include '../config/connectdb.php';
include 'header.php';

// --- (โค้ด PHP สำหรับดึงข้อมูลทั้งหมดเหมือนเดิม) ---
function getSingleValue($conn, $sql) { /* ... */ }
// ... (โค้ดดึงข้อมูลทั้งหมด) ...
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
            <div class="card-body flex-grow-1">
                <div class="chart-area">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4 d-flex flex-column">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">5 หมวดหมู่ขายดี (ตามจำนวนชิ้น)</h6>
            </div>
            <div class="card-body flex-grow-1 d-flex flex-column">
                <div class="chart-pie pt-4">
                    <canvas id="categoryChart"></canvas>
                </div>
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
    new Chart(document.getElementById("salesChart"), {
        type: 'line',
        data: {
            labels: <?= json_encode($sales_by_month_labels) ?>,
            datasets: [/* ... */],
        },
        options: {
            maintainAspectRatio: false, // คำสั่งนี้สำคัญมาก
            // ... (options อื่นๆ เหมือนเดิม)
        }
    });

    new Chart(document.getElementById("categoryChart"), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($category_labels) ?>,
            datasets: [/* ... */],
        },
        options: { 
            maintainAspectRatio: false, // คำสั่งนี้สำคัญมาก
            // ... (options อื่นๆ เหมือนเดิม)
        }
    });
});
</script>