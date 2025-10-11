<?php
// pages/promotions.php (Revamped)
session_start();
include '../config/connectdb.php';
$is_logged_in = isset($_SESSION['role']);

// ดึงโปรโมชั่นทั้งหมด พร้อมชื่อสินค้า (ถ้ามี)
$sql = "SELECT pr.*, p.title as product_title
        FROM promotions pr
        LEFT JOIN products p ON pr.product_id = p.id
        ORDER BY pr.end_date ASC"; // เรียงตามโปรโมชั่นที่ใกล้หมดอายุก่อน
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>โปรโมชั่นสุดพิเศษ - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css"> </head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5">โปรโมชั่นสุดพิเศษ</h1>
        <p class="lead text-muted">ส่วนลดและข้อเสนอดีๆ สำหรับคนรักหนังสือ</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card promo-card h-100">
                        <div class="promo-card-header">
                            <?= htmlspecialchars($row['name']) ?>
                        </div>
                        <div class="promo-card-body text-center">
                            <?php
                                // Logic สำหรับแสดงผลส่วนลดให้สวยงาม
                                $discount_text = '';
                                if ($row['discount_type'] == 'percentage') {
                                    $discount_text = $row['discount_value'] . '%';
                                } elseif ($row['discount_type'] == 'fixed') {
                                    $discount_text = '฿' . number_format($row['discount_value']);
                                }
                            ?>
                            <div class="promo-discount mb-3"><?= $discount_text ?></div>
                            <p class="text-muted">
                                <?php if($row['product_title']): ?>
                                    สำหรับหนังสือ: <strong><?= htmlspecialchars($row['product_title']) ?></strong>
                                <?php else: ?>
                                    สำหรับสินค้าทุกรายการที่ร่วมรายการ
                                <?php endif; ?>
                            </p>
                            <span class="badge rounded-pill bg-light text-dark fw-normal">
                                <i class="far fa-calendar-alt me-1"></i> ใช้ได้ถึง: <?= date('d M Y', strtotime($row['end_date'])) ?>
                            </span>
                        </div>
                        <div class="promo-card-footer text-center">
                            <a href="all_products.php" class="btn btn-primary">เลือกซื้อสินค้า</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted fs-4 mt-5">ยังไม่มีโปรโมชั่นในขณะนี้</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>