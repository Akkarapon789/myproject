<?php
// admin/add_product.php (Corrected & Final Version)
session_start();
include '../config/connectdb.php';

// --- ส่วนของการบันทึกข้อมูล (เมื่อกดปุ่มบันทึก) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        $category_id = $_POST['category_id'];
        $title = $_POST['title'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'] ?? '';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $main_image_url = null;
        $other_image_paths = [];

        // 1. จัดการการอัปโหลดรูปภาพ
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $i => $name) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $targetDir = "../uploads/";
                    $fileName = time() . "_" . basename($name);
                    $targetFilePath = $targetDir . $fileName;

                    if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $targetFilePath)) {
                        $image_path = "uploads/" . $fileName;
                        if ($main_image_url === null) {
                            $main_image_url = $image_path; // รูปแรกเป็นรูปหลัก
                        }
                        $other_image_paths[] = $image_path; // เก็บทุกรูป (รวมรูปหลัก)
                    }
                }
            }
        }

        // 2. บันทึกข้อมูลสินค้าหลัก
        $stmt_product = $conn->prepare("INSERT INTO products (category_id, title, slug, price, stock, image_url, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_product->bind_param("issdiss", $category_id, $title, $slug, $price, $stock, $main_image_url, $description);
        $stmt_product->execute();
        $last_product_id = $conn->insert_id;
        $stmt_product->close();

        // 3. บันทึกรูปภาพเพิ่มเติม
        if (!empty($other_image_paths)) {
            $stmt_images = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
            foreach ($other_image_paths as $path) {
                $stmt_images->bind_param("is", $last_product_id, $path);
                $stmt_images->execute();
            }
            $stmt_images->close();
        }

        $conn->commit();
        $_SESSION['success'] = "เพิ่มสินค้า '$title' สำเร็จ!";
        header("Location: products.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $error = "เกิดข้อผิดพลาด: " . $exception->getMessage();
    }
}

// ⭐️ ดึงข้อมูลหมวดหมู่สำหรับ Dropdown (ต้องอยู่ก่อน include header) ⭐️
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");

// เรียกใช้ header หลังจากเตรียมข้อมูลเสร็จ
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">➕ เพิ่มสินค้าใหม่</h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">ชื่อสินค้า</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">หมวดหมู่</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="" disabled selected>-- กรุณาเลือก --</option>
                        <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                            <?php while($cat = $categories_result->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">ราคา</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">จำนวนคงเหลือ (สต็อก)</label>
                    <input type="number" class="form-control" id="stock" name="stock" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบายสินค้า</label>
                <textarea class="form-control" id="description" name="description" rows="5"></textarea>
            </div>
            <div class="mb-3">
                <label for="images" class="form-label">รูปภาพสินค้า (เลือกได้หลายรูป)</label>
                <input class="form-control" type="file" id="images" name="images[]" multiple accept="image/*">
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>