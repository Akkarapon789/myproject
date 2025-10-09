<?php
session_start();
require_once '../config/connectdb.php';

$cart = $_SESSION['cart'] ?? [];
// [เพิ่ม 1] ดึง user_id ของลูกค้าที่ล็อกอินอยู่ ถ้าไม่มี (เป็นแขก) ให้เป็น null
$user_id = $_SESSION['user_id'] ?? null; 

// ฟังก์ชันเช็กข้อผิดพลาด SQL
function check_error($stmt, $conn) {
    if ($stmt === false) {
        die("❌ Prepare failed: " . $conn->error);
    }
}

// ถ้า form ถูกส่งมา ให้ประมวลผล
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

    // [เพิ่ม] เริ่มต้น Transaction เพื่อความปลอดภัยของข้อมูล
    $conn->begin_transaction();

    try {
        // [แก้ไข] บันทึกข้อมูลลงตาราง orders (เพิ่ม user_id และแก้ชื่อคอลัมน์ total)
        $sql_order = "INSERT INTO orders (user_id, fullname, email, phone, address, payment, total, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql_order);
        check_error($stmt, $conn);
        // "isssssd" คือชนิดข้อมูล: i=integer, s=string, d=double
        $stmt->bind_param("isssssd", $user_id, $fullname, $email, $phone, $address, $payment, $total_price);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // [แก้ไข] บันทึกรายการสินค้าลงตาราง order_items (เปลี่ยนชื่อตารางและคอลัมน์)
        $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_items = $conn->prepare($sql_items);
        check_error($stmt_items, $conn);

        foreach ($cart as $pid => $item) {
            // qty ถูกเปลี่ยนเป็น quantity
            $stmt_items->bind_param("iiid", $order_id, $pid, $item['qty'], $item['price']);
            $stmt_items->execute();

            // อัปเดต stock (ส่วนนี้ถูกต้องแล้ว)
            $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            check_error($update_stock, $conn);
            $update_stock->bind_param("ii", $item['qty'], $pid);
            $update_stock->execute();
            $update_stock->close();
        }
        $stmt_items->close();

        // [เพิ่ม] ถ้าทุกอย่างสำเร็จ ให้ Commit Transaction (ยืนยันการบันทึก)
        $conn->commit();

        // ล้างตะกร้า
        unset($_SESSION['cart']);

        // ไปหน้ายืนยันคำสั่งซื้อ
        header("Location: place_order.php?order_id=" . $order_id);
        exit;

    } catch (Exception $e) {
        // [เพิ่ม] ถ้ามีข้อผิดพลาด ให้ Rollback Transaction (ยกเลิกการบันทึกทั้งหมด)
        $conn->rollback();
        die("❌ เกิดข้อผิดพลาดร้ายแรงในการบันทึกคำสั่งซื้อ: " . $e->getMessage());
    }
}
?>

<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Checkout - The Bookmark Society</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .checkout-card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
    .order-summary { background: #fffdf5; border: 1px solid #f3eac2; border-radius: 10px; }
    .order-summary h5 { border-bottom: 2px solid #e9ecef; padding-bottom: .5rem; margin-bottom: 1rem; }
    .form-label { font-weight: 600; }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="card checkout-card p-4">
        <h3 class="mb-4">📦 ข้อมูลผู้สั่งซื้อ</h3>
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