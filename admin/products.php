<?php
// 1. เรียกใช้ Header ซึ่งมีการเชื่อมต่อ DB, Session และ CSS ทั้งหมดอยู่แล้ว
include 'header.php';

// 2. ดึงข้อมูลหมวดหมู่สินค้าทั้งหมดสำหรับใช้ในฟอร์มแก้ไข
$categories = [];
$categories_result = $conn->query("SELECT id, title FROM categories ORDER BY title ASC");
if ($categories_result) {
    while($row = $categories_result->fetch_assoc()){
        $categories[] = $row;
    }
}

// 3. ดึงข้อมูลสินค้าทั้งหมดพร้อมชื่อหมวดหมู่ (ใช้ LEFT JOIN เพื่อป้องกันกรณีสินค้าไม่มีหมวดหมู่)
$products = [];
$products_sql = "
    SELECT p.*, c.title AS category_title 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
";
$products_result = $conn->query($products_sql);
if ($products_result) {
    while($row = $products_result->fetch_assoc()){
        $products[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">จัดการสินค้า</h1>
    <a href="add_product.php" class="btn btn-success shadow-sm">
        <i class="fas fa-plus fa-sm me-2"></i>เพิ่มสินค้าใหม่
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>สำเร็จ!</strong> <?= htmlspecialchars($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); // เคลียร์ session ทิ้งหลังแสดงผล ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>เกิดข้อผิดพลาด!</strong> <?= htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); // เคลียร์ session ทิ้งหลังแสดงผล ?>
<?php endif; ?>


<div class="card shadow-sm mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายการสินค้าในคลัง</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="productTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#ID</th>
                        <th class="text-center">รูป</th>
                        <th>ชื่อสินค้า</th>
                        <th>หมวดหมู่</th>
                        <th class="text-end">ราคา (บาท)</th>
                        <th class="text-center">คงเหลือ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $prod): ?>
                        <tr>
                            <td class="text-center align-middle"><?= $prod['id'] ?></td>
                            <td class="text-center">
                                <?php 
                                    // กำหนด path รูปภาพพร้อมมีรูปสำรอง
                                    $image_path = !empty($prod['image_url']) && file_exists("../" . $prod['image_url'])
                                                  ? "../" . htmlspecialchars($prod['image_url'])
                                                  : "../uploads/no-image.jpg"; // ควรมีไฟล์รูปนี้ไว้ในระบบ
                                ?>
                                <img src="<?= $image_path ?>" 
                                     alt="<?= htmlspecialchars($prod['title']) ?>" 
                                     width="60" class="img-thumbnail rounded shadow-sm">
                            </td>
                            <td class="align-middle"><?= htmlspecialchars($prod['title']) ?></td>
                            <td class="align-middle"><?= htmlspecialchars($prod['category_title'] ?? 'ไม่มีหมวดหมู่') ?></td>
                            <td class="text-end align-middle"><?= number_format($prod['price'], 2) ?></td>
                            <td class="text-center align-middle"><?= $prod['stock'] ?></td>
                            <td class="text-center align-middle">
                                <button class="btn btn-warning btn-sm editBtn"
                                        data-id="<?= $prod['id'] ?>"
                                        data-title="<?= htmlspecialchars($prod['title']) ?>"
                                        data-category-id="<?= $prod['category_id'] ?>"
                                        data-price="<?= $prod['price'] ?>"
                                        data-stock="<?= $prod['stock'] ?>"
                                        data-image-url="<?= $image_path ?>"
                                        data-bs-toggle="modal" data-bs-target="#editProductModal" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="delete_product.php?id=<?= $prod['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้? การกระทำนี้ไม่สามารถย้อนกลับได้')" title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">ยังไม่มีสินค้าในระบบ</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="edit_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editProductModalLabel"><i class="fas fa-edit me-2"></i>แก้ไขข้อมูลสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <img id="edit_image_preview" src="" class="img-thumbnail" width="120">
                </div>
                <div class="mb-3">
                    <label for="edit_title" class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit_category_id" class="form-label">หมวดหมู่</label>
                    <select name="category_id" id="edit_category_id" class="form-select" required>
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="edit_price" class="form-label">ราคา</label>
                        <input type="number" name="price" id="edit_price" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_stock" class="form-label">จำนวนในสต็อก</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="edit_image" class="form-label">รูปสินค้าใหม่ (ถ้าต้องการเปลี่ยน)</label>
                    <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-warning">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
// รอให้เอกสาร (HTML) โหลดเสร็จก่อน แล้วค่อยเริ่มทำงานกับ JavaScript
$(document).ready(function() {
    
    // 1. สั่งให้ตารางของเรากลายเป็น DataTable ที่มีฟังก์ชันค้นหาและเรียงลำดับ
    $('#productTable').DataTable({
        "order": [[0, "desc"]], // เรียงจาก ID (คอลัมน์ที่ 0) มากไปน้อยเป็นค่าเริ่มต้น
        "language": { // ทำให้เมนูต่างๆ เป็นภาษาไทย
            "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/th.json"
        }
    });

    // 2. เมื่อมีการคลิกปุ่มที่มี class 'editBtn'
    $('.editBtn').click(function() {
        // ดึงข้อมูลจาก attribute data-* ของปุ่มที่ถูกคลิก
        var id = $(this).data('id');
        var title = $(this).data('title');
        var categoryId = $(this).data('category-id');
        var price = $(this).data('price');
        var stock = $(this).data('stock');
        var imageUrl = $(this).data('image-url');

        // นำข้อมูลไปใส่ในฟอร์มของ Modal แก้ไข
        $('#edit_id').val(id);
        $('#edit_title').val(title);
        $('#edit_category_id').val(categoryId);
        $('#edit_price').val(price);
        $('#edit_stock').val(stock);
        $('#edit_image_preview').attr('src', imageUrl);
    });
});
</script>

<?php
// 4. เรียกใช้ Footer ซึ่งมี JS Libraries ทั้งหมดและปิด Tag HTML
include 'footer.php'; 
?>