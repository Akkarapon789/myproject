<?php
include '../config/connectdb.php';
$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];
    $slug = strtolower(str_replace(" ","-", $title));

    $stmt = $conn->prepare("UPDATE products SET title=?, price=?, stock=?, category_id=?, slug=? WHERE id=?");
    $stmt->bind_param("sdiisi", $title, $price, $stock, $category, $slug, $id);
    $stmt->execute();
    header("Location: products.php");
    exit();
}
$cats = $conn->query("SELECT * FROM categories");
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขสินค้า</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="container mt-5">
  <h2>แก้ไขสินค้า</h2>
  <form method="post">
    <div class="mb-3"><label>ชื่อสินค้า</label>
      <input type="text" name="title" class="form-control" value="<?= $product['title']; ?>" required>
    </div>
    <div class="mb-3"><label>ราคา</label>
      <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
    </div>
    <div class="mb-3"><label>สต็อก</label>
      <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
    </div>
    <div class="mb-3"><label>หมวดหมู่</label>
      <select name="category_id" class="form-control">
        <?php while($c=$cats->fetch_assoc()): ?>
          <option value="<?= $c['id']; ?>" <?= $c['id']==$product['category_id']?'selected':''; ?>>
            <?= $c['title']; ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <button class="btn btn-primary">อัพเดต</button>
    <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
  </form>
</div>
</body>
</html>
