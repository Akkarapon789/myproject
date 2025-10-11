<?php
// cart/payment.php (Updated with correct QR code image)
session_start();
include '../config/connectdb.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if ($order_id === 0) {
    header('Location: ../pages/index.php');
    exit();
}

// ดึงยอดเงินของออเดอร์นี้
$stmt = $conn->prepare("SELECT total FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("ไม่พบคำสั่งซื้อ");
}

include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ชำระเงิน - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-3">ชำระเงินสำหรับคำสั่งซื้อ #<?= $order_id ?></h1>
                    <p class="text-muted">กรุณาสแกน QR Code ด้านล่างเพื่อชำระเงิน</p>
                    
                    <div class="my-4">
                        <p class="mb-2">ยอดที่ต้องชำระ:</p>
                        <h2 class="display-5 fw-bold text-primary">฿<?= number_format($order['total'], 2) ?></h2>
                    </div>

                    <img src="../assets/qr/promptpay.jpg" class="img-fluid rounded mb-4" style="max-width: 250px;" alt="QR Code for Payment">

                    <div class="alert alert-light border">
                        <h5 class="alert-heading">หรือโอนผ่านบัญชีธนาคาร</h5>
                        <p>ธนาคาร: กสิกรไทย<br>
                           ชื่อบัญชี: อัครพนธ์ ป้อมพระราช<br>
                           เลขที่บัญชี: <strong>126-2-91326-7</strong>
                        </p>
                    </div>
                    
                    <p class="mt-4">หลังจากชำระเงินแล้ว ระบบจะทำการตรวจสอบและอัปเดตสถานะให้ท่านทราบทางอีเมล</p>
                    
                    <div class="d-grid gap-2 mt-4">
                         <a href="../pages/order_history.php" class="btn btn-primary btn-lg">ดูประวัติการสั่งซื้อ</a>
                         <a href="../pages/index.php" class="btn btn-outline-secondary">กลับไปหน้าแรก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>