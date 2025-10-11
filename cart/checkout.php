<?php
// cart/checkout.php (Corrected & Final Version)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/connectdb.php';

// ตรวจสอบว่าล็อกอินหรือยัง และมีของในตะกร้าไหม
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=cart/checkout.php');
    exit();
}
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// ใช้ยอดรวมสุทธิจาก Session (ถ้ามี) หรือคำนวณใหม่
$final_total = $_SESSION['final_total'] ?? array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));

// ดึงข้อมูลผู้ใช้ปัจจุบันมาใส่ในฟอร์ม
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

include '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยืนยันการสั่งซื้อ - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<div class="container my-5">
    <h1><i class="fas fa-shipping-fast"></i> ยืนยันการสั่งซื้อ</h1>
    <p class="lead">กรุณาตรวจสอบข้อมูลและเลือกวิธีการชำระเงิน</p>

    <div class="row mt-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="checkout_process.php" method="POST">
                        <h4 class="mb-3">ข้อมูลสำหรับจัดส่ง</h4>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="fullname" class="form-label">ชื่อ-นามสกุล ผู้รับ</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user_data['firstname'] . ' ' . $user_data['lastname']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone']) ?>" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">ที่อยู่สำหรับจัดส่ง</label>
                                <textarea class="form-control" id="address" name="address" rows="4" required><?= htmlspecialchars($user_data['address']) ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h4 class="mb-3">วิธีการชำระเงิน</h4>
                        <div class="my-3">
                            <div class="form-check">
                                <input id="cod" name="payment" type="radio" class="form-check-input" value="cod" checked required>
                                <label class="form-check-label" for="cod">ชำระเงินปลายทาง (Cash on Delivery)</label>
                            </div>
                            <div class="form-check">
                                <input id="bank" name="payment" type="radio" class="form-check-input" value="bank" required>
                                <label class="form-check-label" for="bank">โอนเงินผ่านธนาคาร / QR Code</label>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">ยืนยันการสั่งซื้อ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">รายการสินค้า</h4>
                    <?php 
                        $cart_items = $_SESSION['cart'] ?? [];
                        foreach ($cart_items as $item): 
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?= htmlspecialchars($item['title']) ?> (x<?= $item['quantity'] ?>)</span>
                        <span>฿<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>ยอดรวมสุทธิ</span>
                        <span>฿<?= number_format($final_total, 2) ?></span>
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