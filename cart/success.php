<?php
// success.php
require_once '../config/connectdb.php';

// ดึง order_id จาก URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;
$items = [];

if ($order_id > 0) {
    // 🔹 ดึงข้อมูลคำสั่งซื้อหลัก
    $sql = "SELECT fullname, total_price, created_at FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    // 🔹 ดึงรายการสินค้าภายในการสั่งซื้อนี้
    $sql_items = "SELECT p.title, od.qty, od.price 
                  FROM order_detail od
                  JOIN products p ON od.product_id = p.id
                  WHERE od.order_id = ?";
    $stmt2 = $conn->prepare($sql_items);
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $items = $result2->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สั่งซื้อสำเร็จ - The Bookmark Society</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .success-card {
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      padding: 50px 30px;
      max-width: 700px;
      margin: 80px auto;
    }
    .emoji {
      font-size: 4rem;
      animation: pop 0.4s ease-in-out;
    }
    @keyframes pop {
      0% { transform: scale(0.5); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
    .btn-primary {
      background-color: #2155CD;
      border: none;
      font-weight: 500;
    }
    .btn-primary:hover {
      background-color: #173b96;
    }
    .order-items {
      background: #fffdf5;
      border: 1px solid #f3eac2;
      border-radius: 10px;
      margin-top: 1.5rem;
    }
    .order-items h5 {
      background-color: #fdf7d0;
      padding: 10px 15px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      margin-bottom: 0;
      font-weight: 600;
    }
  </style>
</head>
<body class="bg-light">

  <div class="container">
    <div class="success-card text-center">
      <div class="emoji mb-3">✅</div>
      <h1 class="text-success mb-3">สั่งซื้อสำเร็จ!</h1>
      <p class="text-muted mb-4">ขอบคุณที่ใช้บริการกับเรา ระบบได้รับคำสั่งซื้อของคุณเรียบร้อยแล้ว</p>

      <?php if ($order): ?>
        <div class="border rounded p-3 bg-light mb-4 text-start">
          <p><strong>หมายเลขคำสั่งซื้อ:</strong> #<?= htmlspecialchars($order_id) ?></p>
          <p><strong>ชื่อผู้สั่งซื้อ:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
          <p><strong>ยอดรวมทั้งหมด:</strong> ฿<?= number_format($order['total_price'], 2) ?></p>
          <p><strong>วันที่สั่งซื้อ:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>

        <!-- 🔹 รายการสินค้าที่สั่งซื้อ -->
        <?php if (!empty($items)): ?>
          <div class="order-items text-start">
            <h5>รายการสินค้าที่สั่งซื้อ</h5>
            <div class="p-3">
              <table class="table table-borderless align-middle mb-0">
                <thead>
                  <tr class="border-bottom">
                    <th>ชื่อสินค้า</th>
                    <th class="text-center">จำนวน</th>
                    <th class="text-end">ราคา/ชิ้น</th>
                    <th class="text-end">ราคารวม</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($items as $it): 
                    $sum = $it['qty'] * $it['price']; ?>
                    <tr>
                      <td><?= htmlspecialchars($it['title']) ?></td>
                      <td class="text-center"><?= $it['qty'] ?></td>
                      <td class="text-end">฿<?= number_format($it['price'], 2) ?></td>
                      <td class="text-end">฿<?= number_format($sum, 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <div class="alert alert-warning">ไม่พบข้อมูลคำสั่งซื้อในระบบ</div>
      <?php endif; ?>

      <a href="../pages/index.php" class="btn btn-primary btn-lg px-5 mt-4">กลับไปหน้าหลัก</a>
    </div>
  </div>

</body>
</html>
