<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/connectdb.php';
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการสินค้า</title>
  <?php include 'layout.php'; ?>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3">
    <h4>Admin Panel</h4>
    <a href="index.php">แดชบอร์ด</a>
    <a href="users.php">ผู้ใช้</a>
    <a href="products.php" class="active">สินค้า</a>
    <a href="orders.php">คำสั่งซื้อ</a>
    <a href="adminout.php" class="text-danger">ออกจากระบบ</a>
  </div>

  <div class="content flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>จัดการสินค้า</h3>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">+ เพิ่มสินค้า</button>
    </div>

    <table id="productTable" class="table table-striped table-bordered">
      <thead>
        <tr class="text-center">
          <th>ID</th>
          <th>รูปสินค้า</th>
          <th>ชื่อสินค้า</th>
          <th>หมวดหมู่</th>
          <th>ราคา</th>
          <th>สต็อก</th>
          <th>วันที่เพิ่ม</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT p.*, c.title AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td class="text-center">
            <?php if(!empty($row['image'])): ?>
              <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" width="50" height="50" style="object-fit:cover;">
            <?php else: ?>-
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
          <td><?= number_format($row['price'],2) ?> ฿</td>
          <td><?= $row['stock'] ?></td>
          <td><?= $row['created_at'] ?></td>
          <td class="text-center">
            <button class="btn btn-warning btn-sm editBtn"
              data-id="<?= $row['id'] ?>"
              data-title="<?= htmlspecialchars($row['title']) ?>"
              data-price="<?= $row['price'] ?>"
              data-stock="<?= $row['stock'] ?>"
              data-category="<?= $row['category_id'] ?>"
              data-image="<?= $row['image'] ?>"
            >แก้ไข</button>
            <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบสินค้านี้?');">ลบ</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" action="add_product.php" method="POST" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">เพิ่มสินค้าใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>ชื่อสินค้า</label><input type="text" name="title" class="form-control" required></div>
        <div class="mb-3">
          <label>หมวดหมู่</label>
          <select name="category_id" class="form-select" required>
            <option value="">-- เลือกหมวดหมู่ --</option>
            <?php
            $cats = $conn->query("SELECT * FROM categories");
            while ($c = $cats->fetch_assoc()) echo "<option value='{$c['id']}'>{$c['title']}</option>";
            ?>
          </select>
        </div>
        <div class="mb-3"><label>ราคา</label><input type="number" name="price" step="0.01" class="form-control" required></div>
        <div class="mb-3"><label>สต็อก</label><input type="number" name="stock" class="form-control" required></div>
        <div class="mb-3"><label>รูปสินค้า</label><input type="file" name="image" class="form-control" accept="image/*"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary" type="submit">บันทึก</button></div>
    </form>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" action="edit_product.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-header">
        <h5 class="modal-title">แก้ไขสินค้า</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>ชื่อสินค้า</label><input type="text" name="title" id="edit_title" class="form-control" required></div>
        <div class="mb-3">
          <label>หมวดหมู่</label>
          <select name="category_id" id="edit_category" class="form-select" required>
            <option value="">-- เลือกหมวดหมู่ --</option>
            <?php
            $cats = $conn->query("SELECT * FROM categories");
            while ($c = $cats->fetch_assoc()) echo "<option value='{$c['id']}'>{$c['title']}</option>";
            ?>
          </select>
        </div>
        <div class="mb-3"><label>ราคา</label><input type="number" name="price" step="0.01" id="edit_price" class="form-control" required></div>
        <div class="mb-3"><label>สต็อก</label><input type="number" name="stock" id="edit_stock" class="form-control" required></div>
        <div class="mb-3">
          <label>รูปสินค้า (เว้นว่างถ้าไม่เปลี่ยน)</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <div class="mt-2"><img id="edit_image_preview" src="" width="100" style="object-fit:cover;"></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary" type="submit">อัปเดต</button></div>
    </form>
  </div>
</div>

<script>
$(document).ready(function(){
  $('#productTable').DataTable({language: {url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json"}});
  $('.editBtn').click(function(){
    $('#edit_id').val($(this).data('id'));
    $('#edit_title').val($(this).data('title'));
    $('#edit_price').val($(this).data('price'));
    $('#edit_stock').val($(this).data('stock'));
    $('#edit_category').val($(this).data('category'));
    let img = $(this).data('image');
    $('#edit_image_preview').attr('src', img ? '../uploads/'+img : '');
    $('#editModal').modal('show');
  });
});
</script>
</body>
</html>
