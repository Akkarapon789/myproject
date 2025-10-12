<?php
// admin/add_product.php (Upgraded for Multiple Images)
session_start();
include '../config/connectdb.php';

// --- ส่วนของการบันทึกข้อมูล ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction(); // เริ่ม Transaction
    try {
        $category_id = $_POST['category_id'];
        $title = $_POST['title'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'] ?? '';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $main_image_url = null; // เตรียมตัวแปรสำหรับรูปภาพหลัก

        // ⭐️ 1. จัดการการอัปโหลดรูปภาพ (แบบหลายไฟล์) ⭐️
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $file_count = count($_FILES['images']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $targetDir = "../uploads/";
                    $fileName = time() . "_" . basename($_FILES["images"]["name"][$i]);
                    $targetFilePath = $targetDir . $fileName;

                    if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $targetFilePath)) {
                        $image_path = "uploads/" . $fileName;
                        
                        // ⭐️ 2. กำหนดให้รูปแรกเป็นรูปภาพหลัก (Main Image) ⭐️
                        if ($i === 0) {
                            $main_image_url = $image_path;
                        }
                        // เก็บ Path ของรูปภาพอื่นๆ ไว้ใน array เพื่อบันทึกทีหลัง
                        $other_image_paths[] = $image_path;
                    }
                }
            }
        }

        // 3. บันทึกข้อมูลสินค้าหลักลงตาราง `products` พร้อมรูปภาพหลัก
        $stmt_product = $conn->prepare("INSERT INTO products (category_id, title, slug, price, stock, image_url, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_product->bind_param("issdis", $category_id, $title, $slug, $price, $stock, $main_image_url, $description);
        $stmt_product->execute();
        
        // 4. ดึง ID ของสินค้าล่าสุดที่เพิ่งสร้าง
        $last_product_id = $conn->insert_id;
        $stmt_product->close();

        // ⭐️ 5. วนลูปบันทึกรูปภาพเพิ่มเติมลงตาราง `product_images` ⭐️
        if (!empty($other_image_paths)) {
            $stmt_images = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
            foreach ($other_image_paths as $path) {
                $stmt_images->bind_param("is", $last_product_id, $path);
                $stmt_images->execute();
            }
            $stmt_images->close();
        }

        $conn->commit(); // ยืนยันการทำรายการทั้งหมด
        $_SESSION['success'] = "เพิ่มสินค้า '$title' สำเร็จ!";
        header("Location: products.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // ยกเลิกการทำรายการทั้งหมดหากมีข้อผิดพลาด
        $error = "เกิดข้อผิดพลาด: " . $exception->getMessage();
    }
}
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");
include 'header.php';
?>

<h1 class="h3 mb-4 text-gray-800">➕ เพิ่มสินค้าใหม่</h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
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