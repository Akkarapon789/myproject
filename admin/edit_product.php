<?php
include '../config/connectdb.php';

// ✅ รับ ID และดึงข้อมูลสินค้า
$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];
    $slug = strtolower(str_replace(" ", "-", $title));

    // ✅ ส่วนอัปโหลดหลายรูป
    $imagePaths = [];

    // ถ้ามีรูปเก่าใน DB
    if (!empty($product['image_url'])) {
        $imagePaths = explode(",", $product['image_url']);
    }

    // ถ้ามีการอัปโหลดรูปใหม่
    if (!empty($_FILES['images']['name'][0])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        foreach ($_FILES['images']['name'] as $key => $name) {
            $fileTmp = $_FILES['images']['tmp_name'][$key];
            $fileName = time() . "_" . basename($name);
            $targetFile = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if (in_array($fileType, ['jpg','jpeg','png','gif','webp'])) {
                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $imagePaths[] = str_replace("../", "", $targetFile);
                }
            }
        }
    }

    // ✅ รวมชื่อรูปทั้งหมดเก็บในคอลัมน์เดียว
    $imagePathStr = implode(",", $imagePaths);

    // ✅ อัปเดตข้อมูลสินค้า
    $stmt = $conn->prepare("UPDATE products SET title=?, price=?, stock=?, category_id=?, slug=?, image_url=? WHERE id=?");
    $stmt->bind_param("sdiissi", $title, $price, $stock, $category, $slug, $imagePathStr, $id);
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
  <h2>🖋️ แก้ไขสินค้า</h2>

  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label>ชื่อสินค้า</label>
      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($product['title']); ?>" required>
    </div>

    <div class="mb-3">
      <label>ราคา</label>
      <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
    </div>

    <div class="mb-3">
      <label>สต็อก</label>
      <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
    </div>

    <div class="mb-3">
      <label>หมวดหมู่</label>
      <select name="category_id" class="form-select">
        <?php while($c = $cats->fetch_assoc()): ?>
          <option value="<?= $c['id']; ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : ''; ?>>
            <?= htmlspecialchars($c['title']); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- ✅ ส่วนอัปโหลดหลายรูป -->
    <div class="mb-3">
      <label>📸 รูปสินค้า (สามารถเลือกได้หลายรูป)</label>
      <input type="file" name="images[]" class="form-control" accept="image/*" multiple>

      <?php if (!empty($product['image_url'])): 
          $images = explode(",", $product['image_url']); ?>
        <div class="mt-3 d-flex flex-wrap gap-3">
          <?php foreach($images as $img): ?>
            <div class="border rounded p-2 text-center" style="width:120px;">
              <img src="../<?= htmlspecialchars($img); ?>" class="img-fluid mb-1" style="max-height:100px; object-fit:cover;">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <button class="btn btn-primary">💾 อัปเดตสินค้า</button>
    <a href="products.php" class="btn btn-secondary">ย้อนกลับ</a>
  </form>
</div>
</body>
</html>
