<?php
// pages/order_detail.php
session_start();
include '../config/connectdb.php';

// 1. ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_logged_in = true;

// 2. ⭐️ ความปลอดภัย: ดึงข้อมูลออเดอร์ โดยต้องแน่ใจว่าเป็นของ user ที่ล็อกอินอยู่เท่านั้น ⭐️
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$order = $order_result->fetch_assoc();
$stmt_order->close();

// ถ้าไม่เจอออเดอร์ (อาจจะใส่ ID มั่ว หรือเป็นออเดอร์ของคนอื่น)
if (!$order) {
    die("ไม่พบคำสั่งซื้อ หรือคุณไม่มีสิทธิ์เข้าถึงหน้านี้");
}

// 3. ดึงข้อมูลสินค้าทั้งหมดในออเดอร์นี้
$stmt_items = $conn->prepare("SELECT oi.*, p.title as product_title, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$order_items_result = $stmt_items->get_result();
$stmt_items->close();

include '../includes/navbar.php';
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดคำสั่งซื้อ #<?= $order_id ?> - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">หน้าแรก</a></li>
            <li class="breadcrumb-item"><a href="order_history.php">ประวัติการสั่งซื้อ</a></li>
            <li class="breadcrumb-item active" aria-current="page">คำสั่งซื้อ #<?= $order_id ?></li>
        </ol>
    </nav>
    <h1 class="mb-4">รายละเอียดคำสั่งซื้อ #<?= $order_id ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-bold">สินค้าในคำสั่งซื้อ</div>
                <div class="card-body">
                    <?php while($item = $order_items_result->fetch_assoc()): ?>
                    <div class="row mb-3 align-items-center">
                        <div class="col-2"><img src="../<?= htmlspecialchars($item['image_url'] ?? 'assets/default.jpg') ?>" class="img-fluid rounded"></div>
                        <div class="col-6">
                            <div><?= htmlspecialchars($item['product_title']) ?></div>
                            <small class="text-muted"><?= $item['quantity'] ?> x ฿<?= number_format($item['price'], 2) ?></small>
                        </div>
                        <div class="col-4 text-end">฿<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                    </div>
                    <hr>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold">ข้อมูลสรุป</div>
                <div class="card-body">
                    <p><strong>วันที่สั่งซื้อ:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>สถานะ:</strong> <span class="badge bg-warning text-dark"><?= ucfirst($order['status']) ?></span></p>
                    <hr>
                    <p class="mb-1"><strong>จัดส่งไปที่:</strong></p>
                    <p class="text-muted">
                        <?= htmlspecialchars($order['fullname']) ?><br>
                        <?= nl2br(htmlspecialchars($order['address'])) ?><br>
                        โทร. <?= htmlspecialchars($order['phone']) ?>
                    </p>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>ยอดรวมสุทธิ:</span>
                        <span>฿<?= number_format($order['total'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>