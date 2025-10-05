<?php
include '../config/connectdb.php';
$id = $_GET['id'];
$order = $conn->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();
$details = $conn->query("SELECT od.*, p.title 
                         FROM order_detail od 
                         JOIN products p ON od.product_id = p.id 
                         WHERE od.order_id=$id");
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายละเอียดคำสั่งซื้อ</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="container mt-5">
  <h2>รายละเอียดคำสั่งซื้อ #<?= $order['id']; ?></h2>
  <p>ชื่อลูกค้า: <?= htmlspecialchars($order['fullname']); ?></p>
  <p>Email: <?= htmlspecialchars($order['email']); ?></p>
  <p>เบอร์: <?= htmlspecialchars($order['phone']); ?></p>
  <p>ที่อยู่: <?= htmlspecialchars($order['address']); ?></p>
  <p>ยอดรวม: <?= number_format($order['total'],2); ?> บาท</p>

  <h4 class="mt-4">สินค้าในคำสั่งซื้อ</h4>
  <table class="table table-bordered">
    <tr><th>สินค้า</th><th>จำนวน</th><th>ราคา</th></tr>
    <?php while($d=$details->fetch_assoc()): ?>
      <tr>
        <td><?= $d['title']; ?></td>
        <td><?= $d['qty']; ?></td>
        <td><?= number_format($d['price'],2); ?> บาท</td>
      </tr>
    <?php endwhile; ?>
  </table>

  <a href="orders.php" class="btn btn-secondary">กลับ</a>
</div>
</body>
</html>
