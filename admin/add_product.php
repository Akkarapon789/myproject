<?php
include '../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];
    $images = $_POST['images'] ?? []; // จะเป็น base64 string ของแต่ละรูป

    // insert product
    $stmt = $conn->prepare("INSERT INTO products (title, price, stock, category_id, slug) VALUES (?,?,?,?,?)");
    $slug = strtolower(str_replace(" ","-", $title));
    $stmt->bind_param("sdiis", $title, $price, $stock, $category, $slug);
    $stmt->execute();
    $product_id = $stmt->insert_id;

    // insert product images
    $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
    foreach($images as $img) {
        if(!empty($img)) {
            $stmt_img->bind_param("is", $product_id, $img);
            $stmt_img->execute();
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

    <!-- Drag & Drop Zone -->
    <div class="mb-3">
      <label>รูปภาพสินค้า (ลากวาง หรือ คลิกเพื่อเลือก)</label>
      <div id="drop-zone" class="drop-zone">คลิกหรือลากไฟล์ภาพมาที่นี่</div>
      <div id="previews" class="d-flex flex-wrap mt-2"></div>
    </div>

    <button class="btn btn-success">บันทึก</button>
    <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
  </form>
</div>

<script>
const dropZone = document.getElementById('drop-zone');
const previews = document.getElementById('previews');
const form = document.querySelector('form');

let images = []; // เก็บ base64 ของแต่ละรูป

dropZone.addEventListener('click', () => {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    fileInput.multiple = true;
    fileInput.onchange = () => handleFiles(fileInput.files);
    fileInput.click();
});

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', (e) => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    handleFiles(e.dataTransfer.files);
});

function handleFiles(files) {
    for (let file of files) {
        if (!file.type.startsWith('image/')) continue;

        const reader = new FileReader();
        reader.onload = (e) => {
            const base64 = e.target.result;
            images.push(base64);

            // สร้าง preview
            const img = document.createElement('img');
            img.src = base64;
            img.className = 'preview-img';
            previews.appendChild(img);

            // สร้าง hidden input สำหรับส่ง form
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'images[]';
            input.value = base64;
            form.appendChild(input);
        }
        reader.readAsDataURL(file);
    }
}
</script>
</body>
</html>