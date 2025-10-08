<?php
include '../config/connectdb.php';
$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];
    $slug = strtolower(str_replace(" ", "-", $title));
    $image = $product['image']; // เก็บชื่อรูปเก่าไว้ก่อน

    // 📸 ถ้ามีการอัปโหลดรูปใหม่
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // ตรวจสอบไฟล์ว่าเป็นรูป
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // ลบรูปเก่าออกถ้ามี
                if (!empty($product['image']) && file_exists("../uploads/" . $product['image'])) {
                    unlink("../uploads/" . $product['image']);
                }
                $image = $fileName;
            }
        }
    }

    // ✅ อัปเดตฐานข้อมูล
    $stmt = $conn->prepare("UPDATE products SET title=?, price=?, stock=?, category_id=?, slug=?, image=? WHERE id=?");
    $stmt->bind_param("sdiissi", $title, $price, $stock, $category, $slug, $image, $id);
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
  <div class="card shadow-sm p-4">
    <h2 class="mb-4">🛠 แก้ไขสินค้า</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">ชื่อสินค้า</label>
          <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($product['title']); ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">ราคา (บาท)</label>
          <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">สต็อก</label>
          <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-bold">หมวดหมู่</label>
          <select name="category_id" class="form-select">
            <?php while($c=$cats->fetch_assoc()): ?>
              <option value="<?= $c['id']; ?>" <?= $c['id']==$product['category_id']?'selected':''; ?>>
                <?= htmlspecialchars($c['title']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- 📸 แสดงและอัปโหลดรูปสินค้า -->
        <div class="col-md-6">
          <label class="form-label fw-bold">รูปสินค้า</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <div class="mt-3">
            <?php if (!empty($product['image']) && file_exists("../uploads/" . $product['image'])): ?>
              <img src="../uploads/<?= htmlspecialchars($product['image']); ?>" alt="product" class="img-thumbnail" width="150">
            <?php else: ?>
              <img src="https://picsum.photos/150?random=<?= $product['id']; ?>" class="img-thumbnail" alt="default">
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button class="btn btn-primary">💾 บันทึกการเปลี่ยนแปลง</button>
        <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
