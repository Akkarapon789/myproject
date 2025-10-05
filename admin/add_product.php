<?php
include '../config/connectdb.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];

    $stmt = $conn->prepare("INSERT INTO products (title, price, stock, category_id, slug) VALUES (?,?,?,?,?)");
    $slug = strtolower(str_replace(" ","-", $title));
    $stmt->bind_param("sdiis", $title, $price, $stock, $category, $slug);
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
  <title>เพิ่มสินค้า</title>
  <?php include 'layout.php'; ?>
</head>
<body>
<div class="container mt-5">
  <h2>เพิ่มสินค้าใหม่</h2>
  <form method="post">
    <div class="mb-3"><label>ชื่อสินค้า</label>
      <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3"><label>ราคา</label>
      <input type="number" step="0.01" name="price" class="form-control" required>
    </div>
    <div class="mb-3"><label>สต็อก</label>
      <input type="number" name="stock" class="form-control" required>
    </div>
    <div class="mb-3"><label>หมวดหมู่</label>
      <select name="category_id" class="form-control">
        <?php while($c=$cats->fetch_assoc()): ?>
          <option value="<?= $c['id']; ?>"><?= $c['title']; ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <button class="btn btn-success">บันทึก</button>
    <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
  </form>
</div>
</body>
</html>
