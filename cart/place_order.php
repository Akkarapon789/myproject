<?php
session_start();
require_once '../config/connectdb.php';

// รับ ID ของออเดอร์จาก URL, ถ้าไม่มีให้เป็น 0
$order_id = $_GET['order_id'] ?? 0;

// ใช้ Prepared Statement เพื่อดึงข้อมูลอย่างปลอดภัย ป้องกัน SQL Injection
$sql = "SELECT fullname, email FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// ถ้าหาออเดอร์ไม่เจอ ให้ไปหน้าแรก
if (!$order) {
    header("Location: ../index.php");
    exit();
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ยืนยันการสั่งซื้อสำเร็จ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
        background-color: #f8f9fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .confirmation-card {
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        border: none;
        max-width: 550px;
        width: 100%;
    }
    .check-icon {
        font-size: 5rem;
        color: #198754; /* สีเขียวของ Bootstrap Success */
    }
  </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card confirmation-card text-center p-4 p-md-5">
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
                        <p class="mb-0">เราได้ส่งอีเมลยืนยันคำสั่งซื้อไปที่ <strong><?= htmlspecialchars($order['email']) ?></strong> เรียบร้อยแล้ว</p>
                    </div>
                    
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                        <a href="order_history.php?id=<?= $order_id ?>" class="btn btn-outline-primary btn-lg px-4 gap-3">ดูรายละเอียดคำสั่งซื้อ</a>
                        <a href="../pages/index.php" class="btn btn-primary btn-lg px-4">กลับไปหน้าแรก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>