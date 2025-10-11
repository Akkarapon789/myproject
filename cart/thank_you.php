<?php
// cart/thank_you.php
session_start();
require_once '../config/connectdb.php';
include '../includes/navbar.php'; // เรียกใช้ Navbar ของเรา

$order_id = $_GET['order_id'] ?? 0;

$sql = "SELECT fullname, email FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: ../pages/index.php");
    exit();
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยืนยันการสั่งซื้อสำเร็จ - The Bookmark Society</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../includes/css/style.css"> <style>
      .confirmation-card {
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        border: none;
      }
      .check-icon {
        font-size: 4rem;
        color: #198754;
      }
    </style>
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card confirmation-card text-center p-4 p-md-5 w-100" style="max-width: 600px;">
        <div class="card-body">
            <div class="check-icon mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="h3 mb-3">ยืนยันการสั่งซื้อสำเร็จ!</h1>
            
            <p class="lead text-muted mb-4">
                ขอบคุณสำหรับการสั่งซื้อ, <strong><?= htmlspecialchars($order['fullname']) ?></strong>!
            </p>
            
            <div class="alert alert-light border">
                <p class="mb-1">รหัสคำสั่งซื้อของคุณคือ: <strong>#<?= $order_id ?></strong></p>
                <p class="mb-0">เราได้ส่งอีเมลยืนยันไปที่ <strong><?= htmlspecialchars($order['email']) ?></strong> เรียบร้อยแล้ว</p>
            </div>
            
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                <a href="../pages/order_history.php?id=<?= $order_id ?>" class="btn btn-outline-primary btn-lg px-4">ดูประวัติการสั่งซื้อ</a>
                <a href="../pages/index.php" class="btn btn-primary btn-lg px-4">กลับไปหน้าแรก</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>