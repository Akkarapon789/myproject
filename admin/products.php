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
                            <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger delete-btn">ลบ</a>
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
    // โค้ด DataTables เดิม ไม่ต้องแก้ไข
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

    // === เพิ่มส่วนนี้เข้าไป ===
    // สำหรับจัดการการคลิกปุ่มลบที่มี class="delete-btn"
    $('.delete-btn').on('click', function(e) {
        e.preventDefault(); // หยุดการทำงานของลิงก์ปกติ
        const deleteUrl = $(this).attr('href'); // ดึง URL ที่จะไป

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณจะไม่สามารถกู้คืนข้อมูลสินค้านี้ได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ถ้าผู้ใช้กดยืนยัน ให้ไปที่ URL สำหรับลบ
                window.location.href = deleteUrl;
            }
        })
    });
});
</script>