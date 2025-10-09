<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/connectdb.php';

// ดึงหมวดหมู่
$categories = [];
$res = $conn->query("SELECT * FROM categories");
while($row = $res->fetch_assoc()){
    $categories[] = $row;
}

// ดึงสินค้า
$products = [];
$res = $conn->query("SELECT p.*, c.title AS category FROM products p 
                     LEFT JOIN categories c ON p.category_id=c.id");
while($row = $res->fetch_assoc()){
    $products[] = $row;
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสินค้า</title>
<?php include 'layout.php'; ?>
</head>
<body>
<div class="d-flex">
    <div class="sidebar p-3">
        <h4>Admin Panel</h4>
        <a href="index.php">แดชบอร์ด</a>
        <a href="products.php" class="active">สินค้า</a>
        <a href="users.php">ผู้ใช้</a>
        <a href="orders.php">คำสั่งซื้อ</a>
        <a href="adminout.php" class="text-danger">ออก</a>
    </div>
    <div class="content flex-grow-1">
        <h2>สินค้า</h2>

        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">เพิ่มสินค้า</button>

        <table id="productTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>รูป</th>
                    <th>ชื่อสินค้า</th>
                    <th>หมวดหมู่</th>
                    <th>ราคา</th>
                    <th>จำนวน</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($products as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <?php if(!empty($row['image_url'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['image_url']) ?>" width="60" height="60">
                        <?php else: ?>-
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= number_format($row['price'],2) ?></td>
                    <td><?= $row['stock'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning editBtn"
                                data-id="<?= $row['id'] ?>"
                                data-title="<?= htmlspecialchars($row['title']) ?>"
                                data-category="<?= $row['category_id'] ?>"
                                data-price="<?= $row['price'] ?>"
                                data-stock="<?= $row['stock'] ?>"
                                data-image="<?= $row['image_url'] ?>"
                                data-bs-toggle="modal" data-bs-target="#editModal">แก้ไข</button>
                        <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('คุณต้องการลบสินค้านี้จริงหรือไม่?')">ลบ</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="add_product.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">เพิ่มสินค้า</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label>ชื่อสินค้า</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>หมวดหมู่</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= $cat['title'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>ราคา</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>จำนวน</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>รูปสินค้า</label>
            <input type="file" name="image" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">บันทึก</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="edit_product.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-header">
        <h5 class="modal-title">แก้ไขสินค้า</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label>ชื่อสินค้า</label>
            <input type="text" name="title" id="edit_title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>หมวดหมู่</label>
            <select name="category_id" id="edit_category" class="form-control" required>
                <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= $cat['title'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>ราคา</label>
            <input type="number" name="price" id="edit_price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>จำนวน</label>
            <input type="number" name="stock" id="edit_stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>รูปสินค้า</label>
            <input type="file" name="image" class="form-control">
            <img id="edit_image_preview" src="" class="mt-2" width="100" height="100">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">บันทึก</button>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function(){
    $('#productTable').DataTable();

    // กรณีเปิด Edit Modal
    $('.editBtn').click(function(){
        $('#edit_id').val($(this).data('id'));
        $('#edit_title').val($(this).data('title'));
        $('#edit_category').val($(this).data('category'));
        $('#edit_price').val($(this).data('price'));
        $('#edit_stock').val($(this).data('stock'));

        let img = $(this).data('image');
        $('#edit_image_preview').attr('src', img ? '../uploads/'+img : '');
    });
});
</script>
</body>
</html>
