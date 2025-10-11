<?php
// admin/products.php (Corrected for DataTables)
session_start();
include '../config/connectdb.php';
include 'header.php'; // ⭐️ ต้องเรียก header.php ก่อนเสมอ ⭐️

// โค้ด PHP สำหรับดึงข้อมูล (ส่วนนี้ถูกต้องแล้ว)
$sql = "SELECT p.*, c.title AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📦 จัดการสินค้า</h3>
    <a href="add_product.php" class="btn btn-primary">➕ เพิ่มสินค้าใหม่</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table id="productsTable" class="table table-striped table-bordered align-middle" style="width:100%">
            <thead class="table-primary">
                <tr class="text-center">
                    <th>#</th>
                    <th>รูปภาพ</th>
                    <th>ชื่อสินค้า</th>
                    <th>ราคา</th>
                    <th>หมวดหมู่</th>
                    <th>สต็อก</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><?= $row['id']; ?></td>
                        <td class="text-center">
                            <?php if (!empty($row['image_url'])): ?>
                                <img src="../<?= htmlspecialchars($row['image_url']); ?>" width="80" class="rounded">
                            <?php else: ?>
                                <span class="text-muted">ไม่มีภาพ</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td class="text-end"><?= number_format($row['price'], 2); ?> ฿</td>
                        <td><?= htmlspecialchars($row['category_name']); ?></td>
                        <td class="text-center"><?= $row['stock']; ?></td>
                        <td class="text-center">
                            <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                            <a href="products.php?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบสินค้าชิ้นนี้?')">ลบ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
include 'footer.php'; // ⭐️ ต้องเรียก footer.php เพื่อโหลด JS ⭐️
?>

<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoEmpty": "ไม่พบข้อมูล",
            "zeroRecords": "ไม่พบข้อมูลที่ตรงกับการค้นหา",
            "paginate": { "previous": "ก่อนหน้า", "next": "ถัดไป" }
        }
    });
});
</script>