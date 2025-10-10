<?php
include 'header.php';

// ✅ ดึงหมวดหมู่ (สำหรับ dropdown)
$categories = [];
$res_cat = $conn->query("SELECT * FROM categories ORDER BY title ASC");
while($row_cat = $res_cat->fetch_assoc()){
    $categories[] = $row_cat;
}

// ✅ ดึงสินค้าทั้งหมด พร้อมชื่อหมวดหมู่
$products = [];
$res_prod = $conn->query("
    SELECT p.*, c.title AS category_title 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
while($row_prod = $res_prod->fetch_assoc()){
    $products[] = $row_prod;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">จัดการสินค้า</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="fas fa-plus fa-sm me-2"></i>เพิ่มสินค้าใหม่
    </button>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายการสินค้า</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="productTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>รูป</th>
                        <th>ชื่อสินค้า</th>
                        <th>หมวดหมู่</th>
                        <th>ราคา (บาท)</th>
                        <th>จำนวน</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?= $prod['id'] ?></td>
                        <td>
                            <?php 
                                $img_path = !empty($prod['image_url']) ? "../" . htmlspecialchars($prod['image_url']) : "../uploads/no-image.jpg";
                            ?>
                            <img src="<?= $img_path ?>" 
                                 alt="<?= htmlspecialchars($prod['title']) ?>" 
                                 width="60" class="img-thumbnail rounded shadow-sm">
                        </td>
                        <td><?= htmlspecialchars($prod['title']) ?></td>
                        <td><?= htmlspecialchars($prod['category_title']) ?></td>
                        <td><?= number_format($prod['price'], 2) ?></td>
                        <td><?= $prod['stock'] ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm editBtn"
                                    data-id="<?= $prod['id'] ?>"
                                    data-title="<?= htmlspecialchars($prod['title']) ?>"
                                    data-category-id="<?= $prod['category_id'] ?>"
                                    data-price="<?= $prod['price'] ?>"
                                    data-stock="<?= $prod['stock'] ?>"
                                    data-image-url="../<?= htmlspecialchars($prod['image_url']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#editProductModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="delete_product.php?id=<?= $prod['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Product -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">เพิ่มสินค้าใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">หมวดหมู่</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ราคา</label>
                    <input type="number" name="price" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">จำนวนในสต็อก</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รูปสินค้า</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal: Edit Product -->
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="edit_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">แก้ไขสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">หมวดหมู่</label>
                    <select name="category_id" id="edit_category_id" class="form-select" required>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ราคา</label>
                    <input type="number" name="price" id="edit_price" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">จำนวนในสต็อก</label>
                    <input type="number" name="stock" id="edit_stock" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รูปสินค้าใหม่ (ถ้าต้องการเปลี่ยน)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <img id="edit_image_preview" src="" class="mt-2 img-thumbnail" width="100">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-warning">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#productTable').DataTable({
        "order": [[0, "desc"]]
    });

    // ✅ ดึงข้อมูลไปใส่ในฟอร์มแก้ไข
    $('.editBtn').click(function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_title').val($(this).data('title'));
        $('#edit_category_id').val($(this).data('category-id'));
        $('#edit_price').val($(this).data('price'));
        $('#edit_stock').val($(this).data('stock'));
        $('#edit_image_preview').attr('src', $(this).data('image-url'));
    });
});
</script>