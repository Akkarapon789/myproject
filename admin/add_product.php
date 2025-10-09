<?php
session_start();
include '../config/connectdb.php';

// ตรวจสอบว่ามี id ที่จะแก้ไขไหม
if (!isset($_GET['id'])) {
  header("Location: products.php");
  exit();
}
$id = intval($_GET['id']);

// ดึงข้อมูลสินค้า
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$product) {
  die("❌ ไม่พบสินค้านี้ในระบบ");
}

// อัปเดตข้อมูลสินค้า
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];
    $slug = strtolower(str_replace(" ","-", $title));

    // อัปเดตข้อมูลสินค้า
    $stmt = $conn->prepare("UPDATE products SET title=?, price=?, stock=?, category_id=?, slug=? WHERE id=?");
    $stmt->bind_param("sdiisi", $title, $price, $stock, $category, $slug, $id);
    $stmt->execute();

    // 🔹 โฟลเดอร์เก็บรูป
    $uploadsDir = "../assets/product/";
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0775, true);

    // 🔹 เพิ่มรูปใหม่ (ถ้ามี)
    if(isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        for($i=0; $i<count($_FILES['images']['name']); $i++){
            $fileTmp = $_FILES['images']['tmp_name'][$i];
            $fileName = time() . "_" . basename($_FILES['images']['name'][$i]);
            $targetFile = $uploadsDir . $fileName;

            if(move_uploaded_file($fileTmp, $targetFile)){
                $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                $stmt_img->bind_param("is", $id, $fileName);
                $stmt_img->execute();

                // ถ้า product ยังไม่มีรูปหลัก → กำหนดรูปแรกนี้ให้เป็นหลัก
                if (empty($product['image_url'])) {
                    $conn->query("UPDATE products SET image_url='$fileName' WHERE id=$id");
                }
            }
        }
    }

    header("Location: edit_product.php?id=$id&success=1");
    exit();
}

// ดึงข้อมูลหมวดหมู่
$cats = $conn->query("SELECT * FROM categories");
// ดึงรูปภาพของสินค้านี้
$images = $conn->query("SELECT * FROM product_images WHERE product_id=$id");
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขสินค้า</title>
  <?php include 'layout.php'; ?>
  <style>
    .preview-img {
      width: 140px;
      height: 140px;
      object-fit: cover;
      margin: 5px;
      border: 1px solid #ccc;
      border-radius: 10px;
      position: relative;
    }
    .preview-container {
      display: inline-block;
      position: relative;
    }
    .delete-btn {
      position: absolute;
      top: -5px;
      right: -5px;
      background: rgba(255,0,0,0.8);
      color: #fff;
      border: none;
      border-radius: 50%;
      width: 25px;
      height: 25px;
      cursor: pointer;
      font-weight: bold;
    }
    .drop-zone {
      border: 2px dashed #aaa;
      border-radius: 6px;
      padding: 20px;
      text-align: center;
      color: #666;
      cursor: pointer;
      transition: 0.3s;
    }
    .drop-zone.dragover {
      border-color: #2155CD;
      background-color: #f0f8ff;
      color: #2155CD;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2>แก้ไขสินค้า: <?= htmlspecialchars($product['title']); ?></h2>
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ แก้ไขสินค้าสำเร็จ</div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
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
      <select name="category_id" class="form-control" required>
        <?php while($c=$cats->fetch_assoc()): ?>
          <option value="<?= $c['id']; ?>" <?= $c['id']==$product['category_id']?'selected':''; ?>>
            <?= htmlspecialchars($c['title']); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>รูปภาพที่มีอยู่</label><br>
      <div class="d-flex flex-wrap">
        <?php while($img=$images->fetch_assoc()): ?>
          <div class="preview-container" id="img-<?= $img['id']; ?>">
            <img src="../assets/product/<?= $img['image_url']; ?>" class="preview-img">
            <button type="button" class="delete-btn" onclick="deleteImage(<?= $img['id']; ?>)">×</button>
          </div>
        <?php endwhile; ?>
      </div>
    </div>

    <div class="mb-3">
      <label>เพิ่มรูปใหม่ (ลากวางหรือคลิกเลือกได้)</label>
      <div id="drop-zone" class="drop-zone">คลิกหรือลากไฟล์มาวางที่นี่</div>
      <div id="previews" class="d-flex flex-wrap mt-2"></div>
      <input type="file" name="images[]" id="fileInput" accept="image/*" multiple style="display:none;">
    </div>

    <button class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
    <a href="products.php" class="btn btn-secondary">กลับ</a>
  </form>
</div>

<script>
// ✅ ลบรูปภาพด้วย AJAX
function deleteImage(id) {
  if(confirm("คุณแน่ใจหรือไม่ที่จะลบรูปนี้?")) {
    fetch('delete_image.php?id=' + id)
      .then(res => res.text())
      .then(data => {
        if(data.trim() === 'success') {
          document.getElementById('img-' + id).remove();
        } else {
          alert('ไม่สามารถลบรูปได้');
        }
      });
  }
}

// ✅ Drag & Drop Upload
const dropZone = document.getElementById('drop-zone');
const previews = document.getElementById('previews');
const fileInput = document.getElementById('fileInput');
dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', e => {
  e.preventDefault(); dropZone.classList.add('dragover');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
  e.preventDefault(); dropZone.classList.remove('dragover');
  fileInput.files = e.dataTransfer.files;
  updatePreviews(e.dataTransfer.files);
});
fileInput.addEventListener('change', () => updatePreviews(fileInput.files));

function updatePreviews(files) {
  previews.innerHTML = "";
  for (let file of files) {
    if (!file.type.startsWith('image/')) continue;
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.className = 'preview-img';
      previews.appendChild(img);
    }
    reader.readAsDataURL(file);
  }
}
</script>
</body>
</html>
