<?php
// admin/edit_product.php (Corrected & Final Version)
session_start();
include '../config/connectdb.php';

// --- 1. ตรวจสอบ ID สินค้าที่ส่งมา ---
$id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_to_edit === 0) {
    die("ไม่ได้ระบุ ID สินค้า");
}

// --- 2. ส่วนของการอัปเดตข้อมูล (เมื่อมีการกดบันทึก) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        // อัปเดตข้อมูล Text พื้นฐาน
        $title = $_POST['title'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id'];
        $description = $_POST['description'] ?? null;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        if ($description !== null) {
            $stmt_update_prod = $conn->prepare("UPDATE products SET title=?, slug=?, price=?, stock=?, category_id=?, description=? WHERE id=?");
            $stmt_update_prod->bind_param("ssdiisi", $title, $slug, $price, $stock, $category_id, $description, $id_to_edit);
        } else {
            $stmt_update_prod = $conn->prepare("UPDATE products SET title=?, slug=?, price=?, stock=?, category_id=? WHERE id=?");
            $stmt_update_prod->bind_param("ssdiii", $title, $slug, $price, $stock, $category_id, $id_to_edit);
        }
        $stmt_update_prod->execute();
        $stmt_update_prod->close();

        // จัดการการลบรูปภาพที่ถูกเลือก
        if (!empty($_POST['delete_images']) && is_array($_POST['delete_images'])) {
            $images_to_delete = $_POST['delete_images'];
            $placeholders = implode(',', array_fill(0, count($images_to_delete), '?'));
            $types = str_repeat('i', count($images_to_delete));

            // ดึง URL ของไฟล์ที่จะลบก่อน
            $result_urls = $conn->execute_query("SELECT image_url FROM product_images WHERE id IN ($placeholders)", $images_to_delete);
            while($row = $result_urls->fetch_assoc()) {
                if (file_exists('../' . $row['image_url'])) {
                    unlink('../' . $row['image_url']);
                }
            }
            
            // ลบข้อมูลออกจากฐานข้อมูล
            $stmt_delete = $conn->prepare("DELETE FROM product_images WHERE id IN ($placeholders)");
            $stmt_delete->bind_param($types, ...$images_to_delete);
            $stmt_delete->execute();
            $stmt_delete->close();
        }

        // จัดการการอัปโหลดรูปภาพใหม่
        if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
            $stmt_images = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
            foreach ($_FILES['new_images']['name'] as $i => $name) {
                if ($_FILES['new_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $targetDir = "../uploads/";
                    $fileName = time() . "_" . basename($name);
                    $targetFilePath = $targetDir . $fileName;
                    if (move_uploaded_file($_FILES["new_images"]["tmp_name"][$i], $targetFilePath)) {
                        $image_path = "uploads/" . $fileName;
                        $stmt_images->bind_param("is", $id_to_edit, $image_path);
                        $stmt_images->execute();
                    }
                }
            }
            $stmt_images->close();
        }

        // อัปเดตรูปภาพหลัก (Main Image) ให้เป็นรูปแรกในแกลเลอรีเสมอ
        $result_first_img = $conn->query("SELECT image_url FROM product_images WHERE product_id = $id_to_edit ORDER BY id ASC LIMIT 1");
        $new_main_image = ($result_first_img->num_rows > 0) ? $result_first_img->fetch_assoc()['image_url'] : null;
        
        $stmt_update_main_img = $conn->prepare("UPDATE products SET image_url = ? WHERE id = ?");
        $stmt_update_main_img->bind_param("si", $new_main_image, $id_to_edit);
        $stmt_update_main_img->execute();
        $stmt_update_main_img->close();

        $conn->commit();
        $_SESSION['success'] = "แก้ไขสินค้า '$title' สำเร็จ!";
        header("Location: products.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $error = "เกิดข้อผิดพลาด: " . $exception->getMessage();
    }
}

// --- 3. ดึงข้อมูลเดิมมาแสดง (ย้าย Logic มาไว้ด้วยกัน) ---
$stmt_select_prod = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt_select_prod->bind_param("i", $id_to_edit);
$stmt_select_prod->execute();
$product = $stmt_select_prod->get_result()->fetch_assoc();
$stmt_select_prod->close();

if (!$product) {
    die("ไม่พบสินค้า ID: $id_to_edit");
}

// ดึงรูปภาพในแกลเลอรี
$gallery_result = $conn->query("SELECT * FROM product_images WHERE product_id = $id_to_edit ORDER BY id ASC");

// ดึงหมวดหมู่ทั้งหมด
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");

// ⭐️⭐️⭐️ เรียก header.php หลังจากดึงข้อมูลทั้งหมดเสร็จแล้ว ⭐️⭐️⭐️
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">✏️ แก้ไขสินค้า: <?= htmlspecialchars($product['title']) ?></h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="edit_product.php?id=<?= $id_to_edit ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                 <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">ชื่อสินค้า</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">หมวดหมู่</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <?php while($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">ราคา</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">สต็อก</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบายสินค้า</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>
            
            <hr class="my-4">
            
            <div class="mb-4">
                <h5 class="mb-3">จัดการรูปภาพ</h5>
                <div class="row g-3">
                    <?php if ($gallery_result->num_rows > 0): ?>
                        <?php while($img = $gallery_result->fetch_assoc()): ?>
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="position-relative">
                                <img src="../<?= htmlspecialchars($img['image_url']) ?>" class="img-thumbnail w-100" style="aspect-ratio: 1/1; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 p-1">
                                    <input class="form-check-input bg-danger border-danger" type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" title="เลือกลบ">
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีรูปภาพสำหรับสินค้านี้</p>
                    <?php endif; ?>
                </div>
                <?php if ($gallery_result->num_rows > 0): ?>
                    <small class="form-text text-muted mt-2 d-block">ติ๊กที่ช่องสี่เหลี่ยมบนรูปภาพที่ต้องการลบ แล้วกด "บันทึกการแก้ไข"</small>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="new_images" class="form-label">เพิ่มรูปภาพใหม่</label>
                <input class="form-control" type="file" id="new_images" name="new_images[]" multiple accept="image/*">
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>