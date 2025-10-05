<?php
include '../config/connectdb.php';

// ดึง id จาก URL เช่น product_detail.php?id=5
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

// ตรวจสอบว่ามีสินค้าหรือไม่
if ($result->num_rows > 0) {
  $book = $result->fetch_assoc();
} else {
  echo "<h2>ไม่พบสินค้านี้</h2>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($book['title']); ?> | The Bookmark Society </title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { font-family: sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
    .container { width: 80%; margin: 40px auto; display: flex; gap: 30px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .book-img img { width: 300px; border-radius: 8px; }
    .book-info { flex: 1; }
    .book-title { font-size: 1.8rem; font-weight: bold; color: #333; }
    .book-author { color: #666; margin-top: 5px; }
    .price { font-size: 1.5rem; color: #e74c3c; margin-top: 15px; }
    .old-price { text-decoration: line-through; color: #999; margin-left: 10px; }
    .desc { margin-top: 20px; line-height: 1.6; color: #444; }
    .buy-btn { display: inline-block; margin-top: 25px; padding: 12px 25px; background: #007bff; color: #fff; border-radius: 6px; text-decoration: none; }
    .buy-btn:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="container">
    <div class="product-img">
      <img src="../assets/img/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($book['title']); ?>">
    </div>
    <div class="product-info">
      <div class="product-title"><?= htmlspecialchars($product['title']); ?></div>
      <div class="product-author">โดย <?= htmlspecialchars($product['author']); ?></div>
      <div class="product-publisher">สำนักพิมพ์: <?= htmlspecialchars($product['publisher']); ?></div>
      
      <div class="price">
        ฿<?= number_format($product['price'] - $product['discount'], 2); ?>
        <?php if ($product['discount'] > 0): ?>
          <span class="old-price">฿<?= number_format($product['price'], 2); ?></span>
        <?php endif; ?>
      </div>
      
      <div class="desc"><?= nl2br(htmlspecialchars($product['description'])); ?></div>

      <a href="../cart/add.php?id=<?= $product['id']; ?>" class="buy-btn">เพิ่มลงตะกร้า</a>
    </div>
  </div>
</body>
</html>
