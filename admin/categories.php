<?php
// admin/categories.php (Upgraded with DataTables)
session_start();
include '../config/connectdb.php';
include 'header.php';

if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);
    // (ควรมีโค้ดลบรูปภาพจริงจาก server ที่นี่ด้วย)
    $stmt_delete = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    $stmt_delete->execute();
    header("Location: categories.php");
    exit();
}

$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">🏷️ จัดการหมวดหมู่สินค้า</h1>
    <a href="add_category.php" class="btn btn-primary"><i class="fas fa-plus fa-sm me-2"></i>เพิ่มหมวดหมู่ใหม่</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <table id="categoriesTable" class="table table-bordered table-hover align-middle" style="width:100%">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>รูปภาพ</th>
                    <th>ชื่อหมวดหมู่</th>
                    <th>คำอธิบาย</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="text-center"><img src="../<?= htmlspecialchars($row['image_url'] ?? 'assets/default.jpg') ?>" width="100" class="img-thumbnail"></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td class="text-center">
                        <a href="edit_category.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> แก้ไข</a>
                        <a href="categories.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')"><i class="fas fa-trash"></i> ลบ</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#categoriesTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": { "search": "ค้นหา:", "lengthMenu": "แสดง _MENU_ รายการ", "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ", "paginate": { "previous": "ก่อนหน้า", "next": "ถัดไป" } }
    });
});
</script>