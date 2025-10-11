<?php
session_start();
include '../config/connectdb.php'; // 1. เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ไม่ได้ระบุ ID ของสินค้า";
    header("Location: products.php");
    exit();
}

$id = intval($_GET['id']); // แปลง id เป็นตัวเลขเพื่อความปลอดภัย

// 2. ดึงข้อมูลสินค้าเดิมเพื่อมาแสดงในฟอร์ม
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// หากไม่พบสินค้า ให้กลับไปหน้าหลัก
if (!$product) {
    $_SESSION['error'] = "ไม่พบสินค้า ID: {$id}";
    header("Location: products.php");
    exit();
}

// 3. ส่วนของการอัปเดตข้อมูลเมื่อกดบันทึก
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // รับค่าจากฟอร์ม
    $category_id = $_POST['category_id'] ?? $product['category_id'];
    $title       = $_POST['title']       ?? $product['title'];
    $price       = $_POST['price']       ?? $product['price'];
    $stock       = $_POST['stock']       ?? $product['stock'];

    // สร้าง Slug ใหม่อีกครั้ง เผื่อมีการเปลี่ยนชื่อสินค้า
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    // ใช้ URL รูปภาพเดิมเป็นค่าเริ่มต้น
    $image_url = $product['image_url'];

    // 4. จัดการการอัปโหลดรูปภาพใหม่ (ถ้ามี)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        // ลบรูปภาพเก่าออกจาก Server ก่อน (ถ้ามี)
        // **จุดสำคัญ:** เราใช้ Path จาก DB โดยตรงในการหาไฟล์เพื่อลบ
        if (!empty($product['image_url']) && file_exists('../' . $product['image_url'])) {
            unlink('../' . $product['image_url']);
        }

        // เริ่มกระบวนการอัปโหลดรูปใหม่
        $targetDir    = "../uploads/";
        $fileName     = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        // **จุดสำคัญ:** บันทึก Path แบบเต็ม `uploads/filename.jpg` ลง DB เหมือนตอน Add
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image_url = "uploads/" . $fileName;
        }
    }

    // 5. อัปเดตข้อมูลลงฐานข้อมูล
    // **หมายเหตุ:** ผมตัดฟิลด์ description ออกไปเพราะไม่มีในตาราง `products` ของคุณ
    // แต่เพิ่ม slug กับ stock เข้ามาให้ครบถ้วน
    $update_stmt = $conn->prepare("UPDATE products SET category_id=?, title=?, slug=?, price=?, stock=?, image_url=? WHERE id=?");
    $update_stmt->bind_param("issdisi", $category_id, $title, $slug, $price, $stock, $image_url, $id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "อัปเดตข้อมูลสินค้า '$title' สำเร็จ!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดต: " . $update_stmt->error;
    }
    $update_stmt->close();

    header("Location: products.php");
    exit();
}

// 6. ดึงข้อมูลหมวดหมู่ทั้งหมดสำหรับ Dropdown
// **จุดสำคัญ:** แก้ไขชื่อคอลัมน์จาก name เป็น title ให้ตรงกับ DB
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");

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
    <div class="card shadow-lg border-0 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🛠️ แก้ไขสินค้า: <?= htmlspecialchars($product['title']) ?></h4>
        </div>
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="title" class="form-label">ชื่อสินค้า</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($product['title']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">ราคา</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label">จำนวนคงเหลือ (Stock)</label>
                        <input type="number" id="stock" name="stock" class="form-control" value="<?= htmlspecialchars($product['stock']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">หมวดหมู่</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['title']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">รูปภาพปัจจุบัน</label><br>
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="../<?= htmlspecialchars($product['image_url']) ?>" alt="product" width="150" class="rounded mb-2 border">
                    <?php else: ?>
                        <p class="text-muted">ไม่มีรูปภาพ</p>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">อัปโหลดรูปใหม่ (หากต้องการเปลี่ยน)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>