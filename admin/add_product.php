<?php
// admin/add_product.php (Detective Mode)

// ⭐️⭐️⭐️ 1. เปิดไฟฉายส่องหา Error ทั้งหมด ⭐️⭐️⭐️
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// เริ่ม Session และเรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/connectdb.php';

// --- ส่วนของการบันทึกข้อมูล (เมื่อกดปุ่มบันทึก) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        // ... (โค้ดส่วนบันทึกข้อมูลเหมือนเดิม) ...

        $conn->commit();
        $_SESSION['success'] = "เพิ่มสินค้าสำเร็จ!";
        header("Location: products.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        // ถ้าเกิด Error ตอนบันทึก ให้เก็บข้อความไว้
        $error = "เกิดข้อผิดพลาดในการบันทึก: " . $exception->getMessage();
    }
}

// --- ดึงข้อมูลหมวดหมู่สำหรับ Dropdown ---
$categories_result = $conn->query("SELECT * FROM categories ORDER BY title ASC");

// ⭐️⭐️⭐️ 2. ตรวจสอบว่าดึงหมวดหมู่มาได้จริงหรือไม่ (สำหรับ Debug) ⭐️⭐️⭐️
if (!$categories_result) {
    // ถ้า query ล้มเหลว ให้หยุดและแสดง Error ของฐานข้อมูลทันที
    die("Error fetching categories: " . $conn->error);
}

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
                        <?php
                        // ตรวจสอบอีกครั้งก่อนวนลูป
                        if ($categories_result->num_rows > 0) {
                            while($cat = $categories_result->fetch_assoc()) {
                                echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['title']) . '</option>';
                            }
                        }
                        ?>
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
                <label for="images" class="form-label">รูปภาพสินค้า (เลือกได้หลายรูป, รูปแรกคือรูปปก)</label>
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