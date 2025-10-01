<?php
session_start();
$cart = $_SESSION['cart'] ?? [];

// Popup แจ้งเตือนเมื่อมีการเพิ่มสินค้า
$added_product = $_SESSION['added_product'] ?? null;
unset($_SESSION['added_product']); // แสดงครั้งเดียว

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
  <title>ตะกร้าสินค้า</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h1 class="mb-4">🛒 ตะกร้าสินค้า</h1>

  <?php if (empty($cart)): ?>
    <div class="alert alert-warning">ยังไม่มีสินค้าในตะกร้า</div>
    <a href="../pages/index.php" class="btn btn-primary">เลือกซื้อหนังสือเพิ่มเติม</a>
  <?php else: ?>
    <table class="table table-bordered bg-white shadow-sm">
      <thead class="table-dark">
        <tr>
          <th>สินค้า</th>
          <th>ราคา (บาท)</th>
          <th>จำนวน</th>
          <th>รวม</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart as $id => $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['title']) ?></td>
          <td><?= number_format($item['price'], 2) ?></td>
          <td>
            <div class="d-flex align-items-center">
              <a href="update.php?action=decrease&id=<?= $id ?>" class="btn btn-sm btn-outline-secondary me-2">-</a>
              <span><?= $item['qty'] ?></span>
              <a href="update.php?action=increase&id=<?= $id ?>" class="btn btn-sm btn-outline-secondary ms-2">+</a>
            </div>
          </td>
          <td><?= number_format($item['price'] * $item['qty'], 2) ?></td>
          <td>
            <a href="update.php?action=remove&id=<?= $id ?>" class="btn btn-sm btn-danger">ลบ</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="text-end">
      <h4>ราคารวมทั้งหมด: <?= number_format($total_price, 2) ?> บาท</h4>
      <a href="checkout.php" class="btn btn-success btn-lg mt-3">สั่งซื้อสินค้า</a>
    </div>
  <?php endif; ?>
</div>

<?php if ($added_product): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'เพิ่มลงตะกร้าแล้ว',
    text: '<?= htmlspecialchars($added_product) ?> ถูกเพิ่มในตะกร้าของคุณ',
    showConfirmButton: false,
    timer: 2000
  });
</script>
<?php endif; ?>

</body>
</html>
