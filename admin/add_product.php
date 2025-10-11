<?php
// add_product.php (Corrected)
session_start();
include '../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? '';
    $title       = $_POST['title']       ?? '';
    $price       = $_POST['price']       ?? 0;
    $stock       = $_POST['stock']       ?? 0;
    $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $image_name  = null; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // [แก้ไข] บันทึกแค่ชื่อไฟล์ ไม่ต้องมี "uploads/"
                $image_name = $fileName;
            } else {
                $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ภาพ!";
                header("Location: add_product.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "อนุญาตเฉพาะไฟล์ภาพนามสกุล JPG, JPEG, PNG และ GIF เท่านั้น";
            header("Location: add_product.php");
            exit;
        }
    }

    $sql = "INSERT INTO products (category_id, title, slug, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdis", $category_id, $title, $slug, $price, $stock, $image_name);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มสินค้า '$title' สำเร็จแล้ว!";
        header("Location: products.php");
        exit;
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        header("Location: add_product.php");
        exit;
    }
}

$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div style="max-width: 800px; margin: auto;">
            <h2 class="mb-4 text-center">เพิ่มสินค้าใหม่</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
                <div class="mb-3">
                    <label for="category_id" class="form-label">หมวดหมู่สินค้า <span class="text-danger">*</span></label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="" disabled selected>-- กรุณาเลือกหมวดหมู่ --</option>
                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="title" class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">ราคา <span class="text-danger">*</span></label>
                        <input type="number" id="price" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label">จำนวนคงเหลือ (Stock) <span class="text-danger">*</span></label>
                        <input type="number" id="stock" name="stock" class="form-control" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="image" class="form-label">เลือกรูปภาพสินค้า</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/png, image/jpeg, image/gif">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">บันทึกสินค้า</button>
                    <a href="products.php" class="btn btn-secondary w-100">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>