<?php
session_start();
require_once '../config/connectdb.php';

$cart = $_SESSION['cart'] ?? [];

// ✅ ถ้ามีการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cart)) {

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);
    $payment  = trim($_POST['payment']);

    // คำนวณราคารวม
    $total_price = 0;
    foreach ($cart as $item) {
        $total_price += $item['price'] * $item['qty'];
    }

    // ✅ บันทึกข้อมูลคำสั่งซื้อในตาราง orders
    $sql_order = "INSERT INTO orders (fullname, email, phone, address, payment, total_price, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("sssssd", $fullname, $email, $phone, $address, $payment, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // ✅ บันทึกรายการสินค้าใน order_detail
    $sql_detail = "INSERT INTO order_detail (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);

    foreach ($cart as $pid => $item) {
        $stmt_detail->bind_param("iiid", $order_id, $pid, $item['qty'], $item['price']);
        $stmt_detail->execute();

        // 🔹 อัปเดต stock (-จำนวนที่ซื้อ)
        $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update_stock->bind_param("ii", $item['qty'], $pid);
        $update_stock->execute();
        $update_stock->close();
    }

    $stmt_detail->close();

    // ✅ ล้างตะกร้า
    unset($_SESSION['cart']);

    // ✅ ไปหน้า place_order
    header("Location: place_order.php?order_id=" . $order_id);
    exit;
}

// คำนวณราคารวม (สำหรับตอนแสดง)
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['qty'];
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Checkout - The Bookmark Society</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .checkout-card {
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .order-summary {
      background: #fffdf5;
      border: 1px solid #f3eac2;
      border-radius: 10px;
    }
    .order-summary h5 {
      border-bottom: 2px solid #e9ecef;
      padding-bottom: .5rem;
      margin-bottom: 1rem;
    }
    .form-label {
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row">
    <!-- ฟอร์มที่อยู่ -->
    <div class="col-lg-7 mb-4">
      <div class="card checkout-card p-4">
        <h3 class="mb-4">ข้อมูลผู้สั่งซื้อ</h3>
        <?php if (empty($cart)): ?>
          <div class="alert alert-warning">ยังไม่มีสินค้าในตะกร้า กรุณากลับไปเลือกซื้อก่อน</div>
          <a href="../pages/index.php" class="btn btn-primary">กลับไปเลือกซื้อ</a>
        <?php else: ?>
        <form action="" method="POST">
          <div class="mb-3">
            <label class="form-label">ชื่อ-นามสกุล</label>
            <input type="text" class="form-control" name="fullname" required>
          </div>
          <div class="mb-3">
            <label class="form-label">อีเมล</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" class="form-control" name="phone" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ที่อยู่จัดส่ง</label>
            <textarea class="form-control" name="address" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">วิธีการชำระเงิน</label>
            <select class="form-select" name="payment" required>
              <option value="cod">เก็บเงินปลายทาง (COD)</option>
              <option value="bank">โอนผ่านธนาคาร</option>
              <option value="credit">บัตรเครดิต/เดบิต</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">ยืนยันการสั่งซื้อ</button>
          <button type="reset" class="btn btn-outline-secondary btn-lg w-100">ล้างข้อมูล</button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- สรุปออเดอร์ -->
    <div class="col-lg-5">
      <div class="order-summary p-4">
        <h5>🛒 สรุปรายการสินค้า</h5>
        <?php if (!empty($cart)): ?>
          <ul class="list-group mb-3">
            <?php foreach ($cart as $item): ?>
              <li class="list-group-item d-flex justify-content-between lh-sm">
                <div>
                  <h6 class="my-0"><?= htmlspecialchars($item['title']) ?></h6>
                  <small class="text-muted">จำนวน: <?= $item['qty'] ?></small>
                </div>
                <span class="text-muted">฿<?= number_format($item['price'] * $item['qty'], 2) ?></span>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between bg-light">
              <span class="fw-bold">ยอดรวมทั้งหมด</span>
              <strong>฿<?= number_format($total_price, 2) ?></strong>
            </li>
          </ul>
        <?php else: ?>
          <p class="text-muted">ไม่มีสินค้า</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
