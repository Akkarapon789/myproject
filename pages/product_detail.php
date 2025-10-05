<?php
include '../config/connectdb.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ดึงข้อมูลสินค้า
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
  echo "<h2>ไม่พบสินค้านี้</h2>";
  exit;
}
$product = $result->fetch_assoc();

// ✅ บันทึกรีวิวใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['user_name'])) {
  $user_name = $conn->real_escape_string($_POST['user_name']);
  $rating = intval($_POST['rating']);
  $comment = $conn->real_escape_string($_POST['comment']);

  $conn->query("INSERT INTO reviews (product_id, user_name, rating, comment)
                VALUES ($product_id, '$user_name', $rating, '$comment')");
}

// ✅ ดึงรีวิวทั้งหมดของสินค้านี้
$review_sql = "SELECT * FROM reviews WHERE product_id = $product_id ORDER BY created_at DESC";
$reviews = $conn->query($review_sql);

// ✅ ดึงสินค้าที่คล้ายกัน
$related_sql = "SELECT * FROM products WHERE category_id = {$product['category_id']} AND id != {$product['id']} LIMIT 4";
$related_products = $conn->query($related_sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($product['name']); ?> | ร้านหนังสือออนไลน์</title>
<style>
  body { font-family: sans-serif; background: #f9f9f9; margin: 0; }
  .container { width: 90%; max-width: 1200px; margin: 40px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
  .detail { display: flex; flex-wrap: wrap; gap: 30px; }
  .product-img img { width: 100%; max-width: 350px; border-radius: 10px; }
  .product-info { flex: 1; min-width: 300px; }
  .product-title { font-size: 1.8rem; font-weight: bold; color: #333; }
  .price { font-size: 1.5rem; color: #e74c3c; margin-top: 15px; }
  .old-price { text-decoration: line-through; color: #999; margin-left: 10px; font-size: 1rem; }
  .desc { margin-top: 20px; line-height: 1.6; color: #444; }
  .btn-group { margin-top: 25px; display: flex; flex-wrap: wrap; gap: 10px; }
  .btn { padding: 12px 20px; border-radius: 6px; text-decoration: none; color: white; font-weight: bold; text-align: center; flex: 1; }
  .btn-cart { background: #007bff; }
  .btn-buy { background: #28a745; }
  .btn-wishlist { background: #ff9800; }
  .btn-share { background: #6c757d; }
  .btn:hover { opacity: 0.9; }

  /* หนังสือที่คล้ายกัน */
  .related-section { margin-top: 50px; }
  .related-title { font-size: 1.5rem; margin-bottom: 15px; color: #333; }
  .related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
  .related-item { background: #fff; border-radius: 8px; padding: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; }
  .related-item:hover { transform: scale(1.03); }
  .related-item img { width: 100%; height: 240px; object-fit: cover; border-radius: 6px; }

  /* รีวิว */
  .review-section { margin-top: 50px; }
  .review-section h3 { font-size: 1.4rem; margin-bottom: 15px; }
  .review-card { background: #f1f1f1; border-radius: 8px; padding: 15px; margin-bottom: 10px; }
  .review-header { display: flex; justify-content: space-between; }
  .review-name { font-weight: bold; color: #333; }
  .review-date { color: #888; font-size: 0.9rem; }
  .review-stars { color: gold; margin: 5px 0; }
  .review-form { background: #fafafa; border-radius: 10px; padding: 20px; margin-top: 20px; }
  .review-form input, .review-form textarea, .review-form select { width: 100%; margin-top: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
  .review-form button { margin-top: 10px; padding: 12px 25px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; }
  .review-form button:hover { background: #218838; }

  @media (max-width: 768px) {
    .detail { flex-direction: column; align-items: center; }
    .btn-group { flex-direction: column; }
  }
</style>
</head>
<body>

<div class="container">
  <div class="detail">
    <div class="product-img">
      <img src="../assets/img/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
    </div>

    <div class="product-info">
      <div class="product-title"><?= htmlspecialchars($product['name']); ?></div>
      <div>ผู้เขียน: <?= htmlspecialchars($product['author']); ?></div>
      <div>สำนักพิมพ์: <?= htmlspecialchars($product['publisher']); ?></div>

      <div class="price">
        ฿<?= number_format($product['price'] - $product['discount'], 2); ?>
        <?php if ($product['discount'] > 0): ?>
          <span class="old-price">฿<?= number_format($product['price'], 2); ?></span>
        <?php endif; ?>
      </div>

      <div class="desc"><?= nl2br(htmlspecialchars($product['description'])); ?></div>

      <div class="btn-group">
        <a href="cart_add.php?id=<?= $product['id']; ?>" class="btn btn-cart">🛒 เพิ่มลงตะกร้า</a>
        <a href="checkout.php?id=<?= $product['id']; ?>" class="btn btn-buy">💳 ซื้อเลย</a>
        <a href="wishlist_add.php?id=<?= $product['id']; ?>" class="btn btn-wishlist">❤️ Wishlist</a>
        <a href="javascript:void(0);" onclick="shareProduct()" class="btn btn-share">🔗 แชร์</a>
      </div>
    </div>
  </div>

  <!-- 🔹 ส่วนรีวิวสินค้า -->
  <div class="review-section">
    <h3>⭐ รีวิวจากผู้อ่าน</h3>
    <?php if ($reviews->num_rows > 0): ?>
      <?php while($r = $reviews->fetch_assoc()): ?>
        <div class="review-card">
          <div class="review-header">
            <div class="review-name"><?= htmlspecialchars($r['user_name']); ?></div>
            <div class="review-date"><?= date('d/m/Y', strtotime($r['created_at'])); ?></div>
          </div>
          <div class="review-stars"><?= str_repeat('⭐', $r['rating']); ?></div>
          <div class="review-comment"><?= nl2br(htmlspecialchars($r['comment'])); ?></div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>ยังไม่มีรีวิวสำหรับสินค้านี้</p>
    <?php endif; ?>

    <div class="review-form">
      <h4>📝 เขียนรีวิวของคุณ</h4>
      <form method="post">
        <label>ชื่อของคุณ</label>
        <input type="text" name="user_name" required>

        <label>ให้คะแนน</label>
        <select name="rating" required>
          <option value="">-- เลือกคะแนน --</option>
          <option value="5">⭐⭐⭐⭐⭐ (5)</option>
          <option value="4">⭐⭐⭐⭐ (4)</option>
          <option value="3">⭐⭐⭐ (3)</option>
          <option value="2">⭐⭐ (2)</option>
          <option value="1">⭐ (1)</option>
        </select>

        <label>ความคิดเห็น</label>
        <textarea name="comment" rows="4" placeholder="บอกความรู้สึกหลังอ่าน..." required></textarea>

        <button type="submit">ส่งรีวิว</button>
      </form>
    </div>
  </div>

  <!-- 🔹 หนังสือที่คล้ายกัน -->
  <div class="related-section">
    <div class="related-title">📚 หนังสือที่คล้ายกัน</div>
    <div class="related-grid">
      <?php while($r = $related_products->fetch_assoc()): ?>
        <div class="related-item">
          <a href="product_detail.php?id=<?= $r['id']; ?>">
            <img src="../assets/img/<?= htmlspecialchars($r['image']); ?>" alt="<?= htmlspecialchars($r['name']); ?>">
            <h4><?= htmlspecialchars($r['name']); ?></h4>
            <p>฿<?= number_format($r['price'] - $r['discount'], 2); ?></p>
          </a>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<script>
function shareProduct() {
  const url = window.location.href;
  if (navigator.share) {
    navigator.share({ title: document.title, url });
  } else {
    navigator.clipboard.writeText(url);
    alert("คัดลอกลิงก์แล้ว!");
  }
}
</script>
</body>
</html>
