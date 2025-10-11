<?php
// admin/promotions.php (Upgraded with DataTables)
session_start();
include '../config/connectdb.php';
include 'header.php';

if (isset($_GET['delete'])) {
    $id_to_delete = intval($_GET['delete']);
    $stmt_delete = $conn->prepare("DELETE FROM promotions WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    $stmt_delete->execute();
    header("Location: promotions.php");
    exit();
}

$result = $conn->query("SELECT pr.*, p.title AS product_title FROM promotions pr LEFT JOIN products p ON pr.product_id = p.id ORDER BY pr.id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">✨ จัดการโปรโมชั่น</h1>
    <a href="add_promotion.php" class="btn btn-primary"><i class="fas fa-plus fa-sm me-2"></i>เพิ่มโปรโมชั่นใหม่</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <table id="promotionsTable" class="table table-bordered table-hover align-middle" style="width:100%">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>ชื่อโปรโมชั่น</th>
                    <th>ส่วนลด</th>
                    <th>ใช้ได้ถึง</th>
                    <th>สำหรับสินค้า</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= ($row['discount_type'] == 'percentage') ? $row['discount_value'] . '%' : '฿' . number_format($row['discount_value']); ?></td>
                    <td><?= date('d M Y', strtotime($row['end_date'])) ?></td>
                    <td><?= htmlspecialchars($row['product_title'] ?? 'ทุกชิ้นที่ร่วมรายการ') ?></td>
                    <td class="text-center">
                        <a href="edit_promotion.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> แก้ไข</a>
                        <a href="promotions.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')"><i class="fas fa-trash"></i> ลบ</a>
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
    $('#promotionsTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": { "search": "ค้นหา:", "lengthMenu": "แสดง _MENU_ รายการ", "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ", "paginate": { "previous": "ก่อนหน้า", "next": "ถัดไป" } }
    });
});
</script>