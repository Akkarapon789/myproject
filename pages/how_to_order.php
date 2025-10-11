<?php
session_start();
include '../config/connectdb.php';
$is_logged_in = isset($_SESSION['user_id']);
include '../includes/navbar.php';
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>วิธีการสั่งซื้อ - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css">
</head>
<body>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5">วิธีการสั่งซื้อ</h1>
        <p class="lead text-muted">สั่งซื้อง่ายๆ เพียง 5 ขั้นตอน</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-circle bg-light mx-auto mb-3"><i class="fas fa-search fa-2x text-primary"></i></div>
                    <h5 class="card-title">1. เลือกชมสินค้า</h5>
                    <p class="card-text text-muted">คุณสามารถค้นหาหนังสือที่ต้องการ หรือเลือกชมจากหมวดหมู่ต่างๆ ที่เราจัดเตรียมไว้ให้</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-circle bg-light mx-auto mb-3"><i class="fas fa-cart-plus fa-2x text-primary"></i></div>
                    <h5 class="card-title">2. เพิ่มลงตะกร้า</h5>
                    <p class="card-text text-muted">เมื่อเจอหนังสือที่ถูกใจ กดปุ่ม "เพิ่มลงตะกร้า" เพื่อเก็บสินค้าไว้</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-circle bg-light mx-auto mb-3"><i class="fas fa-shopping-cart fa-2x text-primary"></i></div>
                    <h5 class="card-title">3. ตรวจสอบตะกร้า</h5>
                    <p class="card-text text-muted">ไปที่หน้าตะกร้าสินค้าเพื่อตรวจสอบรายการ, ปรับแก้จำนวน, และใช้โค้ดส่วนลดโปรโมชั่น</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-circle bg-light mx-auto mb-3"><i class="fas fa-map-marked-alt fa-2x text-primary"></i></div>
                    <h5 class="card-title">4. ยืนยันการสั่งซื้อ</h5>
                    <p class="card-text text-muted">กรอกข้อมูลที่อยู่สำหรับจัดส่ง และเลือกวิธีการชำระเงินที่คุณสะดวก</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-circle bg-light mx-auto mb-3"><i class="fas fa-check-circle fa-2x text-primary"></i></div>
                    <h5 class="card-title">5. ชำระเงินและรอรับ</h5>
                    <p class="card-text text-muted">ชำระเงินตามช่องทางที่เลือก และรอรับหนังสือเล่มโปรดที่หน้าบ้านของคุณได้เลย</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>