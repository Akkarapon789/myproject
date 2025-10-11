<?php
session_start();
include '../config/connectdb.php'; // ✅ เชื่อมต่อฐานข้อมูล

// ✅ ตรวจสอบว่ามีการกดปุ่มบันทึกหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? '';
    $title       = $_POST['title'] ?? '';
    $slug        = strtolower(str_replace(' ', '-', $title)); // สร้าง slug อัตโนมัติ
    $price       = $_POST['price'] ?? 0;
    $stock       = $_POST['stock'] ?? 0;

    // ✅ จัดการอัปโหลดภาพ
    $image_url = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $image_url = "uploads/" . $fileName;
            }
        }
    }

    // ✅ บันทึกข้อมูลสินค้า
    $sql = "INSERT INTO products (category_id, title, slug, price, stock, image_url)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdis", $category_id, $title, $slug, $price, $stock, $image_url);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มสินค้าสำเร็จแล้ว!";
        header("Location: products.php");
        exit;
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ✅ ดึงข้อมูลหมวดหมู่ทั้งหมดมาแสดงใน dropdown
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">เพิ่มสินค้าใหม่</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">หมวดหมู่สินค้า</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['title']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">ชื่อสินค้า</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">ราคา</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">จำนวนคงเหลือ (Stock)</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">เลือกรูปภาพสินค้า</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
        <a href="products.php" class="btn btn-secondary">กลับ</a>
    </form>
</div>

</body>
</html>