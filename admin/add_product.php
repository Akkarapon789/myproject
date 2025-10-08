<?php
include '../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];

    // insert product
    $stmt = $conn->prepare("INSERT INTO products (title, price, stock, category_id, slug) VALUES (?,?,?,?,?)");
    $slug = strtolower(str_replace(" ","-", $title));
    $stmt->bind_param("sdiis", $title, $price, $stock, $category, $slug);
    $stmt->execute();
    $product_id = $stmt->insert_id;

    // อัพโหลดรูปจริง
    if(isset($_FILES['images'])) {
        $uploadsDir = "../uploads/";
        $stmt_img = $conn->prepare("INSERT INTO products (title, price, stock, category_id, slug, image_url) VALUES (?, ?, ?, ?, ?, ?)");

        foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = time() . "_" . basename($_FILES['images']['name'][$key]);
            $targetFile = $uploadsDir . $fileName;

            if(move_uploaded_file($tmp_name, $targetFile)) {
                $stmt_img->bind_param("is", $product_id, $fileName);
                $stmt_img->execute();
            }
        }
    }

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
  <style>
    .preview-img {
        max-width: 150px;
        max-height: 150px;
        margin: 5px;
        border: 1px solid #ccc;
        object-fit: cover;
    }
    .drop-zone {
        border: 2px dashed #aaa;
        border-radius: 6px;
        padding: 20px;
        text-align: center;
        color: #666;
        cursor: pointer;
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
  <h2>เพิ่มสินค้าใหม่</h2>
  <!-- เพิ่ม enctype="multipart/form-data" เพื่อรองรับการอัพโหลดไฟล์ -->
  <form method="post" enctype="multipart/form-data">
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

    <!-- Drag & Drop Zone -->
    <div class="mb-3">
      <label>รูปภาพสินค้า (ลากวาง หรือ คลิกเพื่อเลือก)</label>
      <div id="drop-zone" class="drop-zone">คลิกหรือลากไฟล์ภาพมาที่นี่</div>
      <div id="previews" class="d-flex flex-wrap mt-2"></div>
      <!-- input type file จริง -->
      <input type="file" name="images[]" id="fileInput" accept="image/*" multiple style="display:none;">
    </div>

    <button class="btn btn-success">บันทึก</button>
    <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
  </form>
</div>

<script>
const dropZone = document.getElementById('drop-zone');
const previews = document.getElementById('previews');
const fileInput = document.getElementById('fileInput');

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});
dropZone.addEventListener('dragleave', e => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    fileInput.files = e.dataTransfer.files; // กำหนดให้ input รับไฟล์ที่ลากมาลง
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
