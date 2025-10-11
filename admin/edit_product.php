<?php
session_start();
include '../config/connectdb.php';

// ✅ ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = intval($_GET['id']);

// ✅ ดึงข้อมูลสินค้าปัจจุบัน
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>ไม่พบข้อมูลสินค้า</p>";
    exit();
}

// ✅ เมื่อมีการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title'] ?? '';
    $price       = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $description = $_POST['description'] ?? '';

    $imageName = $product['image']; // ใช้รูปเดิมก่อน

    // ✅ ตรวจสอบว่ามีการอัปโหลดรูปใหม่ไหม
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageTmp  = $_FILES['image']['tmp_name'];
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;

        // ✅ ลบรูปเดิม (ถ้ามี)
        if (!empty($product['image']) && file_exists($targetDir . $product['image'])) {
            unlink($targetDir . $product['image']);
        }

        // ✅ บันทึกรูปใหม่
        move_uploaded_file($imageTmp, $targetFile);
    }

    // ✅ อัปเดตข้อมูลในฐานข้อมูล
    $update = $conn->prepare("UPDATE products 
                              SET title=?, price=?, category_id=?, description=?, image=? 
                              WHERE id=?");
    $update->execute([$title, $price, $category_id, $description, $imageName, $id]);

    $_SESSION['success'] = "อัปเดตสินค้าสำเร็จ!";
    header("Location: products.php");
    exit();
}

// ✅ ดึงข้อมูลหมวดหมู่สำหรับ dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🛠️ แก้ไขสินค้า</h4>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($product['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">ราคา</label>
                    <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">หมวดหมู่</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">รายละเอียดสินค้า</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">รูปภาพปัจจุบัน</label><br>
                    <?php if (!empty($product['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="product" width="150" class="rounded mb-2 border">
                    <?php else: ?>
                        <p class="text-muted">ไม่มีรูปภาพ</p>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">อัปโหลดรูปใหม่ (ถ้ามี)</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="text-end">
                    <a href="products.php" class="btn btn-secondary">กลับ</a>
                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>