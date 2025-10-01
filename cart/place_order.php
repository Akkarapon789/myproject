<?php
session_start();
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: checkout.php");
    exit();
}

// รับข้อมูลจาก checkout.php
$fullname = $_POST['fullname'] ?? '';
$email    = $_POST['email'] ?? '';
$phone    = $_POST['phone'] ?? '';
$address  = $_POST['address'] ?? '';
$payment  = $_POST['payment'] ?? '';

// คำนวณราคารวม
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['qty'];
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ยืนยันการสั่งซื้อ - The Bookmark Society</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .card-custom {
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .btn-confirm {
      background-color: #2155CD;
      color: white;
      border-radius: 25px;
      padding: 10px 20px;
    }
    .btn-confirm:hover {
      background-color: #1741a0;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card card-custom p-4">
        <h3 class="text-center mb-4">📦 สรุปการสั่งซื้อ</h3>

        <!-- ข้อมูลลูกค้า -->
        <div class="mb-4">
          <h5>ข้อมูลผู้สั่งซื้อ</h5>
          <p><strong>ชื่อ:</strong> <?= htmlspecialchars($fullname) ?></p>
          <p><strong>อีเมล:</strong> <?= htmlspecialchars($email) ?></p>
          <p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($phone) ?></p>
          <p><strong>ที่อยู่:</strong> <?= nl2br(htmlspecialchars($address)) ?></p>
          <p><strong>วิธีการชำระเงิน:</strong> 
            <?php
              $payment_text = [
                "cod" => "เก็บเงินปลายทาง (COD)",
                "bank" => "โอนผ่านธนาคาร",
                "credit" => "บัตรเครดิต/เดบิต"
              ];
              echo $payment_text[$payment] ?? "ไม่ระบุ";
            ?>
          </p>
        </div>

        <!-- สรุปสินค้า -->
        <div class="mb-4">
          <h5>รายการสินค้า</h5>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>สินค้า</th>
                <th class="text-center">จำนวน</th>
                <th class="text-end">ราคา</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart as $item): ?>
                <tr>
                  <td><?= htmlspecialchars($item['title']) ?></td>
                  <td class="text-center"><?= $item['qty'] ?></td>
                  <td class="text-end">฿<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="2" class="text-end">ยอดรวมทั้งหมด</th>
                <th class="text-end text-success">฿<?= number_format($total_price, 2) ?></th>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="text-center">
          <a href="loading-car.php" class="btn btn-confirm btn-lg">
                ยืนยันการสั่งซื้อ
          </a>
          <a href="checkout.php" class="btn btn-outline-secondary btn-lg ms-2">
                กลับไปแก้ไข
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

</body>
</html>