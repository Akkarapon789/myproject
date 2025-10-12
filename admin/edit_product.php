<?php
// admin/edit_product.php (Upgraded for Multiple Images)
session_start();
include '../config/connectdb.php';

$id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_to_edit === 0) {
    die("ไม่ได้ระบุ ID สินค้า");
}

// --- ส่วนของการอัปเดตข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        // 1. อัปเดตข้อมูล Text พื้นฐาน
        $title = $_POST['title'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id'];
        $description = $_POST['description'] ?? '';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        $stmt_update_prod = $conn->prepare("UPDATE products SET title=?, slug=?, price=?, stock=?, category_id=?, description=? WHERE id=?");
        $stmt_update_prod->bind_param("ssdiisi", $title, $slug, $price, $stock, $category_id, $description, $id_to_edit);
        $stmt_update_prod->execute();
        $stmt_update_prod->close();

        // 2. จัดการการลบรูปภาพที่ถูกเลือก
        if (!empty($_POST['delete_images'])) {
            $images_to_delete = $_POST['delete_images'];
            $delete_ids = implode(',', array_map('intval', $images_to_delete)); // '1,2,3'

            // ดึง URL ของไฟล์ที่จะลบก่อน
            $result_urls = $conn->query("SELECT image_url FROM product_images WHERE id IN ($delete_ids)");
            while($row = $result_urls->fetch_assoc()) {
                if (file_exists('../' . $row['image_url'])) {
                    unlink('../' . $row['image_url']); // ลบไฟล์จริง
                }
            }
            
            // ลบข้อมูลออกจากฐานข้อมูล
            $conn->query("DELETE FROM product_images WHERE id IN ($delete_ids)");
        }

        // 3. จัดการการอัปโหลดรูปภาพใหม่
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

        // 4. ⭐️ อัปเดตรูปภาพหลัก (Main Image) ให้เป็นรูปแรกในแกลเลอรีเสมอ ⭐️
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

// --- ดึงข้อมูลเดิมมาแสดงในฟอร์ม ---
$stmt_select_prod = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt_select_prod->bind_param("i", $id_to_edit);
$stmt_select_prod->execute();
$product = $stmt_select_prod->get_result()->fetch_assoc();
$stmt_select_prod->close();

// ดึงรูปภาพในแกลเลอรี
$gallery_result = $conn->query("SELECT * FROM product_images WHERE product_id = $id_to_edit ORDER BY id ASC");

// ดึงหมวดหมู่ทั้งหมด
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");

if (!$product) die("ไม่พบสินค้า");
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">✏️ แก้ไขสินค้า: <?= htmlspecialchars($product['title']) ?></h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="edit_product.php?id=<?= $id_to_edit ?>" method="POST" enctype="multipart/form-data">
            <hr class="my-4">
            
            <div class="mb-4">
                <h5 class="mb-3">จัดการรูปภาพ</h5>
                <div class="row g-3">
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
                </div>
                <?php if ($gallery_result->num_rows > 0): ?>
                    <small class="form-text text-muted">ติ๊กที่ช่องสี่เหลี่ยมบนรูปภาพที่ต้องการลบ แล้วกด "บันทึกการแก้ไข"</small>
                <?php else: ?>
                    <p class="text-muted">ยังไม่มีรูปภาพสำหรับสินค้านี้</p>
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