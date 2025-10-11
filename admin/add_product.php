<?php
// เริ่ม Session เพื่อใช้ในการเก็บข้อความแจ้งเตือน (Success/Error)
session_start();

// 1. เชื่อมต่อฐานข้อมูล (ตรวจสอบให้แน่ใจว่า path นี้ถูกต้อง)
include '../config/connectdb.php';

// 2. ตรวจสอบว่าเป็นการส่งข้อมูลแบบ POST (กดปุ่มบันทึก)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // รับค่าจากฟอร์ม
    $category_id = $_POST['category_id'] ?? '';
    $title       = $_POST['title']       ?? '';
    $price       = $_POST['price']       ?? 0;
    $stock       = $_POST['stock']       ?? 0;
    
    // สร้าง Slug อัตโนมัติ: แปลงเป็นตัวพิมพ์เล็ก และแทนที่ช่องว่างด้วยขีด (-)
    $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    // 3. จัดการการอัปโหลดรูปภาพ
    $image_url = null; // กำหนดค่าเริ่มต้นเป็น null

    // ตรวจสอบว่ามีไฟล์ถูกส่งมา และไม่มี error
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/"; // โฟลเดอร์สำหรับเก็บไฟล์ภาพ

        // ตรวจสอบว่ามีโฟลเดอร์ uploads หรือยัง ถ้ายังไม่มีให้สร้างขึ้นมา
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0775, true); // 0775 เป็น permission ที่ปลอดภัยกว่า
        }

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // ตรวจสอบชนิดของไฟล์ที่อนุญาต
        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowTypes)) {
            // พยายามย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $image_url =   $fileName; // หากสำเร็จ กำหนด path ของรูป
            } else {
                // หากย้ายไฟล์ไม่สำเร็จ
                $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ภาพ! (อาจเกี่ยวกับ Folder Permissions)";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        } else {
            // หากไฟล์ที่อัปโหลดไม่ตรงกับชนิดที่อนุญาต
            $_SESSION['error'] = "อนุญาตเฉพาะไฟล์ภาพนามสกุล JPG, JPEG, PNG และ GIF เท่านั้น";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // 4. บันทึกข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO products (category_id, title, slug, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // "issdis" คือชนิดข้อมูล: i=integer, s=string, d=double
    $stmt->bind_param("issdis", $category_id, $title, $slug, $price, $stock, $image_url);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มสินค้า '$title' สำเร็จแล้ว!";
        header("Location: products.php"); // ไปยังหน้าแสดงรายการสินค้า
        exit;
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// 5. ดึงข้อมูลหมวดหมู่ทั้งหมดสำหรับ Dropdown
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS เพิ่มเติมเล็กน้อยเพื่อความสวยงาม */
        .form-container {
            max-width: 800px;
            margin: auto;
        }
        .btn-group-custom {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="form-container">
        <h2 class="mb-4 text-center">เพิ่มสินค้าใหม่</h2>

        <?php // แสดงข้อความแจ้งเตือน ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
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

            <div class="btn-group-custom">
                <button type="submit" class="btn btn-primary w-100">บันทึกสินค้า</button>
                <a href="products.php" class="btn btn-secondary w-100">ยกเลิก</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>